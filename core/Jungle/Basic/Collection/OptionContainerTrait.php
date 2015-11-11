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

	public function getOption($key,$default = null){
		if($this->hasOption($key)){
			return $this->_OptionContainerTrait_options[$key];
		}else{
			return $default;
		}
	}

	public function setOption($key,$value){
		if($this->_OptionContainerTrait_options[$key]!==$value){
			$this->_OptionContainerTrait_options[$key] = $value;
			$this->onOptionChanged();
		}
	}

	public function hasOption($key){
		return isset($this->_OptionContainerTrait_options[$key]);
	}

	public function rmOption($key){
		unset($this->_OptionContainerTrait_options[$key]);
	}

	protected function onOptionChanged(){}


}