<?php
/**
 * Created by PhpStorm.
 * Project: MobileCasino
 * Date: 16.03.2015
 * Time: 13:54
 */

namespace Jungle\Basic\Collection;

/**
 * Class OptionContainerTrait
 * @package Jungle\Basic\Collection
 *
 * Трейт реализующий хранение опций и настроек
 *
 */
trait OptionContainerTrait {

	protected $_OptionContainerTrait_options = [];


	/**
	 * @param $key
	 * @param null $default
	 * @return null
	 */
	public function &getOption($key,$default = null){
		if($this->hasOption($key)){
			return $this->_OptionContainerTrait_options[$key];
		}else{
			$d = $default;
			return $d;
		}
	}

	public function setOption($key,$value){
		if(!isset($this->_OptionContainerTrait_options[$key]) || $this->_OptionContainerTrait_options[$key]!==$value){
			$this->_OptionContainerTrait_options[$key] = $value;
			$this->onOptionChanged($key);
		}
	}

	public function hasOption($key){
		return isset($this->_OptionContainerTrait_options[$key]);
	}

	public function rmOption($key){
		unset($this->_OptionContainerTrait_options[$key]);
	}

	protected function onOptionChanged($key){}


}