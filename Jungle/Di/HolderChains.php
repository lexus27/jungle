<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.09.2016
 * Time: 17:12
 */
namespace Jungle\Di {

	use Jungle\Di;
	use Jungle\Di\HolderChains\HolderManagerInterface;
	use Jungle\Exception;

	/**
	 * Class HolderChains
	 * @package Jungle\Di
	 */
	class HolderChains implements
		HolderManagerInterface,
		DiInterface,
		DiNestingOverlappingInterface{

		use DiNestingOverlappingTrait;
		use DiNestingTrait;

		/** @var  DiInterface[]  */
		protected $dependency_injections = [];

		/** @var  array  */
		protected $holders_history = [];

		/** @var  array  */
		protected $holders = [];

		/** @var bool  */
		protected $holders_sorted = false;


		/**
		 * @param $alias
		 * @param DiInterface $di
		 * @param null $priority
		 * @return $this
		 * @throws Exception
		 */
		public function insertInjection($alias, DiInterface $di, $priority = null){
			if($priority!==null) $priority = floatval($priority);

			if(
				( isset($this->holders[$alias]) && $priority !== null && $this->holders[$alias] != $priority) ||
			    (!isset($this->holders[$alias]) && $priority !== null)
			){
				$this->holders_sorted = false;
			}elseif($priority === null){
				throw new Exception('Priority not pass');
			}
			$this->holders[$alias] = $priority;


			if(!isset($this->dependency_injections[$alias])){
				$this->dependency_injections[$alias] = null;
			}
			if($this->dependency_injections[$alias]){
				$previous = $this->dependency_injections[$alias];
				$this->holders_history[$alias][] = $previous;
			}
			$di->setParent($this);
			$this->dependency_injections[$alias] = $di;

			if(!isset($this->holders_history[$alias])){
				$this->holders_history[$alias] = [];
			}
		}

		/**
		 * @param $alias
		 * @param $priority
		 * @return $this
		 */
		public function defineHolder($alias, $priority = 0.0){
			if(!isset($this->holders[$alias]) || $this->holders[$alias] !== ($priority = floatval($priority))){
				$this->holders[$alias] = $priority;
				$this->dependency_injections[$alias] = null;
				$this->holders_sorted = false;
				if(!isset($this->holders_history[$alias])){
					$this->holders_history[$alias] = [];
				}
			}
			return $this;
		}

		/**
		 * @param $holderAlias
		 * @param object|null $instance
		 * @return $this
		 * @throws Exception
		 */
		public function restoreInjection($holderAlias, $instance = null){
			if(array_key_exists($holderAlias, $this->holders_history)){
				if($instance!==null && $this->dependency_injections[$holderAlias] !== $instance){
					return $this;
				}
				if(!empty($this->holders_history[$holderAlias])){
					$this->dependency_injections[$holderAlias] = $injection = array_pop($this->holders_history[$holderAlias]);
					$injection->setParent($this);
				}else{
					$this->dependency_injections[$holderAlias] = null;
				}
			}else{
				throw new Exception('Holder with alias "'.$holderAlias.'" not defined');
			}
			return $this;
		}

		/**
		 * @param $holderAlias
		 * @param DiInterface $di
		 * @return $this
		 */
		public function changeInjection($holderAlias, DiInterface $di){
			if(array_key_exists($holderAlias, $this->dependency_injections)){
				if($this->dependency_injections[$holderAlias]){
					$previous = $this->dependency_injections[$holderAlias];
					$this->holders_history[$holderAlias][] = $previous;
				}
				$di->setParent($this);
				$this->dependency_injections[$holderAlias] = $di;
			}else{
				//error
			}
			return $this;
		}

		/**
		 * @param $holderAlias
		 * @return mixed|void
		 * @throws Exception
		 */
		public function getLastInjection($holderAlias){
			if(array_key_exists($holderAlias, $this->holders_history)){
				end($this->holders_history[$holderAlias]);
				return current($this->holders_history[$holderAlias]);
			}else{
				throw new Exception('Holder with alias "'.$holderAlias.'" not defined');
			}
		}

		/**
		 * @param $holderAlias
		 * @return Di|DiInterface|null
		 */
		public function getInjection($holderAlias){
			return isset($this->dependency_injections[$holderAlias])?$this->dependency_injections[$holderAlias]:null;
		}

		/**
		 *
		 */
		protected function _sortHolders(){
			$holders = array_keys($this->holders);
			usort($holders, function($a,$b){
				$priorityA = $this->holders[$a];
				$priorityB = $this->holders[$b];
				if($priorityA == $priorityB) return 0;
				return $priorityA > $priorityB?1:-1;
			});
			$_h = [];
			foreach($holders as $key){
				$_h[$key] = $this->holders[$key];
			}
			$this->holders = $_h;
			$this->holders_sorted = true;
		}

		/**
		 * @param $name
		 * @return ServiceInterface|null
		 */
		public function getService($name){
			if(!$this->holders_sorted) $this->_sortHolders();
			foreach($this->holders as $key => $priority){
				if(!($di = $this->dependency_injections[$key]) || !$di->has($name)) continue;
				return $di->getService($name);
			}
			return null;
		}




		/**
		 * @return array
		 */
		public function getServiceNames(){
			if(!$this->holders_sorted) $this->_sortHolders();
			$names = [];
			foreach($this->holders as $key => $priority){
				if(!($di = $this->dependency_injections[$key])) continue;
				$names = array_replace($names, array_fill_keys($di->getServiceNames(),null));
			}
			return array_keys($names);
		}

		/**
		 * @return array
		 */
		public function getContainerNames(){
			if(!$this->holders_sorted) $this->_sortHolders();
			$names = [];
			foreach($this->holders as $key => $priority){
				if(!($di = $this->dependency_injections[$key])) continue;
				$names = array_replace($names, array_fill_keys($di->getContainerNames(),null));
			}
			return array_keys($names);
		}


		/**
		 * @param $name
		 * @param array|null $parameters
		 * @param bool $throwException
		 * @return mixed
		 * @throws mixed
		 */
		public function get($name, array $parameters = null, $throwException = true){
			if(!$this->holders_sorted) $this->_sortHolders();
			$lastError = null;
			foreach($this->holders as $key => $priority){
				if(!($di = $this->dependency_injections[$key])) continue;
				try{
					$object = $di->get($name, $parameters);
					return $object;
				}catch(NotFoundException $lastError){}
			}
			if($throwException && $lastError instanceof NotFoundException){
				throw $lastError;
			}
			return null;
		}

		/**
		 * @param $name
		 * @return mixed|null
		 * @throws null
		 */
		public function __get($name){
			$parameters = null;
			$throwException = true;
			if(!$this->holders_sorted) $this->_sortHolders();
			$lastError = null;
			foreach($this->holders as $key => $priority){
				if(!($di = $this->dependency_injections[$key])) continue;
				try{
					$object = $di->get($name, $parameters);
					return $object;
				}catch(\Exception $lastError){}
			}
			if($throwException && $lastError instanceof \Exception)throw $lastError;
			return null;
		}


		/**
		 * @param $name
		 * @return bool
		 */
		public function has($name){
			if(!$this->holders_sorted) $this->_sortHolders();
			foreach($this->holders as $key => $priority){
				if(!($di = $this->dependency_injections[$key])) continue;
				if($di->has($name)){
					return true;
				}
			}
			return false;
		}


		/**
		 * @param mixed $offset
		 * @return bool
		 */
		public function offsetExists($offset){
			if(!$this->holders_sorted) $this->_sortHolders();
			foreach($this->holders as $key => $priority){
				if(!($di = $this->dependency_injections[$key])) continue;
				if($di->has($key)){
					return true;
				}
			}
			return false;
		}

		/**
		 * @param mixed $offset
		 * @return mixed|null
		 * @throws mixed
		 */
		public function offsetGet($offset){
			if(!$this->holders_sorted) $this->_sortHolders();
			$lastError = null;
			foreach($this->holders as $key => $priority){
				if(!($di = $this->dependency_injections[$key])) continue;
				try{
					$object = $di->offsetGet($offset);
					return $object;
				}catch(\Exception $lastError){}
			}
			if($lastError instanceof \Exception)throw $lastError;
			return null;
		}


		/**
		 * @param mixed $offset
		 * @param mixed $value
		 */
		public function offsetSet($offset, $value){}

		/**
		 * @param mixed $offset
		 */
		public function offsetUnset($offset){}


		/**
		 * @param $serviceKey
		 * @param bool $throwException
		 * @return mixed
		 * @throws null
		 */
		public function getShared($serviceKey, $throwException = true){
			if(!$this->holders_sorted) $this->_sortHolders();
			$lastError = null;
			foreach($this->holders as $key => $priority){
				if(!($di = $this->dependency_injections[$key])) continue;
				try{
					$object = $di->offsetGet($serviceKey);
					return $object;
				}catch(\Exception $lastError){}
			}
			if($throwException && $lastError instanceof \Exception)throw $lastError;
			return null;
		}


		/**
		 * @param $name
		 * @return DiInterface
		 */
		public function getServiceContainer($name){
			if(!$this->holders_sorted) $this->_sortHolders();
			foreach($this->holders as $key => $priority){
				if(!($di = $this->dependency_injections[$key]) || !($srvCt = $di->getServiceContainer($name))) continue;
				return $srvCt;
			}
			return null;
		}

		/**
		 * @param $object
		 * @return mixed
		 */
		public function getSharedServiceBy($object){
			if(!$this->holders_sorted) $this->_sortHolders();
			foreach($this->holders as $key => $priority){
				if(!($di = $this->dependency_injections[$key]) || !($service = $di->getSharedServiceBy($object))) continue;
				return $service;
			}
			return null;
		}

		/**
		 * @return DiInterface[]|ServiceInterface[]
		 */
		public function getServices(){
			if(!$this->holders_sorted) $this->_sortHolders();
			$services = [];
			foreach($this->holders as $key => $priority){
				if(!($di = $this->dependency_injections[$key])) continue;
				$services = array_replace($services, $di->getServices());
			}
			return $services;
		}

	}
}

