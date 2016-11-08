<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.10.2016
 * Time: 0:17
 */
namespace Jungle\Util\Communication\Stream {

	/**
	 * Class StreamInteraction
	 * @package Jungle\Util\Communication\Stream
	 */
	class StreamInteraction implements StreamInteractionInterface{

		/** @var  resource */
		protected $resource;

		/**
		 * @param $length
		 * @return mixed
		 * @throws \Exception
		 */
		public function read($length){
			$result = @fread($this->resource, $length);
			if($result === false){
				throw new \Exception('Stream interaction [read]: Resource is not available Stream');
			}
			return $result;
		}

		/**
		 * @param $data
		 * @param null $length
		 * @return mixed
		 * @throws \Exception
		 */
		public function write($data, $length = null){
			if($length===null){
				$length = strlen($data);
			}
			$result = @fwrite($this->resource, $data, $length);
			if($result === false){
				throw new \Exception('Stream interaction [write]: Resource is not available Stream');
			}
			return $result;
		}

		/**
		 * @param null $length
		 * @return mixed
		 * @throws \Exception
		 */
		public function readLine($length = null){
			if($length===null){
				$result = @fgets($this->resource);
			}else{
				$result = @fgets($this->resource, $length);
			}
			if($result === false){
				throw new \Exception('Stream interaction [readLine]: Resource is not available Stream');
			}
			return $result;
		}

		/**
		 * @return mixed
		 */
		public function isEof(){
			return feof($this->resource);
		}

		/**
		 * @param $offset
		 * @param $whence
		 * @return mixed
		 */
		public function seek($offset, $whence = SEEK_SET){
			return fseek($this->resource,$offset,$whence);
		}

		/**
		 * @return resource
		 */
		public function getResource(){
			return $this->resource;
		}

	}
}

