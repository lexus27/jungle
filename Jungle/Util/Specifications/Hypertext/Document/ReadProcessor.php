<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.10.2016
 * Time: 19:17
 */
namespace Jungle\Util\Specifications\Hypertext\Document {

	use Jungle\Util\Communication\Connection\Exception\ConnectionClosed;
	use Jungle\Util\Communication\Connection\Stream\Memory;
	use Jungle\Util\Communication\Connection\StreamInteractionInterface;
	use Jungle\Util\Communication\Connection\StreamInterface;
	use Jungle\Util\Specifications\Hypertext\Document;
	use Jungle\Util\Specifications\Hypertext\DocumentInterface;
	use Jungle\Util\Specifications\Hypertext\Header;

	/**
	 * Class ReadProcessor
	 * @package Jungle\Util\Specifications\Hypertext\Document
	 */
	class ReadProcessor extends Processor{

		protected static $default;

		/** @var  bool */
		protected $process_headers = true;

		/**
		 * @param ReadProcessor $readProcessor
		 */
		public static function setDefault(ReadProcessor $readProcessor){
			self::$default = $readProcessor;
		}

		/**
		 * @return ReadProcessor
		 */
		public static function getDefault(){
			if(!self::$default){
				self::$default = new self();
			}
			return self::$default;
		}

		/**
		 * @param null|string|StreamInteractionInterface|StreamInterface $source
		 * @return DocumentInterface
		 * @throws \Exception
		 */
		public function process($source = null){
			$this->reset();

			$generated = false;
			if(is_string($source)){
				$source = new Memory($source);
				$generated = true;
			}
			$this->source = $source;
			if(!$this->source) throw new \Exception('Source not present for reading');

			if(!$this->document){
				$this->document = new Document();
			}else{
				$this->document->setContent(null);
			}

			$this->sourceBeforeProcess($generated);
			$this->beforeProcess();


			$this->beforeHeaders();
			$this->headers();
			$this->afterHeaders();

			$this->beforeContents();
			$contents = $this->contents();
			if($contents){
				$contents = $this->decodeContents($contents);
				$this->handleContents($contents);
			}else{
				$this->handleContents(null);
			}
			$this->afterContents();

			$this->afterProcess();
			$this->sourceAfterProcess($generated);

			$this->completed = true;
			return $this->document;
		}

		/**
		 *
		 */
		protected function beforeProcess(){
			$this->document->beforeRead($this);
		}

		/**
		 * @param $data
		 * @param $i
		 */
		protected function handleLine($data, $i){
			$data = rtrim($data,"\r\n");
			if($data){
				if($this->beforeHeaderSet($data,$i)!==false){
					if($data = Header::parseHeaderRow($data)){
						$this->document->setHeader($data[0],$data[1],false);
					}
				}
			}else{
				$this->process_headers = false;
			}
		}




		/**
		 *
		 */
		protected function beforeHeaders(){ }


		/**
		 * @throws ConnectionClosed
		 */
		protected function headers(){
			$i = 0;
			while(!$this->source->isEof()){
				$data = $this->source->readLine();
				if($data === false){
					throw new ConnectionClosed('The server closed the connection');
				}
				$this->buffer($data);
				$this->handleLine($data,$i);
				if(!$this->process_headers){
					break;
				}
				$i++;
			}
		}

		/**
		 *
		 */
		protected function afterHeaders(){
			$this->document->onHeadersRead($this);
		}

		/**
		 *
		 */
		protected function beforeContents(){ }

		/**
		 * @return string
		 * @throws ConnectionClosed
		 */
		protected function contents(){
			if(!$this->document->haveHeader('Transfer-Encoding','chunked')){
				$length = $this->document->getHeader('Content-Length',null);
				if($length!==null){
					$length = intval($length);
				}
			}else{
				$length = null;
			}
			$content = '';
			if($length === null && $this->source instanceof Memory){
				$content = $this->source->read(-1);
				if($content === false){
					throw new ConnectionClosed('The server closed the connection');
				}
				$this->buffer($content);
			}else{
				$block_size = 4072;
				while(!$this->source->isEof()){
					if($length === null){
						$data = $this->source->readLine(128);
						if($data === false){
							throw new ConnectionClosed('The server closed the connection');
						}
						$this->buffer($data);
						$length = hexdec(trim($data));
					}elseif($length > 0){
						$read_length = $length > $block_size ? $block_size : $length;
						$length -= $read_length;
						$data = $this->source->read($read_length);
						if($data === false){
							throw new ConnectionClosed('The server closed the connection');
						}
						$this->buffer($data);
						$content.= $data;
						if ($length <= 0) {
							$this->source->seek(2,SEEK_CUR);
							$length = false;
						}
					}else{
						break;
					}
				}
			}


			return $content;
		}

		/**
		 * @param $contents
		 * @return mixed
		 */
		protected function decodeContents($contents){
			$decoded = $this->document->decodeContents($contents);
			if($decoded !== null){
				$contents = $decoded;
			}
			return $contents;
		}

		/**
		 * @param $contents
		 */
		protected function handleContents($contents = null){
			$this->document->setContent($contents);
		}

		protected function afterContents(){
			$this->document->onContentsRead($this);
		}

		protected function beforeHeaderSet($data, $i){ }


	}
}

