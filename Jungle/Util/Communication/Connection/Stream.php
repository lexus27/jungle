<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 23:25
 */
namespace Jungle\Util\Communication\Connection {
	
	use Jungle\Util\Communication\Connection;

	/**
	 * Class Stream
	 * @package Jungle\Util\Communication\Connection
	 */
	abstract class Stream extends Connection implements StreamInterface{

		/**
		 * @param $length
		 * @return string
		 */
		public function read($length){
			return fread($this->connection,$length);
		}

		/**
		 * @param $length
		 * @return mixed
		 */
		public function readLine($length = null){
			if($length===null){
				return fgets($this->connection);
			}else{
				return fgets($this->connection,$length);
			}
		}


		/**
		 * @param $data
		 * @param null $length
		 * @return int
		 * @throws Exception
		 */
		public function write($data, $length = null){
			$l = $length!==null?$length:strlen($data);
			$s = fwrite($this->connection,$data,$l);
			if($s!==$l){
				throw new Exception('Failure data send');
			}
			return $s;
		}
		/**
		 * Close connection
		 */
		protected function _close(){
			fclose($this->connection);
		}

		/**
		 * @return bool
		 */
		public function isEof(){
			return feof($this->connection);
		}

		/**
		 * @param $offset
		 * @param $whence
		 * @return mixed
		 */
		public function seek($offset, $whence = SEEK_SET){
			return fseek($this->connection,$offset, $whence);
		}

	}
}

