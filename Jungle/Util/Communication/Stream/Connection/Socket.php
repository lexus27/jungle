<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.01.2016
 * Time: 15:54
 */
namespace Jungle\Util\Communication\Stream\Connection {

	use Jungle\Util\Communication\Connection\Exception;
	use Jungle\Util\Communication\Stream;
	use Jungle\Util\Communication\Stream\Connection;

	/**
	 * Class Socket
	 * @package Jungle\Util\Communication\Stream\Connector
	 */
	class Socket extends Connection{

		/**
		 * @param $length
		 * @param callable|null $reader
		 * @return string
		 */
		protected function _read($length,callable $reader = null){
			if($reader){
				return call_user_func($reader,$this->connection);
			}
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
				throw new \Jungle\Util\Communication\Connection\Exception('Failure data send');
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
		 *
		 */
		protected function _connect(){
			$u          = $this->url;
			$hostname   = $u->render(null,[$u::V_PORT]);
			$port       = @$u->getPort()->getIdentifier();



			$connection = fsockopen($hostname, $port, $errNo,$errStr, $this->timeout);

			if(!$connection){
				$this->error('Error connect to "'.$hostname.':'.$port.'" message: "'.$hostname.'", number: "'.$errNo.'"',550);
			}
			stream_set_timeout($connection,$this->timeout);
			return $connection;
		}

	}
}

