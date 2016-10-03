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
	abstract class Stream extends Connection{
		/**
		 * @param $length
		 * @return mixed
		 */
		public function readLine($length){
			if(!$this->connection){
				$this->connect();
			}
			return $this->_readLine($length);
		}

		/**
		 * @param $length
		 * @return mixed
		 */
		abstract protected function _readLine($length);


		/**
		 * @param $length
		 * @return mixed
		 */
		public function read($length){
			if(!$this->connection){
				$this->connect();
			}
			return $this->_read($length);
		}

		/**
		 * @param $length
		 * @return mixed
		 */
		abstract protected function _read($length);

		/**
		 * @param $data
		 * @param null $length
		 * @return mixed
		 */
		public function send($data, $length = null){
			if(!$this->connection) $this->connect();
			return $this->_send($data,$length);
		}

		/**
		 * @param $data
		 * @param $length
		 * @return mixed
		 */
		abstract protected function _send($data, $length = null);

	}
}

