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
		 * @param $length
		 * @return mixed
		 */
		protected function _readLine($length){
			return fgets($this->connection,$length);
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
		 * @throws Exception\ConfigException
		 * @throws Exception\ConnectException
		 * @throws \Exception
		 */
		protected function _connect(){
			$to = $this->getOption('timeout',$this->default_timeout);
			$host = $this->getOption('host',null,true);
			$port = $this->getOption('port',null,true);
			$scheme = $this->getOption('scheme',null,true);
			$hostname = ($scheme?$scheme.'://':'') . $host;
			$connection = @fsockopen($hostname, $port, $errNo,$errStr, $to);
			if(!$connection){
				$enc = mb_detect_encoding($errStr,['cp1251']);
				if($enc){
					$errStr = mb_convert_encoding($errStr,'utf-8',$enc);
				}
				$message = 'Error connect to "'.$hostname.':'.$port.'" message: "'.$errStr.'", code: "'.$errNo.'"';
				if($errNo === 10060){
					throw new Exception\TimeoutException($message, $errNo);
				}
				throw new Exception\ConnectException($message, $errNo);
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

