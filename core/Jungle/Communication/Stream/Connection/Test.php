<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 28.01.2016
 * Time: 18:05
 */
namespace Jungle\Communication\Stream\Connection {

	use Jungle\Communication\Stream\Connection;

	/**
	 * Class Test
	 * @package Jungle\Communication\Stream\Connection
	 */
	class Test extends Connection{

		/**
		 * @param $length
		 * @return string
		 */
		protected function _read($length){
			return '200 Success data from this connection';
		}

		/**
		 * @param $data
		 * @param null $length
		 */
		protected function _send($data, $length = null){

		}

		protected function _connect(){
			$this->connection = true;
		}
	}
}

