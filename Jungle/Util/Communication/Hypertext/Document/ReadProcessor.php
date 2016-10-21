<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.10.2016
 * Time: 19:17
 */
namespace Jungle\Util\Communication\Hypertext\Document {

	use Jungle\Util\Communication\Hypertext\Document;
	use Jungle\Util\Communication\Hypertext\DocumentInterface;
	use Jungle\Util\Communication\Hypertext\Header;
	use Jungle\Util\Communication\Stream\Memory;
	use Jungle\Util\Communication\Stream\StreamInteractionInterface;

	/**
	 * Class ReadProcessor
	 * @package Jungle\Util\Communication\Hypertext\Document
	 */
	class ReadProcessor extends Processor{

		/** @var  ReadProcessor */
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
		 * @param null|string|StreamInteractionInterface $source
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
			try{
				$this->checkBeforeStart();
				$this->sourceBeforeProcess($generated);
				$this->beforeProcess();
				$this->beforeHeaders();
				if($this->completed === false){
					$this->headers();
					$this->afterHeaders();
					$this->beforeContents();
					if($this->completed === false){
						$contents = $this->contents();
						if($contents){
							$contents = $this->decodeContents($contents);
							$this->handleContents($contents);
						}else{
							$this->handleContents(null);
						}
						$this->afterContents();
					}
					$this->afterProcess();
				}
				$this->sourceAfterProcess($generated);

				$this->completed = true;
				return $this->document;
			}catch(Document\Exception\ProcessorEarlyException $e){
				$this->completed = true;
				return $this->document;
			}finally{
				$this->continueProcess();
			}
		}


		protected function beforeProcess(){
			$this->document->beforeRead($this);
		}

		protected function headers(){
			$i = 0;
			while(!$this->source->isEof()){
				$data = $this->source->readLine();
				$this->buffer($data);
				$this->handleLine($data,$i);
				if(!$this->process_headers){
					break;
				}
				$i++;
			}
		}

		/**
		 * @param $data
		 * @param $i
		 */
		protected function handleLine($data, $i){
			$data = rtrim($data,"\r\n");
			if($data){
				if($this->beforeHeaderRead($data,$i) !== false){
					if($data = Header::parseHeaderRow($data)){
						$this->document->setHeader($data[0],$data[1],false);
					}
				}
			}else{
				$this->process_headers = false;
			}
		}

		/**
		 * @param $data
		 * @param $i
		 * @return bool|void
		 */
		protected function beforeHeaderRead($data, $i){
			return $this->document->beforeHeaderRead($data,$i);
		}

		/**
		 *
		 */
		protected function afterHeaders(){
			$this->document->onHeadersRead($this);
		}

		/**
		 * @return string
		 */
		protected function contents(){
			$chunked = $this->document->haveHeader('Transfer-Encoding','chunked');
			$length = $this->document->getHeader('Content-Length',null);
			if($length !== null){
				$length = intval($length);
			}elseif(!$chunked){
				$length = true;
			}

			$content = '';
			if($length === null && $this->source instanceof Memory){
				$content = $this->source->read(-1);
			}else{
				$block_size = 4072;
				while(!$this->source->isEof()){
					if($length === null){
						$data = $this->source->readLine(128);
						$length = hexdec(trim($data));
					}elseif($length === true){
						$content.= $this->source->read($block_size);
					}elseif($length > 0){
						$read_length = $length > $block_size ? $block_size : $length;
						$length -= $read_length;
						$data = $this->source->read($read_length);
						$content.= $data;
						if ($length <= 0) {
							if($chunked){
								$this->source->seek(2,SEEK_CUR);
							}
							$length = false;
						}
					}else{
						break;
					}
				}
			}
			$this->buffer($content);
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

		/**
		 *
		 */
		protected function afterContents(){
			$this->document->afterRead($this);
		}

		/**
		 *
		 */
		protected function continueProcess(){
			$this->document->continueRead($this);
		}


		/**
		 *
		 */
		protected function afterProcess(){}




	}
}

