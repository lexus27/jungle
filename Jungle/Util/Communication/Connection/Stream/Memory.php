<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.10.2016
 * Time: 17:02
 */
namespace Jungle\Util\Communication\Connection\Stream {
	
	use Jungle\Util\Communication\Connection\Stream;

	/**
	 * Class Memory
	 * @package Jungle\Util\Communication\Connection\Stream
	 */
	class Memory extends Stream{

		/** @var   */
		protected $position;

		/** @var   */
		protected $length;

		/**
		 * Memory constructor.
		 * @param string $string
		 */
		public function __construct($string){
			$this->setConfig([
				'string' => $string
			]);
		}

		/**
		 * @param array $config
		 * @return $this
		 */
		public function setConfig(array $config){
			$this->config = array_replace([
				'string' => '',
			], $config);
			return $this;
		}

		/**
		 * Open connection
		 * @return resource|bool
		 */
		protected function _connect(){
			$string = $this->getOption('string', '');
			$this->length = strlen($string);
			$this->position = 0;
			return $string;
		}

		/**
		 * Close connection
		 */
		protected function _close(){
			$this->length = null;
			$this->position = null;
		}

		/**
		 * @param $data
		 * @param $length
		 * @return mixed
		 */
		public function write($data, $length = null){
			if($length === null){
				$length = strlen($data);
			}
			$i = 0;
			while(true){
				if($length > $i){
					$char = $data{$i};
					$this->connection{$this->position} = $char;
					$this->position++;
					if($this->length <= $this->position){
						$this->length++;
					}
					$i++;
				}else{
					break;
				}
			}
			return $i+1;
		}

		/**
		 * @param $length
		 * @return mixed
		 */
		public function read($length){
			if($this->position >= $this->length){
				return false;
			}
			if($length === -1){
				$a = substr($this->connection,$this->position);
				$this->position = $this->length;
			}else{
				$a = substr($this->connection,$this->position, $length);
				$this->position+= $length;
			}

			return $a;
		}

		/**
		 * @param null $length
		 * @return mixed
		 */
		public function readLine($length = null){
			$len = strlen("\r\n");
			$pos = strpos($this->connection,"\r\n",$this->position);
			if($pos!==false){
				$nextPos = $pos+$len;
				$readLen = $nextPos - $this->position;
				if($length!==null && $readLen > $length){
					$readLen = $length;
				}
				$str = substr($this->connection,$this->position,$readLen);
				$this->position = $nextPos;
				return $str;
			}else{
				$str = substr($this->connection,$this->position);
				$this->position = $this->length;
				return $str;
			}
		}

		/**
		 * @return mixed
		 */
		public function isEof(){
			return $this->position >= $this->length;
		}

		/**
		 * @param $offset
		 * @param $whence
		 * @return mixed
		 */
		public function seek($offset, $whence = SEEK_SET){
			if($whence === SEEK_CUR){
				$this->position = $this->position + $offset;
			}elseif($whence === SEEK_END){
				$this->position = $this->length + $offset;
			}else{

				if($offset >= $this->length){
					$this->position = $this->length;
				}elseif($offset < 0){
					$this->position = 0;
				}
			}
		}


	}
}

