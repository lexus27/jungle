<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.07.2016
 * Time: 15:20
 */
namespace Jungle\Di {

	use Jungle\Di;

	/**
	 * Class DiChained
	 * @package Jungle\Di
	 */
	class DiChained extends Di implements DiChainedInterface{

		/** @var  DiInterface[] */
		protected $chains = [];

		/** @var array  */
		protected $layouts = [];

		/** @var array  */
		protected $layouts_order = [];

		/** @var array  */
		protected $layouts_sorted = false;

		/**
		 * @return array|DiInterface[]
		 */
		public function getChains(){
			if(!$this->layouts_sorted){
				$this->layouts_sorted = true;
				$this->chains = [];
				foreach($this->layouts_order as $i => $alias){
					if(isset($this->layouts[$alias])){
						$this->chains[] = $this->layouts[$alias];
					}
				}
			}
			return $this->chains;
		}

		/**
		 * @param $alias
		 * @param DiInterface $di
		 * @return $this
		 */
		public function setLayout($alias, DiInterface $di){
			$this->layouts[$alias] = $di;
			$this->layouts_sorted = false;
			return $this;
		}

		/**
		 * @param $alias
		 * @return null
		 */
		public function getLayout($alias){
			return isset($this->layouts[$alias])?$this->layouts[$alias]:null;
		}

		/**
		 * @param array $order
		 * @return $this
		 */
		public function setLayoutsOrder(array $order){
			$this->layouts_order = $order;
			$this->layouts_sorted = false;
			return $this;
		}

		/**
		 * @return array
		 */
		public function getServiceNames(){
			if($this->layouts){
				$service_names = [];
				foreach($this->getChains() as $di){
					$service_names = array_merge($service_names, $di->getServiceNames());
				}
				$service_names = array_merge($service_names, parent::getServiceNames());
				return array_unique($service_names);
			}else{
				return parent::getServiceNames();
			}
		}

		/**
		 * @return array
		 */
		public function getContainerNames(){
			if($this->layouts){
				$service_names = [ ];
				foreach($this->getChains() as $di){
					$service_names = array_merge($service_names, $di->getContainerNames());
				}
				$service_names = array_merge($service_names, parent::getContainerNames());
				return array_unique($service_names);
			}
			return parent::getContainerNames();
		}

		/**
		 * @param $key
		 * @param array|null $parameters
		 * @return mixed
		 */
		public function get($key, array $parameters = null){
			if($this->layouts){
				foreach($this->getChains() as $di){
					if($s = $di->get($key, $parameters)){
						return $s;
					}
				}
			}
			return parent::get($key,$parameters);
		}

		/**
		 * @param $serviceKey
		 * @return mixed
		 */
		public function getShared($serviceKey){
			if($this->layouts){
				foreach($this->getChains() as $di){
					if($s = $di->getShared($serviceKey)){
						return $s;
					}
				}
			}
			return parent::getShared($serviceKey);
		}

		/**
		 * @param $name
		 * @return DiInterface
		 */
		public function getServiceContainer($name){
			if($this->layouts){
				foreach($this->getChains() as $di){
					if($s = $di->getServiceContainer($name)){
						return $s;
					}
				}
			}
			return parent::getServiceContainer($name);
		}

		/**
		 * @return DiInterface[]|ServiceInterface[]
		 */
		public function getServices(){
			if($this->layouts){
				$services = [];
				foreach($this->getChains() as $di){
					$services = array_replace($services, $di->getServices());
				}
				$services = array_replace($services, parent::getServices());
				return $services;
			}
			return parent::getServices();
		}

		/**
		 * @param $name
		 * @return ServiceInterface
		 */
		public function getService($name){
			if($this->layouts){
				foreach($this->getChains() as $di){
					if($s = $di->getService($name)){
						return $s;
					}
				}
			}
			return parent::getService($name);
		}

		/**
		 * @param $object
		 * @return ServiceInterface|null
		 */
		public function getSharedServiceBy($object){
			if($this->layouts){
				foreach($this->getChains() as $di){
					if($s = $di->getSharedServiceBy($object)){
						return $s;
					}
				}
			}
			return parent::getSharedServiceBy($object);
		}

		/**
		 * @param $key
		 * @return bool
		 */
		public function has($key){
			if($this->layouts){
				foreach($this->getChains() as $di){
					if($di->has($key)){
						return true;
					}
				}
			}
			return parent::has($key);
		}
	}
}

