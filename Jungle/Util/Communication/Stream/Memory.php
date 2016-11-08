<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.10.2016
 * Time: 0:14
 */
namespace Jungle\Util\Communication\Stream {

	/**
	 * Class Memory
	 * @package Jungle\Util\Communication\Stream
	 */
	class Memory implements StreamInteractionInterface{

		/** @var  int */
		protected $position = 0;

		/** @var  int */
		protected $length;

		/** @var string  */
		protected $text;

		/**
		 * Memory constructor.
		 * @param string $string
		 */
		public function __construct($string){
			$this->text = $string;
			$this->length = strlen($string);
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
					$this->text{$this->position} = $char;
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
				$a = substr($this->text,$this->position);
				$this->position = $this->length;
			}else{
				$a = substr($this->text,$this->position, $length);
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
			$pos = strpos($this->text,"\r\n",$this->position);
			if($pos!==false){
				$nextPos = $pos+$len;
				$readLen = $nextPos - $this->position;
				if($length!==null && $readLen > $length){
					$readLen = $length;
				}
				$str = substr($this->text,$this->position,$readLen);
				$this->position = $nextPos;
				return $str;
			}else{
				$str = substr($this->text,$this->position);
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

