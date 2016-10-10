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

	use Jungle\Util\Communication\Connection\StreamInteractionInterface;
	use Jungle\Util\Communication\Connection\StreamInterface;
	use Jungle\Util\Specifications\Hypertext\ContentInterface;
	use Jungle\Util\Specifications\Hypertext\Document;

	/**
	 * Class WriteProcessor
	 * @package Jungle\Util\Specifications\Hypertext\Document
	 */
	class WriteProcessor extends Processor{

		protected static $default;

		/** @var  StreamInteractionInterface|string */
		protected $source;

		/** @var  string */
		protected $contents;

		/**
		 * @param WriteProcessor $writeProcessor
		 */
		public static function setDefault(WriteProcessor $writeProcessor){
			self::$default = $writeProcessor;
		}

		/**
		 * @return WriteProcessor
		 */
		public static function getDefault(){
			if(!self::$default){
				self::$default = new self();
			}
			return self::$default;
		}

		/**
		 * @param $source
		 * @return string|StreamInteractionInterface|StreamInterface
		 * @throws \Exception
		 */
		public function process($source = null){
			$this->reset();
			if(!$this->document){
				throw new \Exception('Document not present for writing');
			}
			$generated = !$source;
			$this->source = !$source?'':$source;
			try{
				$this->sourceBeforeProcess($generated);
				$this->beforeProcess();
				$this->beforeHeaders();
				if($this->completed === false){
					$this->headers();
					$this->afterHeaders();
					$this->beforeContents();
					if($this->completed === false){
						$this->contents();
					}
					$this->afterContents();
				}
				$this->afterProcess();
				$this->sourceAfterProcess($generated);
				$this->completed = true;
				return $this->source;
			}finally{
				$this->continueProcess();
			}
		}
		/**
		 * @param $contents
		 * @return $this
		 */
		public function setContents($contents){
			$this->contents = $contents;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getContents(){
			return $this->contents;
		}

		/**
		 *
		 */
		protected function beforeProcess(){
			$this->document->beforeWrite($this);
		}


		/**
		 *
		 */
		protected function beforeHeaders(){
			$content = $this->document->getContent();
			if($content instanceof ContentInterface){
				$content->beforeHeadersRender($this->document, $this);
				if($this->contents===null){
					$this->contents = (string) $content;
				}
			}else{
				$this->contents = $content;
			}
			if($this->contents!==null){
				$this->contents = $this->encodeContents($this->contents);
				$this->document->setHeader('Content-Length',strlen($this->contents),true);
			}
		}

		/**
		 *
		 */
		protected function headers(){
			$headers = $this->document->getHeaders();
			ksort($headers);
			foreach($headers as $key => $values){
				foreach($values as $value){
					$row = $this->renderHeaderLine($key,$value) . "\r\n";
					$this->write($row);
				}
			}
			$this->write("\r\n");
		}

		/**
		 * @param $key
		 * @param $value
		 * @return string
		 */
		protected function renderHeaderLine($key, $value){
			if(!$key){
				return "$value";
			}else{
				return "$key: $value";
			}

		}

		/**
		 * @return string
		 */
		protected function contents(){
			if($this->contents){
				$this->write($this->contents);
			}
		}

		/**
		 * @param $content_string
		 * @return null|string
		 */
		protected function encodeContents($content_string){
			$normalized = $this->document->encodeContents($content_string);
			if($normalized !==null){
				$content_string = $normalized;
			}
			return $content_string;
		}



		/**
		 * @param $string
		 * @throws \Exception
		 */
		public function write($string){
			$this->buffer($string);
			if($this->source!==null){
				if($this->source instanceof StreamInteractionInterface){
					$this->source->write($string);
				}else{
					$this->source.=$string;
				}
			}else{
				throw new \Exception('Output stream is not defined');
			}
		}

	}
}

