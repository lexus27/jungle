<?php
/**
 * Created by PhpStorm.
 * Project: MobileCasino
 * Date: 13.03.2015
 * Time: 13:21
 */

namespace Jungle\Util\PropContainer;

/**
 * Class PropContainerServiceTrait
 * @package Jungle\Basic\Collection
 * Трейт реализующий хранение инициализаторов объектов
 *
 */
trait PropContainerServiceTrait {

	/** @var object[]|callable[]|string[]  */
	protected $_srv_services = [];

	/**
	 * @param $alias
	 * @return null
	 */
	public function getService($alias){
		if($this->hasService($alias)){
			$service = null;
			if(is_object($this->_srv_services[$alias]) && !$this->_srv_services[$alias] instanceof \Closure){
				$service = $this->_srv_services[$alias];
			}elseif(is_callable($this->_srv_services[$alias])){
				$service = $this->_srv_services[$alias] = call_user_func($this->_srv_services[$alias]);
			}elseif(is_string($this->_srv_services[$alias])){
				$cName = $this->_srv_services[$alias];
				if(class_exists($cName,true)){
					$service = $this->_srv_services[$alias] = new $cName();
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
		return isset($this->_srv_services[$alias]);
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
		$this->_srv_services[$alias] = $service;
	}

	/**
	 * @param $alias
	 */
	public function rmService($alias){
		if($this->hasService($alias)){
			unset($this->_srv_services[$alias]);
		}
	}

	protected function beforeGetService($alias,$service){}



}