<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 27.01.2016
 * Time: 20:52
 */
namespace Jungle {

	use Jungle\Di\DiInterface;
	use Jungle\Di\Service;
	use Jungle\Di\ServiceInterface;

	/**
	 * Class Di
	 * @package Jungle
	 */
	class Di implements DiInterface{

		/** @var  DiInterface|null */
		protected $parent;

		/** @var  ServiceInterface[]|DiInterface[]  */
		protected $services = [];

		/** @var  mixed[]  */
		protected $shared_instances = [];

		/** @var bool  */
		protected $overlapping_mode = false;

		/** @var  string|null */
		protected $overlap_service_key;

		protected static $latest_created;

		/**
		 * Di constructor.
		 */
		public function __construct(DiInterface $parent = null){
			if($parent === null){
				self::$latest_created = $this;
			}else{
				$this->setParent($parent);
			}
		}

		/**
		 * @return Di
		 */
		public static function getDefault(){
			if(!self::$latest_created){
				return new self();
			}
			return self::$latest_created;
		}

		/**
		 * @param $existingServiceKey
		 * @param null $definition
		 * @return mixed
		 */
		public function setOverlapFrom($existingServiceKey, $definition = null){
			if(!$this->overlapping_mode){
				$this->overlapping_mode = true;
			}
			$this->overlap_service_key = $existingServiceKey;
			if($definition){
				$this->setShared($existingServiceKey,$definition);
			}
			return $this;
		}

