<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.10.2016
 * Time: 15:51
 */
namespace Jungle\Util {

	use Jungle\Util\Exception\RequiredServiceParam;

	/**
	 * Class PropContainerParamTrait
	 * @package Jungle\Util
	 * @implements PropContainerParamInterface
	 */
	trait PropContainerParamTrait{

		/** @var array  */
		protected $_srv_params = [];

		/**
		 * @param $key
		 * @param null $default
		 * @param bool $createLinkIfDefault
		 * @return null
		 */
		public function &getOption($key,$default = null, $createLinkIfDefault = false){
			if($this->hasOption($key)){
				return $this->_srv_params[$key];
			}else{
				if($createLinkIfDefault){
					$this->_srv_params[$key] = $default;
					return $this->_srv_params[$key];
				}
				$d = $default;
				return $d;
			}
		}

		/**
		 * @param $key
		 * @param $value
		 */
		public function setOption($key,$value){
			$old = null;
			if(!isset($this->_srv_params[$key]) || ($old = $this->_srv_params[$key]) !== $value){
				$this->_srv_params[$key] = $value;
				$this->_srv_onOptionChanged($key,$old);
			}
		}

		/**
		 * @param $key
		 * @return mixed
		 * @throws RequiredServiceParam
		 */
		public function requireOption($key){
			if(isset($this->_srv_params[$key])){
				return $this->_srv_params[$key];
			}else{
				throw new RequiredServiceParam($key, 'option');
			}
		}

		public function hasOption($key){
			return isset($this->_srv_params[$key]);
		}

		public function rmOption($key){
			unset($this->_srv_params[$key]);
		}

		protected function _srv_onOptionChanged($key,$old){}

	}
}
