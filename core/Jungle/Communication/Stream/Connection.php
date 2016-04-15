<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.01.2016
 * Time: 15:23
 */
namespace Jungle\Communication\Stream {

	use Jungle\Communication\URL;

	/**
	 * Class Connector
	 * @package Jungle\Communication\Stream
	 */
	abstract class Connection extends \Jungle\Communication\Connection{


		/**
		 * @param $length
		 * @param callable $reader
		 * @return
		 */
		public function read($length,callable $reader = null){
			if(!$this->connection) $this->connect();
			return $this->_read($length,$reader);
		}

		/**
		 * @param $data
		 * @param null $length
		 */
		public function send($data, $length = null){
			if(!$this->connection) $this->connect();
			return $this->_send($data,$length);
		}


		/**
		 * @param $length
		 * @param callable $reader
		 * @return
		 */
		abstract protected function _read($length,callable $reader = null);

		/**
		 * @param $data
		 * @param null $length
		 */
		abstract protected function _send($data, $length = null);

		/**
		 * Close connection
		 */
		protected function _close(){
			fclose($this->connection);
		}


	}
}

