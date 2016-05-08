<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.05.2016
 * Time: 15:10
 */
namespace Jungle\Application\Dispatcher {

	use Jungle\Loader;

	/**
	 * Class Module
	 * @package Jungle\Application\Dispatcher
	 */
	class Module implements ModuleInterface{

		/** @var  string */
		protected $name;

		/** @var  string */
		protected $controller_namespace;

		/** @var array  */
		protected $suffixes = [
			'controller' 	=> 'Controller',
			'action' 		=> 'Action'
		];

		/** @var array  */
		protected $default_reference = [
			'controller'	=> 'index',
			'action'		=> 'index'
		];

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}

		public function initialize(){

		}

		/**
		 * @param $namespace
		 * @return $this
		 */
		public function setControllerNamespace($namespace){
			$this->controller_namespace = $namespace;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getControllerNamespace(){
			return $this->controller_namespace;
		}

		/**
		 * @param string $controller
		 * @return $this
		 */
		public function setDefaultController($controller){
			$this->default_reference['controller'] = $controller;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getDefaultController(){
			return $this->default_reference['controller'];
		}

		/**
		 * @param string $action
		 * @return $this
		 */
		public function setDefaultAction($action){
			$this->default_reference['action'] = $action;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getDefaultAction(){
			return $this->default_reference['action'];
		}

		/**
		 * @param $suffix
		 * @return mixed
		 */
		public function setControllerSuffix($suffix){
			$this->suffixes['controller'] = $suffix;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getControllerSuffix(){
			return $this->suffixes['controller'];
		}

		/**
		 * @param $suffix
		 * @return mixed
		 */
		public function setActionSuffix($suffix){
			$this->suffixes['action'] = $suffix;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getActionSuffix(){
			return $this->suffixes['action'];
		}

		/**
		 * @param $params
		 * @param array|null $reference
		 * @return mixed
		 */
		public function execute(array $params, $reference = null){

		}

		/**
		 *
		 */
		public function getControllerNames(){
			$loader = Loader::getDefault();
			$basedir = $loader->getFilePathByNamespace($this->controller_namespace);
			if($basedir===null){

			}
			$container = $this->scanClasses($basedir, null);
			$controllers = [];
			foreach($container as $className => $path){
				if(fnmatch('*' . $this->getControllerSuffix(), $className)){
					$namespace = str_replace('\\','.',strstr($className, '\\',true));
					$namespaceName = $namespace?:null;
					$controllerName = basename($className,$this->getControllerSuffix());
					$controllerFullName = ($namespaceName? $namespaceName . '.' : '' ) . $controllerName;
					$controllers[$controllerFullName] = $className;
				}
			}
		}

		/**
		 * @param $basedir
		 * @param null $ns
		 * @param array $container
		 * @return array
		 */
		protected function scanClasses($basedir, $ns = null, $container = []){
			foreach(glob($basedir . DIRECTORY_SEPARATOR . '*') as $path){
				if(is_dir($path)){
					$this->scanClasses($path,$ns . '\\' . basename($path), $container);
				}else{
					$extension = pathinfo($path,PATHINFO_EXTENSION);
					if(strcasecmp($extension,'php')===0){
						$container[$ns . '\\' . pathinfo($path,PATHINFO_FILENAME)] = $path;
					}
				}
			}
			return $container;
		}

		/**
		 * @param array $definition
		 * @return $this
		 */
		public function fromArray(array $definition){
			$definition = array_replace_recursive([
				'name'		=> null,
				'namespace'	=> null,
				'suffixes'	=> [
					'controller' => 'Controller',
					'action'		=> 'Action'
				],
				'reference'	=> [
					'controller'	=> 'index',
					'action'		=> 'index'
				]
			],$definition);
			$this->name					= $definition['name'];
			$this->controller_namespace = $definition['namespace'];
			$this->default_reference	= $definition['reference'];
			$this->suffixes 			= $definition['suffixes'];
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getDi(){
			// TODO: Implement getDi() method.
		}
	}
}

