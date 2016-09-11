<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 27.01.2016
 * Time: 20:52
 */
namespace Jungle {

	use Jungle\Di\DiInterface;
	use Jungle\Di\DiLocatorInterface;
	use Jungle\Di\DiNestingInterface;
	use Jungle\Di\DiNestingOverlappingInterface;
	use Jungle\Di\DiNestingOverlappingTrait;
	use Jungle\Di\DiNestingTrait;
	use Jungle\Di\DiSettingInterface;
	use Jungle\Di\Service;
	use Jungle\Di\ServiceInterface;

	/**
	 * Class Di
	 * @package Jungle
	 */
	class Di implements DiSettingInterface, DiLocatorInterface, DiNestingInterface, DiNestingOverlappingInterface{

		use DiNestingTrait;
		use DiNestingOverlappingTrait;

		/** @var Di  */
		protected static $latest_created;

		/** @var  ServiceInterface[]|DiInterface[]  */
		protected $services = [];

		/** @var  mixed[]  */
		protected $shared_instances = [];



		/**
		 * Di constructor.
		 * @param DiInterface $parent
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
		 * @param $service_key
		 * @param $service_definition
		 * @return $this
		 */
		public function setShared($service_key, $service_definition){
			if(isset($this->services[$service_key])){
				$service = $this->services[$service_key];
				if($service instanceof ServiceInterface){
					$service->reset();
					$service->setDefinition($service_definition);
					$service->setShared(true);
					$service->setName($service_key);
				}
			}else{
				$this->services[$service_key] = new Service($service_key,$service_definition,true);
			}
			return $this;
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
		 * @param $name
		 * @return DiInterface
		 */
		public function container($name){
			$di = new Di($this);
			$this->setServiceContainer($name,$di);
			return $di;
		}










		/**
		 * @param $key
		 * @return bool
		 */
		public function has($key){
			return isset($this->services[$key]);
		}


		/**
		 * @param $service_key
		 * @param array $parameters
		 * @param bool $throwException
		 * @return mixed
		 * @throws \Exception
		 */
		public function get($service_key, array $parameters = null, $throwException = true){
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
							if($throwException)throw new \Exception('Service "' . $fullName . '" not exists!');
						}
					}
				}
				if($throwException)throw new \Exception('Service "' . $fullName . '" not exists!');
			}
			return null;
		}


		/**
		 * @param $service_key
		 * @param bool $throwException
		 * @return mixed
		 * @throws \Exception
		 */
		public function getShared($service_key, $throwException = true){
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
							if($throwException)throw new \Exception('Service "' . $fullName . '" not exists!');
							return null;
						}
					}
					return $container->shared_instances[$name];
				}
			}
			if($throwException)throw new \Exception('Service "' . $fullName . '" not exists!');
			return null;
		}

		/**
		 * @param $service_key
		 * @param bool $throwException
		 * @return ServiceInterface
		 * @throws \Exception
		 */
		public function getService($service_key, $throwException = true){
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
					}elseif($throwException){
						throw new \Exception('Service "' . $fullName . '" not exists!');
					}
				}
			}
			if($throwException){
				throw new \Exception('Service "' . $fullName . '" not exists!');
			}
			return null;
		}


		/**
		 * @param $container_name
		 * @param bool $throwException
		 * @return DiInterface
		 * @throws \Exception
		 */
		public function getServiceContainer($container_name, $throwException = true){
			if(isset($this->services[$container_name])){
				if(!$this->services[$container_name] instanceof DiInterface){
					if($throwException)throw new \Exception('Service container "' . $container_name . '" is not DiInterface');
					return null;
				}
				return $this->services[$container_name];
			}else{
				if($throwException)throw new \Exception('Service container "' . $container_name . '" not found');
				return null;
			}
		}

		/**
		 * @param $object
		 * @return ServiceInterface
		 */
		public function getSharedServiceBy($object){
			foreach($this->services as $service){
				if($service->isShared() && $service->resolve($this) === $object){
					return $service;
				}
			}
			return null;
		}

		/**
		 * @return Di\DiInterface[]|Di\ServiceInterface[]
		 */
		public function getServices(){
			return $this->services;
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
			$names = [ ];
			foreach($this->services as $name => $srv){
				if($srv instanceof DiInterface){
					$names[] = $name;
				}
			}
			return $names;
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
					if($this->overlapping_mode && $this->overlap_service_key === $name){
						throw new \LogicException('Service "'.$name.'" used as overlap for container!');
					}
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
		 * @param mixed $offset
		 * @return mixed
		 */
		public function offsetExists($offset){
			return isset($this->services[$offset]);
		}

		/**
		 * @param mixed $name
		 * @return mixed
		 * @throws \Exception
		 */
		public function offsetGet($name){
			if(isset($this->services[$name])){
				$srv = $this->services[$name];
				if($srv instanceof ServiceInterface){
					return $srv->resolve($this,null);
				}elseif($srv instanceof DiInterface){
					if($srv->isSelfOverlapping()){
						return $srv->get(null,null);
					}
					return $srv;
				}
			}else{
				throw new \Exception('Service "' . $name . '" not exists!');
			}
			return null;
		}


		/**
		 * @param mixed $service_key
		 * @param mixed $service_definition
		 * @return mixed
		 */
		public function offsetSet($service_key, $service_definition){
			if($service_key){
				$shared = true;
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
			}
		}

		/**
		 * @param mixed $name
		 * @return mixed
		 */
		public function offsetUnset($name){
			if($this->overlapping_mode && $this->overlap_service_key === $name){
				throw new \LogicException('Service "'.$name.'" used as overlap for container!');
			}
			unset($this->services[$name]);
			unset($this->shared_instances[$name]);
			return $this;
		}


		/**
		 * @param $service_key
		 * @param $service_definition
		 */
		public function __set($service_key, $service_definition){
			$shared = true;
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
		}

		/**
		 * @param $name
		 * @return mixed
		 * @throws \Exception
		 */
		public function __get($name){
			if(isset($this->services[$name])){
				$srv = $this->services[$name];
				if($srv instanceof ServiceInterface){
					return $srv->resolve($this,null);
				}elseif($srv instanceof DiInterface){
					if($srv->isSelfOverlapping()){
						return $srv->get(null,null);
					}
					return $srv;
				}
			}else{
				throw new \Exception('Service "' . $name . '" not exists!');
			}
			return null;
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
		 * @return $this
		 */
		public function __unset($name){
			if($this->overlapping_mode && $this->overlap_service_key === $name){
				throw new \LogicException('Service "'.$name.'" used as overlap for container!');
			}
			unset($this->services[$name]);
			unset($this->shared_instances[$name]);
			return $this;
		}



	}

}

