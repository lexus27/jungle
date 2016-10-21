<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.10.2016
 * Time: 19:04
 */
namespace Jungle\Util\PropContainer {

	/**
	 * Class PropContainerConfigTrait
	 * @package Jungle\Util
	 */
	trait PropContainerConfigTrait{


		/** @var array  */
		protected $_srv_config = [];


		/**
		 * @param $key
		 * @param null $default
		 * @param bool $createLinkIfDefault
		 * @return null
		 */
		public function &getConfig($key,$default = null, $createLinkIfDefault = false){
			if($this->hasConfig($key)){
				return $this->_srv_config[$key];
			}else{
				if($createLinkIfDefault){
					$this->_srv_config[$key] = $default;
					return $this->_srv_config[$key];
				}
				$d = $default;
				return $d;
			}
		}

		/**
		 * @param $key
		 * @param $value
		 */
		public function setConfig($key,$value){
			if(!isset($this->_srv_config[$key]) || $this->_srv_config[$key] !== $value){
				$this->_srv_config[$key] = $value;
				$this->_srv_onConfigChanged($key);
			}
		}

		/**
		 * @param $key
		 * @return mixed
		 * @throws RequiredPropException
		 */
		public function requireConfig($key){
			if(isset($this->_srv_config[$key])){
				return $this->_srv_config[$key];
			}else{
				throw new RequiredPropException($key, 'config');
			}
		}

		/**
		 * @param $key
		 * @return bool
		 */
		public function hasConfig($key){
			return isset($this->_srv_config[$key]);
		}

		/**
		 * @param $key
		 * @return $this
		 */
		public function rmConfig($key){
			unset($this->_srv_config[$key]);
			return $this;
		}

		protected function _srv_onConfigChanged($key){}



	}
}
