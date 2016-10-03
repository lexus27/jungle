<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 03.02.2016
 * Time: 20:22
 */
namespace Jungle\Util\Communication {

	use Jungle\Util\Communication\Connection\Exception;

	/**
	 * Class Connection
	 * @package Jungle\Util\Communication
	 */
	abstract class Connection implements ConnectionInterface{

		/** @var resource|bool */
		protected $connection;

		/** @var  array */
		protected $config = [];

		/** @var int*/
		protected $default_timeout = 2;

		/**
		 * Connection constructor.
		 * @param array $config
		 */
		public function __construct(array $config){
			$this->setConfig($config);
		}

		/**
		 * @param array $config
		 * @return $this
		 */
		public function setConfig(array $config){
			$this->config = array_replace([
				'host'      => null,
				'port'      => null,
				'scheme'    => null,
				'timeout'   => null,
			], $config);
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getConfig(){
			return $this->config;
		}

		/**
		 * @param $key
		 * @param $default
		 * @param bool $required
		 * @return null
		 * @throws Exception\ConfigException
		 */
		public function getOption($key, $default = null, $required = false){
			if(isset($this->config[$key])){
				return $this->config[$key];
			}elseif($required!==false){
				if(is_string($required)){
					throw new Exception\ConfigException('Param "'.$key.'" required: "'.$required.'"');
				}else{
					throw new Exception\ConfigException('Param "'.$key.'" required.');
				}
			}else{
				return $default;
			}
		}


		/**
		 * @return $this
		 */
		public function reconnect(){
			$this->close();
			return $this;
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
		 * @return $this
		 */
		public function __clone(){
			$this->connection = null;
		}


		/**
		 * @return resource
		 */
		public function getInternalConnection(){
			return $this->connection;
		}



	}
}

