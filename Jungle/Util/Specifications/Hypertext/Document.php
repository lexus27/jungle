<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 08.01.2016
 * Time: 15:58
 */
namespace Jungle\Util\Specifications\Hypertext {

	use Jungle\Util\Communication\Connection\StreamInteractionInterface;
	use Jungle\Util\Communication\Connection\StreamInterface;
	use Jungle\Util\Specifications\Hypertext\Document\ReadProcessor;
	use Jungle\Util\Specifications\Hypertext\Document\WriteProcessor;
	use Jungle\Util\Specifications\Hypertext\Header\Pool;

	/**
	 * Class Document
	 * @package Jungle\HeaderCover
	 */
	class Document implements DocumentInterface{

		const META_HEADER = '';

		use HeaderRegistryTrait;

		/** @var  ContentInterface|string */
		protected $content;

		/** @var  string */
		protected $cache;

		/** @var bool  */
		protected $cacheable = false;

		/** @var  WriteProcessor */
		protected $write_processor;

		/** @var  ReadProcessor */
		protected $read_processor;


		/**
		 * Document constructor.
		 * @param null|string|StreamInteractionInterface|StreamInterface $definition
		 */
		public function __construct($definition = null){
			if(!is_null($definition)){
				$this->structure($definition);
			}
		}

		/**
		 * @param string|StreamInteractionInterface|StreamInterface $source
		 * @return $this
		 */
		public function structure($source){
			$reader = $this->getReadProcessor();
			$reader->setDocument($this);
			$reader->setBufferToString();
			$reader->process($source);
			if($this->cacheable){
				$this->cache = null;
			}
			return $this;
		}

		/**
		 *
		 */
		public function cacheClean(){
			$this->cache = null;
			return $this;
		}

		/**
		 * @param bool|true $cacheable
		 * @return $this
		 */
		public function setCacheable($cacheable = true){
			$this->cacheable = $cacheable;
			if(!$cacheable){
				$this->cache = null;
			}
			return $this;
		}

		/**
		 * @return string
		 */
		public function __toString(){
			if(is_string($this->cache) && $this->cacheable){
				return $this->cache;
			}else{
				$writer = $this->getWriteProcessor();
				$writer->setDocument($this);
				$source = $writer->process('');
				if($this->cacheable){
					$this->cache = (string)$source;
				}
				return $source;
			}
		}


		/**
		 * @param WriteProcessor $processor
		 * @return $this
		 */
		public function setWriteProcessor(WriteProcessor $processor = null){
			$this->write_processor = $processor;
			$processor->setDocument($this);
			return $this;
		}

		/**
		 * @return WriteProcessor
		 */
		public function getWriteProcessor(){
			if(!$this->write_processor){
				$this->write_processor = new WriteProcessor($this);
			}
			return $this->write_processor;
		}

		/**
		 * @param ReadProcessor $processor
		 * @return $this
		 */
		public function setReadProcessor(ReadProcessor $processor = null){
			$this->read_processor = $processor;
			$processor->setDocument($this);
			return $this;
		}

		/**
		 * @return ReadProcessor
		 */
		public function getReadProcessor(){
			if(!$this->read_processor){
				$this->read_processor = new ReadProcessor($this);
			}
			return $this->read_processor;
		}

		/**
		 * @param ContentInterface|null|string $content
		 * @return mixed
		 */
		public function setContent($content = null){
			$this->content = $content;
			return $this;
		}

		/**
		 * @return ContentInterface|null|string
		 */
		public function getContent(){
			return $this->content;
		}


		/**
		 * @param string $contents
		 * @return string|null
		 */
		public function encodeContents($contents){
			$pool       = Pool::getDefault();
			$keys       = array_keys($this->headers);
			$priorities = [];
			$headers    = [];

			foreach($keys as $key){
				if($pool->exists($key)){
					/** @var Header $header */
					$header = $pool->get($key);
					$priorities[$key]   = $header->getPriorityEncode();
					$headers[$key]      = $header;
				}else{
					$priorities[$key] = Header::DEFAULT_PRIORITY_ENCODE;
				}
			}
			asort($priorities);
			$hasDecode = false;
			foreach($priorities as $key => $priority){
				if(isset($headers[$key])){
					/** @var Header $header */
					$header = $headers[$key];
					$data = $header->encodeContents($this->headers[$key], $contents, $this);
					if(!is_null($data)){
						$contents = $data;
						$hasDecode = true;
					}
					if(!is_string($data) && $data!==null){
						return $contents;
					}
				}
			}
			if($hasDecode){
				return $contents;
			}
			return null;
		}

		/**
		 * @param string $contents
		 * @return string|null
		 */
		public function decodeContents($contents){
			$pool = Pool::getDefault();
			$keys       = array_keys($this->headers);
			$priorities = [];
			$headers    = [];

			foreach($keys as $key){
				if($pool->exists($key)){
					/** @var Header $header */
					$header = $pool->get($key);
					$priorities[$key]   = $header->getPriorityDecode();
					$headers[$key]      = $header;
				}else{
					$priorities[$key] = Header::DEFAULT_PRIORITY_DECODE;
				}
			}
			asort($priorities);
			$hasDecode = false;
			foreach($priorities as $key => $priority){
				if(isset($headers[$key])){
					/** @var Header $header */
					$header = $headers[$key];
					$data = $header->decodeContents($this->headers[$key], $contents, $this);
					if(!is_null($data)){
						$contents = $data;
						$hasDecode = true;
					}
					if(!is_string($data) && $data!==null){
						$hasDecode = true;
						break;
					}
				}
			}
			if($hasDecode){
				return $contents;
			}
			return null;
		}

		/**
		 * @param WriteProcessor $writer
		 * @return void
		 */
		public function beforeWrite(WriteProcessor $writer){}

		/**
		 * @param ReadProcessor $reader
		 * @return void
		 */
		public function beforeRead(ReadProcessor $reader){}

		/**
		 * @param $data
		 * @param $readingIndex
		 * @return mixed
		 */
		public function beforeHeaderRead($data, $readingIndex){}


		/**
		 * @param ReadProcessor $reader
		 * @return void
		 */
		public function onHeadersRead(ReadProcessor $reader){}

		/**
		 * @param ReadProcessor $reader
		 * @return void
		 */
		public function onContentsRead(ReadProcessor $reader){}


	}
}