		/**
		 * @param bool|false|false $overlap
		 * @return mixed
		 */
		public function useSelfOverlapping($overlap = false){
			$this->overlapping_mode = $overlap;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function isSelfOverlapping(){
			return $this->overlap_service_key;
		}

		/**
		 * @return null|string
		 */
		public function getOverlapKey(){
			return $this->overlap_service_key;
		}



		/**
		 * @return $this
		 */
		public function getRoot(){
			if(!$this->parent){
				return $this;
			}
			return $this->parent->getRoot();
		}

		/**
		 * @param mixed $offset
		 * @return mixed
		 */
		public function offsetExists($offset){
			return isset($this->services[$offset]);
		}

		/**
		 * @param mixed $offset
		 * @return mixed
		 */
		public function offsetGet($offset){
			return $this->get($offset);
		}

		/**
		 * @param mixed $offset
		 * @param mixed $value
		 * @return mixed
		 */
		public function offsetSet($offset, $value){
			if($offset){
				$this->set($offset,$value);
			}
		}

		/**
		 * @param mixed $offset
		 * @return mixed
		 */
		public function offsetUnset($offset){
			$this->removeService($offset);
		}


		/**
		 * @param $name
		 * @param $value
		 */
		public function __set($name, $value){
			$this->set($name,$value);
		}

		/**
		 * @param $name
		 * @return mixed
		 * @throws \Exception
		 */
		public function __get($name){
			return $this->get($name);
		}

		/**
		 * @param $name
		 * @return bool
		 */
		public function __isset($name){
			return isset($this->services[$name]);
		}

		/**
		 * @param $name
		 */
		public function __unset($name){
			$this->remove($name);
		}


		/**
		 * @param $service_key
		 * @param array $parameters
		 * @return mixed
		 * @throws \Exception
		 */
		public function get($service_key, array $parameters = null){

			if($service_key === null && $this->overlapping_mode){
				return $this->get($this->overlap_service_key,$parameters);
			}else{
				$fullName = $service_key;
				if(!is_array($service_key)){
					$service_key = explode('.',$service_key);
				}
				$c = count($service_key);
				$container = $this;
				while($c){
					if($c > 1){
						$container_name = array_shift($service_key);$c--;
						$container = $container->getServiceContainer($container_name);
					}else{
						$name = array_shift($service_key);$c--;
						if(isset($container->services[$name])){
							$srv = $container->services[$name];
							if($srv instanceof ServiceInterface){
								return $srv->resolve($container,$parameters);
							}elseif($srv instanceof DiInterface){
								if($srv->isSelfOverlapping()){
									return $srv->get(null,$parameters);
								}
								return $srv;
							}
						}else{
							throw new \Exception('Service "'.$fullName.'" not exists!');
						}
					}
				}
				throw new \Exception('Service "'.$fullName.'" not exists!');
			}
		}

		/**
		 * @param $service_key
		 * @param $service_definition
		 * @param bool $shared
		 * @return $this
		 */
		public function set($service_key, $service_definition, $shared = false){
			if(isset($this->services[$service_key])){
				$service = $this->services[$service_key];
				if($service instanceof ServiceInterface){
					$service->reset();
					$service->setDefinition($service_definition);
					$service->setShared($shared);
					$service->setName($service_key);
				}
			}else{
				$this->services[$service_key] = new Service($service_key,$service_definition,$shared);
			}
			return $this;
		}

		/**
		 * @param $key
		 * @return bool
		 */
		public function has($key){
			return isset($this->services[$key]);
		}

		/**
		 * @param $key
		 * @return $this
		 */
		public function remove($key){
			if(!is_array($key)){
				$key = explode('.',$key);
			}
			$c = count($key);
			$container = $this;
			while($c){
				if($c > 1){
					$container_name = array_shift($key);$c--;
					$container = $container->getServiceContainer($container_name);
				}else{
					$name = array_shift($key);$c--;
					unset($container->services[$name]);
					unset($container->shared_instances[$name]);
				}
			}
			return $this;
		}

		/**
		 * @param $key
		 * @return $this
		 * @throws \Exception
		 */
		public function removeService($key){
			if(!is_array($key)){
				$key = explode('.',$key);
			}
			$c = count($key);
			$container = $this;
			while($c){
				if($c > 1){
					$container_name = array_shift($key);$c--;
					$container = $container->getServiceContainer($container_name);
				}else{
					$name = array_shift($key);$c--;
					if(isset($container->services[$name])){
						$s = $container->services[$name];
						if($s instanceof ServiceInterface){
							unset($container->services[$name]);
							unset($container->shared_instances[$name]);
						}
					}
				}
			}
			return $this;
		}

		/**
		 * @param $key
		 * @return $this
		 * @throws \Exception
		 */
		public function resetService($key){
			if(!is_array($key)){
				$key = explode('.',$key);
			}
			$c = count($key);
			$container = $this;
			while($c){
				if($c > 1){
					$container_name = array_shift($key);$c--;
					$container = $container->getServiceContainer($container_name);
				}else{
					$name = array_shift($key);$c--;
					if(isset($container->services[$name])){
						$s = $container->services[$name];
						if($s instanceof ServiceInterface){
							$s->reset();
							unset($container->shared_instances[$name]);
							return $s;
						}
					}
				}
			}
			return null;
		}

		/**
		 * @param $service_key
		 * @return ServiceInterface
		 * @throws \Exception
		 */
		public function getService($service_key){
			$fullName = $service_key;
			if(!is_array($service_key)){
				$service_key = explode('.',$service_key);
			}
			$c = count($service_key);
			$container = $this;
			while($c){
				if($c > 1){
					$container_name = array_shift($service_key);$c--;
					$container = $container->getServiceContainer($container_name);
				}else{
					$name = array_shift($service_key);$c--;
					if(isset($container->services[$name])){
						$srv = $container->services[$name];
						if($srv instanceof ServiceInterface){
							return $srv;
						}
					}else{
						throw new \Exception('Service "'.$fullName.'" not exists!');
					}
				}
			}
			throw new \Exception('Service "'.$fullName.'" not exists!');
		}


		/**
		 * @param $key
		 * @return $this
		 * @throws \Exception
		 */
		public function removeContainer($key){
			if(!is_array($key)){
				$key = explode('.',$key);
			}
			$c = count($key);
			$container = $this;
			while($c){
				if($c > 1){
					$container_name = array_shift($key);$c--;
					$container = $container->getServiceContainer($container_name);
				}else{
					$name = array_shift($key);$c--;
					if(isset($container->services[$name])){
						$s = $container->services[$name];
						if($s instanceof DiInterface){
							unset($container->services[$name]);
							unset($container->shared_instances[$name]);
						}
					}
				}
			}
			return $this;
		}

		/**
		 * @param $service_key
		 * @param $service_definition
		 * @return $this
		 */
		public function setShared($service_key, $service_definition){
			return $this->set($service_key,$service_definition,true);
		}

		/**
		 * @param $service_key
		 * @return mixed
		 * @throws \Exception
		 */
		public function getShared($service_key){
			$fullName = $service_key;
			if(!is_array($service_key)){
				$service_key = explode('.',$service_key);
			}
			$c = count($service_key);
			$container = $this;
			while($c){
				if($c > 1){
					$container_name = array_shift($service_key);$c--;
					$container = $container->getServiceContainer($container_name);
				}else{
					$name = array_shift($service_key);$c--;
					if(!isset($container->shared_instances[$name])){
						if(isset($container->services[$name])){
							$srv = $container->services[$name];
							if($srv instanceof ServiceInterface){
								if($srv->isShared()){
									return $srv->resolve($container);
								}else{
									return $container->shared_instances[$name] = $srv->resolve($container);
								}
							}else{
								return $srv;
							}
						}else{
							throw new \Exception('Service "'.$fullName.'" not exists!');
						}
					}
					return $container->shared_instances[$name];
				}
			}
			throw new \Exception('Service "'.$fullName.'" not exists!');
		}

		/**
		 * @param $container_name
		 * @param DiInterface $di
		 * @return $this
		 */
		public function setServiceContainer($container_name, DiInterface $di){
			$this->services[$container_name] = $di;
			$di->setParent($this);
			return $this;
		}

		/**
		 * @param $container_name
		 * @return DiInterface
		 * @throws \Exception
		 */
		public function getServiceContainer($container_name){
			if(isset($this->services[$container_name])){
				if(!$this->services[$container_name] instanceof DiInterface){
					throw new \Exception('Service container "'.$container_name.'" is not DiInterface');
				}
				return $this->services[$container_name];
			}else{
				throw new \Exception('Service container "'.$container_name.'" not found');
			}
		}

		/**
		 * @param $name
		 * @return DiInterface
		 */
		public function container($name){
			$di = new Di($this);
			$this->setServiceContainer($name,$di);
			return $di;
		}

		/**
		 * @return array
		 */
		public function getServiceNames(){
			$names = [];
			foreach($this->services as $name => $srv){
				if($srv instanceof ServiceInterface){
					$names[] = $name;
				}
			}
			return $names;
		}

		/**
		 * @return array
		 */
		public function getContainerNames(){
			$names = [];
			foreach($this->services as $name => $srv){
				if($srv instanceof DiInterface){
					$names[] = $name;
				}
			}
			return $names;
		}

		/**
		 * @param DiInterface $parent
		 * @return $this
		 */
		public function setParent(DiInterface $parent){
			if($this->parent !== $parent){
				$this->parent = $parent;
			}
			return $this;
		}

		/**
		 * @return $this
		 */
		public function getParent(){
			return $this;
		}
	}

}

