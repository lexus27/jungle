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

		const DI_OFFSET_FIRST = -1;
		const DI_OFFSET_LAST = 1;

		/** @var  string */
		protected $name;

		/** @var  mixed */
		protected $definition;

		/** @var array  */
		protected $arguments = [];

		/** @var int  Service::DI_OFFSET_LAST | Service::DI_OFFSET_FIRST */
		protected $arguments_di_pos = self::DI_OFFSET_LAST;

		/** @var  callable */
		protected $arguments_fn;

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
		 * @param array|null $arguments
		 * @return mixed
		 */
		public function resolve(DiInterface $di, array $arguments = null){
			if($this->shared){
				if(!$this->shared_instance){
					$this->shared_instance = $this->_build($di,$this->arguments);
				}
				return $this->shared_instance;
			}else{
				return $this->_build($di,$arguments);
			}
		}

		/**
		 * @param array $externalArguments
		 * @param $di
		 * @return array
		 */
		protected function _prepareArguments($externalArguments, $di){
			$externalArguments = (array) $externalArguments;
			if($this->arguments_fn){
				$arguments = call_user_func($this->arguments_fn, $this->arguments, $externalArguments, $di, $this->arguments_di_pos);
			}else{
				$arguments = array_replace($this->arguments, $externalArguments);
				if($this->arguments_di_pos === self::DI_OFFSET_LAST){
					$arguments[] = $di;
				}
				if($this->arguments_di_pos === self::DI_OFFSET_FIRST){
					array_unshift($arguments,$di);
				}
			}
			return $arguments;
		}

		/**
		 * @param DiInterface $di
		 * @param array|null $arguments
		 * @return mixed
		 * @throws \Exception
		 */
		protected function _build(DiInterface $di, array $arguments = null){
			if(is_object($this->definition)){
				$object = $this->definition;
			}elseif(is_callable($this->definition)){
				$object = call_user_func_array($this->definition,$this->_prepareArguments($arguments, $di));
			}elseif(is_array($this->definition)){
				$definition = array_replace([
				    'class' => null,
				    'staticFactory' => null,
				],$this->definition);

				$class = $definition['class'];
				if(!$class){
					throw new \Exception('Service "'.$this->name.'" definition className not supplied!');
				}
				if(!class_exists($class)){
					throw new \Exception('Service "'.$this->name.'" class "'.$class.'" not found!');
				}

				$arguments = isset($this->definition['arguments']) && is_array($this->definition['arguments'])?
					array_replace($this->definition['arguments'],$arguments) : $arguments ;
				$arguments = $this->_prepareArguments($arguments, $di);

				if($definition['staticFactory']){
					$object = call_user_func_array([$class,$definition['staticFactory']],$arguments);
				}else{
					$object = new $class(...$arguments);
				}
			}else{
				throw new \Exception('Service "'.$this->name.'" is not valid service definition!');
			}
			if($object instanceof InjectionAwareInterface){
				$object->setDi(
					$di->getRoot()
				);
			}
			return $object;
		}

		/**
		 * @param $position
		 * @param $value
		 * @return $this
		 */
		public function setArgument($position, $value){
			$this->arguments[$position] = $value;
			return $this;
		}

		/**
		 * @param $position
		 * @return null
		 */
		public function getArgument($position){
			return isset($this->arguments[$position])?$this->arguments[$position]:null;
		}

		/**
		 * @return array
		 */
		public function getArguments(){
			return $this->arguments;
		}

		/**
		 * @param callable|null $argumentsFn
		 * @return $this
		 */
		public function setArgumentsFn(callable $argumentsFn = null){
			$this->arguments_fn = $argumentsFn;
			return $this;
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

