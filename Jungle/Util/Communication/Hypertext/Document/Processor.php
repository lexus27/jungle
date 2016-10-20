<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.10.2016
 * Time: 19:10
 */
namespace Jungle\Util\Communication\Hypertext\Document {

	use Jungle\Util\Buffer\BufferInterface;
	use Jungle\Util\Communication\Stream\StreamInteractionInterface;
	use Jungle\Util\Communication\Net\ConnectionInterface;
	use Jungle\Util\Communication\Hypertext\DocumentInterface;

	/**
	 * Class Processor
	 * @package Jungle\Util\Communication\Hypertext\Document
	 */
	abstract class Processor implements ProcessorInterface{

		/** @var array  */
		protected static $service_properties = [
			'default_process_properties',
			'document' ,
			'buffer',
			'source',
			'completed',
			'config',
			'auto_close',
			'auto_connect'
		];

		/** @var array  */
		protected $default_process_properties = [];

		/** @var  DocumentInterface */
		protected $document;

		/** @var  BufferInterface|string|null */
		protected $buffer;

		/** @var  \Jungle\Util\Communication\Stream\StreamInteractionInterface */
		protected $source;

		/** @var  bool  */
		protected $completed = false;

		/** @var array  */
		protected $config = [];

		/** @var bool  */
		protected $auto_close = false;

		/** @var bool  */
		protected $auto_connect = false;

		/**
		 * Reader constructor.
		 * @param DocumentInterface|null $document
		 * @param bool|null $auto_connect
		 * @param bool|null $auto_close
		 * @param \Jungle\Util\Buffer\BufferInterface|string|null $buffer
		 */
		public function __construct(DocumentInterface $document = null, $auto_connect = null, $auto_close = null, $buffer = null){
			$this->default_process_properties = array_diff_key(get_object_vars($this), array_flip(self::$service_properties) );
			$this->document = $document;
			if(is_bool($auto_close)){
				$this->auto_close = $auto_close;
			}
			if(is_bool($auto_connect)){
				$this->auto_connect = $auto_connect;
			}
			if($buffer !== null){
				$this->buffer = $buffer;
			}
		}

		/**
		 * @param array $config
		 * @param bool|false $merge
		 * @return $this
		 */
		public function setConfig(array $config, $merge = false){
			$this->config = $merge?array_replace($this->config, $config):$config;
			return $this;
		}

		/**
		 * @param bool|true $auto_close
		 * @return $this
		 */
		public function setSourceAutoClose($auto_close = true){
			$this->auto_close = boolval($auto_close);
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function isSourceAutoClose(){
			return $this->auto_close;
		}


		/**
		 * @param bool|true $auto_close
		 * @return $this
		 */
		public function setSourceAutoConnect($auto_close = true){
			$this->auto_connect = boolval($auto_close);
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function isSourceAutoConnect(){
			return $this->auto_connect;
		}

		/**
		 * @return bool
		 */
		public function isSourceStreamInteraction(){
			return $this->source instanceof StreamInteractionInterface;
		}


		/**
		 *
		 */
		public function setBufferToString(){
			$this->buffer = '';
			return $this;
		}

		/**
		 * @param \Jungle\Util\Buffer\BufferInterface $buffer
		 */
		public function setBuffer(BufferInterface $buffer = null){
			$this->buffer = $buffer;
		}

		/**
		 * @return string|\Jungle\Util\Buffer\BufferInterface|null
		 */
		public function getBuffer(){
			return $this->buffer;
		}

		/**
		 * @return string|null
		 */
		public function getBuffered(){
			if($this->buffer instanceof BufferInterface){
				return $this->buffer->contents();
			}else{
				return $this->buffer;
			}
		}

		/**
		 * @param bool|false $completed
		 * @return $this
		 */
		public function setCompleted($completed = false){
			$this->completed = $completed;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isCompleted(){
			return $this->completed;
		}

		/**
		 * @param DocumentInterface $document
		 * @return $this
		 */
		public function setDocument(DocumentInterface $document){
			$this->document = $document;
			return $this;
		}

		/**
		 * @return DocumentInterface
		 */
		public function getDocument(){
			return $this->document;
		}

		/**
		 * @return mixed
		 */
		public function getSource(){
			return $this->source;
		}

		/**
		 *
		 */
		public function reset(){
			$this->completed = false;
			$this->resetBuffer();
			foreach($this->default_process_properties as $k=>$v){
				$this->{$k} = $v;
			}
		}

		protected function checkBeforeStart(){
			$this->document->beforeProcessStart($this);
		}

		protected function beforeProcess(){}

		protected function afterHeaders(){}
		protected function beforeHeaders(){}

		protected function beforeContents(){}
		protected function afterContents(){}

		protected function afterProcess(){}
		protected function continueProcess(){ }

		/**
		 * @param bool $generated
		 */
		protected function sourceBeforeProcess($generated = false){
			$source = $this->source;
			if($source instanceof ConnectionInterface && ((!$generated && $this->auto_connect) || $generated)){
				$source->connect();
			}
		}

		/**
		 * @param bool|false $generated
		 */
		protected function sourceAfterProcess($generated = false){
			$source = $this->source;
			if($source instanceof ConnectionInterface && ((!$generated && $this->auto_close) || $generated)){
				$source->close();
			}
		}

		/**
		 * @param $data
		 */
		protected function buffer($data){
			if($this->buffer instanceof BufferInterface){
				$this->buffer->write($data);
			}elseif(is_string($this->buffer)){
				$this->buffer.=$data;
			}
		}

		/**
		 *
		 */
		protected function resetBuffer(){
			if($this->buffer instanceof BufferInterface){
				$this->buffer->clear();
			}elseif(is_string($this->buffer)){
				$this->buffer='';
			}
		}


	}
}

