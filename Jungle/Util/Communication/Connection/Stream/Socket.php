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
		 * @param array $config
		 * @return $this
		 */
		public function setConfig(array $config){
			$this->config = array_replace([
				'host'          => null,
				'port'          => null,
				'transport'     => null,
				'timeout'       => null,
			], $config);
			return $this;
		}

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
			$timeout = $this->getOption('timeout',$this->default_timeout);

			$host = $this->getOption('host',null,true);
			$port = $this->getOption('port',null,true);

			$transport = $this->getOption('transport',null);
			$hostname = ($transport?$transport.'://':'') . $host;



			$connection = @fsockopen($hostname, $port, $errNo,$errStr, $timeout);
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
			stream_set_timeout($connection,$timeout);
			return $connection;
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

