<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 16.11.2015
 * Time: 1:34
 */
namespace Jungle\_DesignPatterns\ParadigmLabs {

	/**
	 * Class DataDelegate
	 * @package Jungle\_DesignPatterns\ParadigmLabs
	 */
	class DataDelegate{

		const D_ISSET   = 'isset';
		const D_SET     = 'set';
		const D_UNSET   = 'unset';
		const D_GET     = 'get';

		protected $source;

		protected $decoration;

		/**
		 * @param array|object $source
		 * @return $this|void
		 */
		public function setSource($source){
			$this->source = $source;
			return $this;
		}

		/**
		 * @param array $decoration
		 * @return $this
		 */
		public function setDecoration(array $decoration){
			$this->decoration = $decoration;
			return $this;
		}
		/**
		 * @param $field
		 * @return null
		 */
		public function __get($field){
			if(isset($this->decoration[$field])){
				return $this->_callDecoration($this->decoration[$field],self::D_GET,$field);
			}else{
				return $this->_simpleSourceAccess(self::D_GET, $field);
			}
		}

		/**
		 * @param $field
		 * @return bool
		 */
		public function __isset($field){
			if(isset($this->decoration[$field])){
				return $this->_callDecoration($this->decoration[$field],self::D_ISSET,$field);
			}else{
				return $this->_simpleSourceAccess(self::D_ISSET, $field);
			}
		}

		/**
		 * @param $field
		 * @param $value
		 */
		public function __set($field,$value){
			if(isset($this->decoration[$field])){
				$this->_callDecoration($this->decoration[$field],self::D_SET,$field,$value);
			}else{
				$this->_simpleSourceAccess(self::D_SET, $field,$value);
			}
		}

		/**
		 * @param $field
		 */
		public function __unset($field){
			if(isset($this->decoration[$field])){
				$this->_callDecoration($this->decoration[$field],self::D_UNSET,$field);
			}elseif(in_array($field,$this->decoration)){

			}else{
				$this->_simpleSourceAccess(self::D_UNSET, $field);
			}
		}


		protected function _simpleSourceAccess($type,$key ,$value = null){
			if(is_array($this->source)){
				switch($type){
					case self::D_SET:
						$this->source[$key] = $value;
						break;
					case self::D_GET:
						return isset($this->source[$key])?$this->source[$key]:null;
						break;
					case self::D_ISSET:
						return isset($this->source[$key]);
						break;
					case self::D_UNSET:
						unset($this->source[$key]);
						break;
				}
			}elseif(is_object($this->source)){

				switch($type){
					case self::D_SET:
						$this->source->{$key} = $value;
						break;
					case self::D_GET:
						return isset($this->source->{$key})? $this->source->{$key}:null;
						break;
					case self::D_ISSET:
						return isset($this->source->{$key});
						break;
					case self::D_UNSET:
						unset($this->source->{$key});
						break;
				}

			}
			return null;

		}

		/**
		 * @param $config
		 * @param $type
		 * @param $key
		 * @param null $value
		 * @return mixed
		 */
		protected function _callDecoration($config, $type, $key, $value = null){
			if(is_array($config) && isset($config[$type])){
				if(is_callable($config[$type])){
					return call_user_func($config, $this->source, $key, $value, $type);
				}elseif(is_string($config[$type])){
					return $this->_simpleSourceAccess($type, $config[$type], $value);
				}
			}elseif(is_callable($config)){
				return call_user_func($config, $this->source, $type);
			}elseif(is_string($config)){
				return $this->_simpleSourceAccess($type, $config, $value);
			}
			return $this->_simpleSourceAccess($type, $key, $value);
		}

	}


}

