<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 19.05.2015
 * Time: 22:23
 */

namespace Jungle\XPlate;


use Jungle\XPlate\Interfaces\IWebEngine;

/**
 * Class WebEngine
 * @package Jungle\XPlate
 */
class WebEngine implements IWebEngine{

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $vendor;

	/**
	 * @var array
	 */
	protected $options = [];

	/**
	 * @var array
	 */
	protected $cssPropertyConfigurations = [];

	/**
	 * Получить имя объекта
	 * @return mixed
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * Выставить имя объекту
	 * @param $name
	 * @return $this
	 */
	public function setName($name){
		$this->name = $name;
		return $this;
	}


	/**
	 * @param $prefix
	 * @return $this
	 */
	public function setVendor($prefix){
		$this->vendor = strtolower($prefix);
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getVendor(){
		return $this->vendor;
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public function setOption($key,$value){
		$this->options[$key] = $value;
	}

	/**
	 * @param $key
	 * @param null $default
	 * @return mixed
	 */
	public function getOption($key,$default = null){
		if(isset($this->options[$key])){
			return $this->options[$key];
		}
		return $default;
	}

	/**
	 * @param $key
	 * @return bool
	 */
	public function hasOption($key){
		return isset($this->options[$key]);
	}

}