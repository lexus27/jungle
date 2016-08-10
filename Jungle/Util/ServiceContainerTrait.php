<?php
/**
 * Created by PhpStorm.
 * Project: MobileCasino
 * Date: 13.03.2015
 * Time: 13:21
 */

namespace Jungle\Util;

/**
 * Class ServiceContainerTrait
 * @package Jungle\Basic\Collection
 * Трейт реализующий хранение инициализаторов объектов
 *
 */
trait ServiceContainerTrait {

	/** @var object[]|callable[]|string[]  */
	protected $_ServiceContainerTrait_services = [];

	/**
	 * @param $alias
	 * @return null
	 */
	public function getService($alias){
		if($this->hasService($alias)){
			$service = null;
			if(is_object($this->_ServiceContainerTrait_services[$alias]) && !$this->_ServiceContainerTrait_services[$alias] instanceof \Closure){
				$service = $this->_ServiceContainerTrait_services[$alias];
			}elseif(is_callable($this->_ServiceContainerTrait_services[$alias])){
				$service = $this->_ServiceContainerTrait_services[$alias] = call_user_func($this->_ServiceContainerTrait_services[$alias]);
			}elseif(is_string($this->_ServiceContainerTrait_services[$alias])){
				$cName = $this->_ServiceContainerTrait_services[$alias];
				if(class_exists($cName,true)){
					$service = $this->_ServiceContainerTrait_services[$alias] = new $cName();
				}else{
					throw new \LogicException('Class "'.$cName.'" not found! for instantiate service "'.$alias.'"');
				}
			}

			$this->beforeGetService($alias,$service);

			if(is_object($service)){
				return $service;
			}else{
				throw new \LogicException('Service "'.$alias.'" instantiate error!');
			}

		}else{
			throw new \LogicException('Service "'.$alias.'" not defined!');
		}
	}

	/**
	 * @param $alias
	 * @return bool
	 */
	public function hasService($alias){
		return isset($this->_ServiceContainerTrait_services[$alias]);
	}

	/**
	 * @param $alias
	 * @param $service
	 */
	public function setService($alias,$service){
		if(!is_object($service) && !is_string($service) && !is_callable($service)){
			throw new \InvalidArgumentException('Passed service must be string or object or callable!');
		}
		if(!$alias){
			throw new \InvalidArgumentException('Invalid passed alias for create service!');
		}
		$this->_ServiceContainerTrait_services[$alias] = $service;
	}

	/**
	 * @param $alias
	 */
	public function rmService($alias){
		if($this->hasService($alias)){
			unset($this->_ServiceContainerTrait_services[$alias]);
		}
	}

	protected function beforeGetService($alias,$service){}



}