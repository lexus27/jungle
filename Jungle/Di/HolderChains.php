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

	use Jungle\Di\HolderChains\HolderManagerInterface;
	use Jungle\Exception;

	/**
	 * Class HolderChains
	 * @package Jungle\Di
	 */
	class HolderChains implements HolderManagerInterface, DiInterface{


		/** @var  DiInterface[]  */
		protected $dependency_injections = [];

		/** @var  array  */
		protected $holders_history = [];

		/** @var  array  */
		protected $holders = [];

		/** @var bool  */
		protected $holders_sorted = false;

		/** @var  DiInterface */
		protected $parent;

		/**
		 * @param $alias
		 * @param $priority
		 * @return $this
		 */
		public function defineHolder($alias, $priority = 0.0){
			$this->holders[$alias] = floatval($priority);
			$this->holders_sorted = false;
			return $this;
		}

		/**
		 * @param $holderAlias
		 * @return $this
		 * @throws Exception
		 */
		public function restoreInjection($holderAlias){
			if(array_key_exists($holderAlias, $this->holders_history)){
				if(!empty($this->holders_history[$holderAlias])){
					$this->dependency_injections[$holderAlias] = array_pop($this->holders_history[$holderAlias]);
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
		 * @return DiInterface|null
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
			foreach($this->holders as $key){
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
			foreach($this->holders as $key){
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
			foreach($this->holders as $key){
				if(!($di = $this->dependency_injections[$key])) continue;
				$names = array_replace($names, array_fill_keys($di->getContainerNames(),null));
			}
			return array_keys($names);
		}


		/**
		 * @param $name
		 * @param array|null $parameters
		 * @return mixed
		 */
		public function get($name, array $parameters = null){
			if(!$this->holders_sorted) $this->_sortHolders();
			foreach($this->holders as $key){
				if(!($di = $this->dependency_injections[$key]) || !$di->has($name)) continue;
				return $di->get($name, $parameters);
			}
			return null;
		}


		/**
		 * @param $key
		 * @return bool
		 */
		public function has($key){
			if(!$this->holders_sorted) $this->_sortHolders();
			foreach($this->holders as $key){
				if(!($di = $this->dependency_injections[$key])) continue;
				if($di->has($key)){
					return true;
				}
			}
			return false;
		}



		/**
		 * Whether a offset exists
		 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
		 * @param mixed $offset <p>
		 * An offset to check for.
		 * </p>
		 * @return boolean true on success or false on failure.
		 * </p>
		 * <p>
		 * The return value will be casted to boolean if non-boolean was returned.
		 * @since 5.0.0
		 */
		public function offsetExists($offset){
			if(!$this->holders_sorted) $this->_sortHolders();
			foreach($this->holders as $key){
				if(!($di = $this->dependency_injections[$key])) continue;
				if($di->has($key)){
					return true;
				}
			}
			return false;
		}

		/**
		 * Offset to retrieve
		 * @link http://php.net/manual/en/arrayaccess.offsetget.php
		 * @param mixed $offset <p>
		 * The offset to retrieve.
		 * </p>
		 * @return mixed Can return all value types.
		 * @since 5.0.0
		 */
		public function offsetGet($offset){
			if(!$this->holders_sorted) $this->_sortHolders();
			foreach($this->holders as $key){
				if(!($di = $this->dependency_injections[$key]) || !$di->has($offset)) continue;
				return $di->get($offset);
			}
			return null;
		}




		/**
		 * @param $serviceKey
		 * @return mixed
		 */
		public function getShared($serviceKey){
			if(!$this->holders_sorted) $this->_sortHolders();
			foreach($this->holders as $key){
				if(!($di = $this->dependency_injections[$key]) || !$di->has($serviceKey)) continue;
				return $di->getShared($serviceKey);
			}
			return null;
		}


		/**
		 * @param $name
		 * @return DiInterface
		 */
		public function getServiceContainer($name){
			if(!$this->holders_sorted) $this->_sortHolders();
			foreach($this->holders as $key){
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
			foreach($this->holders as $key){
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
			foreach($this->holders as $key){
				if(!($di = $this->dependency_injections[$key])) continue;
				$services = array_replace($services, $di->getServices());
			}
			return $services;
		}






		/**
		 * @param $name
		 * @return $this
		 */
		public function removeService($name){}

		/**
		 * @param $name
		 * @return $this
		 */
		public function removeContainer($name){}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function remove($name){}

		/**
		 * @param $name
		 * @return ServiceInterface
		 */
		public function resetService($name){}

		/**
		 * @param $key
		 * @param $definition
		 * @param bool|false $shared
		 * @return mixed
		 */
		public function set($key, $definition, $shared = false){}

		/**
		 * @param $serviceKey
		 * @param $definition
		 * @return mixed
		 */
		public function setShared($serviceKey, $definition){}
		/**
		 * @param $name
		 * @return DiInterface
		 */
		public function container($name){}

		/**
		 * @param $name
		 * @param DiInterface $di
		 * @return mixed
		 */
		public function setServiceContainer($name, DiInterface $di){}


		/**
		 * @param $existingServiceKey
		 * @param null $definition
		 * @return $this
		 */
		public function setOverlapFrom($existingServiceKey, $definition = null){}

		/**
		 * @param bool|false|string $overlap
		 * @return $this
		 */
		public function useSelfOverlapping($overlap = false){}

		/**
		 * @return bool
		 */
		public function isSelfOverlapping(){}

		/**
		 * @return mixed
		 */
		public function getOverlapKey(){}


		/**
		 * Offset to set
		 * @link http://php.net/manual/en/arrayaccess.offsetset.php
		 * @param mixed $offset <p>
		 * The offset to assign the value to.
		 * </p>
		 * @param mixed $value <p>
		 * The value to set.
		 * </p>
		 * @return void
		 * @since 5.0.0
		 */
		public function offsetSet($offset, $value){}

		/**
		 * Offset to unset
		 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
		 * @param mixed $offset <p>
		 * The offset to unset.
		 * </p>
		 * @return void
		 * @since 5.0.0
		 */
		public function offsetUnset($offset){}

		/**
		 * @return DiInterface
		 */
		public function getRoot(){
			if(!$this->parent){
				return $this;
			}
			return $this->parent->getRoot();
		}

		/**
		 * @param DiInterface $parent
		 * @return mixed
		 */
		public function setParent(DiInterface $parent){
			$this->parent = $parent;
		}

		/**
		 * @return mixed
		 */
		public function getParent(){
			return $this->parent;
		}

		/**
		 * @return DiInterface
		 */
		public function getNext(){}

		/**
		 * @param DiInterface $di
		 * @return mixed
		 */
		public function setNext(DiInterface $di){}
	}
}

