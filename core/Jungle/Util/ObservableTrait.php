<?php
/**
 * Created by PhpStorm.
 * Project: MobileCasino
 * Date: 07.03.2015
 * Time: 15:31
 */

namespace Jungle\Util;

/**
 * Trait ObservableInterface
 * 	- addEvent()
 * 	- invokeEvent()
 * 	- hasListeners()
 * 	- addListener()
 * 	- getAdditionListeners()
 * @package CSVGear
 */
trait ObservableTrait {

	/**
	 * @var array
	 */
	private $_events = [];

	/**
	 * @var array
	 */
	private $_listeners = [];

	/**
	 * @var array
	 */
	private $_listenersObjects = [];

	/**
	 * @var Observable[]
	 */
	private $_delegations = [];


	/**
	 * @param $name
	 */
	public function addEvent($name){
		if(is_array($name)){
			foreach($name as $k => & $v){
				$eName = is_string($k)?$k:$v;
				if($eName){
					$this->addEvent($eName);
					if(is_string($k) && $v){
						if(is_array($v)){
							$this->setListeners([$eName => $v]);
						}else{
							$this->setListeners([$eName => [$v]]);
						}
					}
				}
			}
		}else{
			$name = strtolower($name);
			if(!$this->hasEvent($name))$this->_events[] = $name;
		}
	}

	/**
	 * @params [eName,[arguments,...]]
	 * @return bool
	 */
	public function invokeEvent(/** Name, [args , ...] */){
		$args = & func_get_args();
		$try = true;
		$eName = array_shift($args);
		if(is_string($eName) && $eName){
			$eName = strtolower($eName);
			if($this->hasEvent($eName) && $this->hasListeners($eName)){
				$listeners = & $this->_listeners[$eName];
				if($listeners){
					array_splice($listeners,0,0,(array)$this->getAdditionListeners($eName));
					foreach($listeners as $i => & $listener){
						$fn = & $listener[0];
						$scope = & $listener[1];
						$config = & $listener[2];
						if(is_callable($fn) ||
						   (is_array($fn) && method_exists($fn[0],$fn[1])) ||
						   (is_string($fn) && function_exists($fn))
						){
							if($fn instanceof \Closure && is_object($scope)){
								$fn = $fn->bindTo($scope);
							}
							$output = call_user_func_array($fn,$args);
							if($output===false && $try)$try = false;

							if(!isset($config['::call_count::'])){
								$config['::call_count::'] = 0;
							}

							$config['::call_count::']++;

							if($config['single'])$config['call_limit'] = 1;
							if($config['call_limit']===$config['::call_count::']){
								unset($listeners[$i]);
							}
						}
					}
				}
				foreach($this->_delegations as $delegate){
					$output = call_user_func_array([$delegate,'invokeEvent'],array_merge([$eName,$args]));
					if($output===false && $try)$try = false;
				}

				foreach($this->_listenersObjects as $object){
					if(method_exists($object,'invokeEvent')){
						$output = call_user_func_array([$object,'invokeEvent'],array_merge([$eName,$args]));
						if($output===false && $try)$try = false;
					}
					if(method_exists($object,'invoke'.$eName)){
						$output = call_user_func_array([$object,'invoke'.$eName],array_merge($args));
						if($output===false && $try)$try = false;
					}
				}
			}
		}
		return $try;
	}

	/**
	 * @param $eName
	 * @return bool
	 */
	public function hasListeners($eName){
		$eName = strtolower($eName);
		return (boolean)(
			(
				isset($this->_listeners[$eName]) &&
				is_array($this->_listeners[$eName]) &&
				count($this->_listeners[$eName])
			) || (
				$this->_listenersObjects
			) || ($this->_delegations)
		);
	}

	/**
	 * @param $eName
	 * @return bool
	 */
	public function hasEvent($eName){
		$eName = strtolower($eName);
		return in_array($eName,$this->_events,true);
	}

	/**
	 * @param object|string $eName
	 * @param null $fn
	 * @param null $scope
	 * @param null $turn
	 * @param array $config
	 * @return bool
	 */
	public function addListener($eName,$fn=null,$scope=null,$turn=null,array $config=[]){
		if(is_object($eName) && $eName instanceof Observable){
			if(array_search($eName,$this->_delegations,true)===false){
				if(is_integer($turn))array_splice($this->_delegations,$turn,0,[$eName]);
				else $this->_delegations[] = $eName;
			}
			return true;
		}elseif(is_object($eName) && $fn===null){
			if(array_search($eName,$this->_listenersObjects,true)===false){
				if(is_integer($turn))array_splice($this->_listenersObjects,$turn,0,[$eName]);
				else $this->_listenersObjects[] = $eName;
			}
			return true;
		}else{
			if(is_callable($fn) || $fn instanceof \Closure || is_array($fn) || is_string($fn)){
				$eName = strtolower($eName);
				if($this->hasEvent($eName)){
					if(!is_array($this->_listeners[$eName])){
						$this->_listeners[$eName] = array();
					}
					$listeners = & $this->_listeners[$eName];
					$listener = array(&$fn,&$scope,(array)$config);
					if(is_integer($turn))array_splice($listeners,$turn,0,array($listener));
					else $listeners[] = $listener;
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * @param $eName
	 * @param null $fn
	 * @param null $scope
	 * @return bool
	 */
	public function removeListener($eName,$fn=null,$scope=null){
		if(is_object($eName) && $fn===null){
			if(($i = array_search($eName,$this->_listenersObjects,true))!==false){
				array_splice($this->_listenersObjects,$i,1);
			}
			return true;
		}else{
			if(is_callable($fn) || $fn instanceof \Closure || is_array($fn) || is_string($fn)){
				$eName = strtolower($eName);
				if($this->hasEvent($eName)){
					if(!is_array($this->_listeners[$eName])){
						return true;
					}
					foreach($this->_listeners[$eName] as $i => list($_fn,$_scope,$_config)){
						if($_fn===$fn && $scope===$_scope){
							unset($this->_listeners[$eName][$i]);
						}
					}
					return true;
				}
			}
		}
		return false;
	}

	public function setListeners(array $listeners = array(),$autoEvent=true){
		foreach($listeners as $eName => & $listener){
			/** @var array $listener */
			$listener = (array) $listener;
			if(!$this->hasEvent($eName) && $autoEvent)$this->addEvent($eName);
			if($this->hasEvent($eName)){
				foreach($listener as $handler){
					if(!is_array($handler)){
						$this->addListener($eName,$handler);
					}else{
						if(!$handler['turn'])$handler['turn'] = null;
						$this->addListener($eName,$handler['fn'],$handler['scope'],$handler['turn'],$handler['config']);
					}
				}
			}
		}
	}

	/**
	 * @param $name
	 * @return array array( array(fn,scope||null), array(fn,scope||null));
	 */
	protected function getAdditionListeners($name){
		//$name = strtolower($name);
		return array();
	}
}