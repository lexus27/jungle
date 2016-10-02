<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 23:22
 */
namespace Jungle\Util\Communication\Connection\Stream {

	use Jungle\Util\Communication\Connection\Exception;
	use Jungle\Util\Communication\Connection\Stream;

	/**
	 * Class Socket
	 * @package Jungle\Util\Communication\Connection
	 */
	class Socket extends Stream{

		/**
		 * @param $length
		 * @return string
		 */
		protected function _read($length){
			return fread($this->connection,$length);
		}

		/**
		 * @param $data
		 * @param null $length
		 * @return int
		 * @throws Exception
		 */
		protected function _send($data, $length = null){
			$l = $length!==null?$length:strlen($data);
			$s = fwrite($this->connection,$data,$l);
			if($s!==$l){
				throw new Exception('Failure data send');
			}
			return $s;
		}

		/**
		 * @return resource
		 */
		public function getResource(){
			if(!$this->connection)$this->connect();
			return $this->connection;
		}

		/**
		 * @return resource
		 * @throws Exception
		 */
		protected function _connect(){
			$to = $this->getOption('timeout',2);
			$host = $this->getOption('host',null,true);
			$port = $this->getOption('port',null,true);
			$connection = fsockopen($port, $port, $errNo,$errStr, $to);
			if(!$connection){
				throw new Exception\ConnectException('Error connect to "'.$host.':'.$port.'" message: "'.$errStr.'", number: "'.$errNo.'"');
			}
			stream_set_timeout($connection,$to);
			return $connection;
		}

		/**
		 * Close connection
		 */
		protected function _close(){
			fclose($this->connection);
		}

	}
}

