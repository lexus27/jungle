<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.06.2016
 * Time: 23:27
 */
namespace Jungle\Di {

	/**
	 * Class Service
	 * @package Jungle\Di
	 */
	class Service implements ServiceInterface{

		/** @var  string */
		protected $name;

		/** @var  mixed */
		protected $definition;

		/** @var array  */
		protected $parameters = [];

		/** @var  bool */
		protected $shared = false;

		/** @var  object */
		protected $shared_instance;

		/**
		 * Service constructor.
		 * @param $name
		 * @param $definition
		 * @param $shared
		 */
		public function __construct($name, $definition, $shared = false){
			$this->setName($name);
			$this->setDefinition($definition);
			$this->setShared($shared);
		}

		/**
		 * @return mixed
		 */
		public function reset(){
			$this->shared_instance = null;
		}

		/**
		 * @param DiInterface $di
		 * @param array|null $parameters
		 * @return mixed
		 */
		public function resolve(DiInterface $di, array $parameters = null){
			if($this->shared){
				if(!$this->shared_instance){
					$this->shared_instance = $this->_build($di,$this->parameters);
				}
				return $this->shared_instance;
			}else{
				return $this->_build($di,$parameters);
			}
		}

		/**
		 * @param DiInterface $di
		 * @param array|null $parameters
		 * @return mixed
		 * @throws \Exception
		 */
		protected function _build(DiInterface $di, array $parameters = null){
			if(is_callable($this->definition)){
				$parameters = (array)$parameters;
				array_unshift($parameters,$di);
				$object = call_user_func_array($this->definition,$parameters);
			}elseif(is_array($this->definition)){
				$definition = array_replace([
				    'class' => null,
				    'parameters' => $parameters,
				    'staticFactory' => null,
				],$this->definition);
				$parameters = $definition['parameters'];
				$class = $definition['class'];
				if(!$class){
					throw new \Exception('Service "'.$this->name.'" definition className not supplied!');
				}
				if(!class_exists($class)){
					throw new \Exception('Service "'.$this->name.'" class "'.$class.'" not found!');
				}
				if($definition['staticFactory']){
					$object = call_user_func_array([$class,$definition['staticFactory']],$parameters);
				}else{
					$object = new $class(...$parameters);
				}
			}elseif(is_object($this->definition)){
				$object = $this->definition;
			}else{
				throw new \Exception('Service "'.$this->name.'" is not valid service definition!');
			}
			if($object instanceof InjectionAwareInterface){
				$object->setDi($di);
			}
			return $object;
		}

		/**
		 * @param $position
		 * @param $value
		 * @return $this
		 */
		public function setParameter($position, $value){
			$this->parameters[$position] = $value;
			return $this;
		}

		/**
		 * @param $position
		 * @return null
		 */
		public function getParameter($position){
			return isset($this->parameters[$position])?$this->parameters[$position]:null;
		}

		/**
		 * @return array
		 */
		public function getParameters(){
			return $this->parameters;
		}

		/**
		 * @return mixed
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}

		/**
		 * @param $definition
		 * @return $this
		 */
		public function setDefinition($definition){
			$this->definition = $definition;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getDefinition(){
			return $this->definition;
		}

		/**
		 * @param bool|true $shared
		 * @return $this
		 */
		public function setShared($shared = true){
			$this->shared = $shared;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isShared(){
			return $this->shared;
		}

		/**
		 * @return mixed
		 */
		public function getSharedInstance(){
			return $this->shared_instance;
		}
	}
}

