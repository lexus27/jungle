<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 03.02.2016
 * Time: 20:22
 */
namespace Jungle\Communication {

	use Jungle\Communication\Connection\Exception;

	/**
	 * Class Connection
	 * @package Jungle\Communication
	 */
	abstract class Connection{

		/**
		 * @var int
		 */
		protected $timeout = 2;

		/**
		 * @var resource|bool
		 */
		protected $connection;

		/**
		 * @var URL
		 */
		protected $url;

		/**
		 * @return $this
		 */
		public function reconnect(){
			$this->close();
			return $this;
		}

		/**
		 * @param URL $url
		 * @return $this
		 */
		public function setUrl($url){
			$this->url = URL::getURL($url);
			return $this;
		}

		/**
		 * @return URL
		 */
		public function getUrl(){
			return $this->url;
		}

		/**
		 * @param int $timeout
		 * @return $this
		 */
		public function setTimeout($timeout){
			$this->timeout = intval($timeout);
			return $this;
		}

		/**
		 * @return int
		 */
		public function getTimeout(){
			return $this->timeout;
		}


		/**
		 * @return $this
		 */
		public function connect(){
			if(!$this->connection){
				$this->connection = $this->_connect();
			}
			return $this;
		}

		/**
		 * @return $this
		 */
		public function close(){
			if($this->connection){
				$this->_close();
				$this->connection = null;
			}
			return $this;
		}


		/**
		 * Open connection
		 * @return resource|bool
		 */
		abstract protected function _connect();

		/**
		 * Close connection
		 */
		abstract protected function _close();

		/**
		 * @param $message
		 * @param $code
		 * @throws Exception
		 */
		protected function error($message,$code){
			throw new Exception($message,$code);
		}

		/**
		 * @return $this
		 */
		public function __clone(){
			$this->connection = null;
		}

		/**
		 * @return resource
		 */
		public function getRealConnection(){
			return $this->connection;
		}

	}
}

