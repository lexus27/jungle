<?php
/**
 * Created by PhpStorm.
 * Project: MobileCasino
 * Date: 16.03.2015
 * Time: 13:54
 */

namespace Jungle\Util;

/**
 * Class OptionContainerTrait
 * @package Jungle\Basic\Collection
 *
 * Трейт реализующий хранение опций и настроек
 *
 */
trait OptionContainerTrait {

	/** @var array  */
	protected $_srv_options = [];


	/**
	 * @param $key
	 * @param null $default
	 * @return null
	 */
	public function &getOption($key,$default = null){
		if($this->hasOption($key)){
			return $this->_srv_options[$key];
		}else{
			$d = $default;
			return $d;
		}
	}

	public function setOption($key,$value){
		if(!isset($this->_srv_options[$key]) || $this->_srv_options[$key] !== $value){
			$this->_srv_options[$key] = $value;
			$this->_srv_onOptionChanged($key);
		}
	}

	/**
	 * @param $key
	 */
	public function requireOption($key){

	}

	public function hasOption($key){
		return isset($this->_srv_options[$key]);
	}

	public function rmOption($key){
		unset($this->_srv_options[$key]);
	}

	protected function _srv_onOptionChanged($key){}


}