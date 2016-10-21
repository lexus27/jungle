<?php
/**
 * Created by PhpStorm.
 * Project: MobileCasino
 * Date: 16.03.2015
 * Time: 13:54
 */

namespace Jungle\Util\PropContainer;
use Jungle\Util\Exception\RequiredServiceParam;

/**
 * Class PropContainerOptionTrait
 * @package Jungle\Basic\Collection
 *
 * Трейт реализующий хранение опций и настроек
 *
 */
trait PropContainerOptionTrait {

	/** @var array  */
	protected $_srv_options = [];


	/**
	 * @param $key
	 * @param null $default
	 * @param bool $createLinkIfDefault
	 * @return null
	 */
	public function &getOption($key,$default = null, $createLinkIfDefault = false){
		if($this->hasOption($key)){
			return $this->_srv_options[$key];
		}else{
			if($createLinkIfDefault){
				$this->_srv_options[$key] = $default;
				return $this->_srv_options[$key];
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
		if(!isset($this->_srv_options[$key]) || ($old = $this->_srv_options[$key]) !== $value){
			$this->_srv_options[$key] = $value;
			$this->_srv_onOptionChanged($key,$old);
		}
	}

	/**
	 * @param $key
	 * @return mixed
	 * @throws RequiredServiceParam
	 */
	public function requireOption($key){
		if(isset($this->_srv_options[$key])){
			return $this->_srv_options[$key];
		}else{
			throw new RequiredServiceParam($key, 'option');
		}
	}

	public function hasOption($key){
		return isset($this->_srv_options[$key]);
	}

	public function rmOption($key){
		unset($this->_srv_options[$key]);
	}

	protected function _srv_onOptionChanged($key,$old){}


}