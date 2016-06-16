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

	use ControllerInterface;
	use Jungle\Application\Dispatcher;
	use Jungle\Application\Dispatcher\Controller\ControllerManuallyInterface;
	use Jungle\Application\Dispatcher\Controller\Process;
	use Jungle\Application\Dispatcher\Controller\ProcessInitiatorInterface;
	use Jungle\Application\Dispatcher\Controller\ProcessInterface;
	use Jungle\Application\Dispatcher\Exception\Control;
	use Jungle\Loader;
	use Jungle\Util\Value\Massive;

	/**
	 * Class Module
	 * @package Jungle\Application\Dispatcher
	 */
	abstract class Module implements ModuleInterface{

		/** @var  string */
		protected $name;

		/** @var  string */
		protected $controller_namespace;

		/** @var  string */
		protected $controllerSuffix = 'Controller';

		/** @var  string */
		protected $actionSuffix = 'Action';

		/** @var  string */
		protected $defaultController = 'index';

		/** @var  string */
		protected $defaultAction = 'index';

		/** @var  object[]|ControllerInterface[]|ControllerManuallyInterface[]  */
		protected $controllers = [];

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

		public function initialize(){}





		public function getCacheDirname(){
			// TODO: Implement getCacheDirname() method.
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
			$this->defaultController = $controller;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getDefaultController(){
			return $this->defaultController;
		}

		/**
		 * @param string $action
		 * @return $this
		 */
		public function setDefaultAction($action){
			$this->defaultAction = $action;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getDefaultAction(){
			return $this->defaultAction;
		}

		/**
		 * @param $suffix
		 * @return mixed
		 */
		public function setControllerSuffix($suffix){
			$this->controllerSuffix = $suffix;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getControllerSuffix(){
			return $this->controllerSuffix;
		}

		/**
		 * @param $suffix
		 * @return mixed
		 */
		public function setActionSuffix($suffix){
			$this->actionSuffix = $suffix;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getActionSuffix(){
			return $this->actionSuffix;
		}

		/**
		 * @param Dispatcher $dispatcher
		 * @param array $data
		 * @param array|null $reference
		 * @param ProcessInitiatorInterface|null $initiator
		 * @return mixed
		 * @throws Exception
		 */
		public function execute(Dispatcher $dispatcher, array $data, $reference = null,ProcessInitiatorInterface $initiator = null){
			$reference = $dispatcher->normalizeReference($reference);
			list($controllerName, $actionName) = Massive::orderedKeys($reference, ['controller','action']);

			$controllerQualified = $this->getQualifiedReferenceString($reference,false);
			$referenceString = $this->appendQualifiedAction($controllerQualified,$reference);

			if(!isset($this->controllers[$controllerName])){
				$className = $this->prepareControllerClassName($controllerName);
				if(!class_exists($className)){
					throw new Control('Controller with name: '.$controllerQualified.' not found (class: '.$className.')');
				}
				$controller = new $className();
				if(method_exists($controller,'initialize')){
					$controller->initialize();
				}
				$this->controllers[$controllerName] = $controller;
			}else{
				$controller = $this->controllers[$controllerName];
			}

			$actionMethod = $this->prepareActionMethodName($actionName);

			if($controller instanceof Dispatcher\Controller\ControllerManuallyInterface){

				$process = $this->factoryProcess($dispatcher, $controller, $data, $reference, $initiator);

				if(!$controller->has($actionName)){
					throw new Control('Action not found: ' . $referenceString);
				}

				if($dispatcher->beforeControl($process)===false){
					return false;
				}
				if($this->beforeControl($process)===false){
					return false;
				}
				$result = $controller->call($actionName, $process);
				if(!$process->isCompleted()){
					$process->setResult($result,true);
				}
				$this->afterControl($process,$result);
				$dispatcher->afterControl($process,$result);

				return $process;
			}elseif(method_exists($controller,$actionMethod)){

				$process = $this->factoryProcess($dispatcher, $controller, $data, $reference, $initiator);

				if($dispatcher->beforeControl($process)===false){
					return false;
				}
				if($this->beforeControl($process)===false){
					return false;
				}
				if(method_exists($controller, 'beforeControl')){
					if($controller->beforeControl($actionName, $process)===false){
						return false;
					}
				}

				$beforeConcreteAction = $actionName.'BeforeControl';
				if(method_exists($controller, $beforeConcreteAction) && ($controller->{$beforeConcreteAction}($process) === false)){
					return false;
				}

				$result = $controller->{$actionMethod}($process);

				$process->setResult($result);
				if(!$process->isCompleted()){
					$process->setResult($result,true);
				}

				$afterControlConcrete = $actionName.'AfterControl';
				if(method_exists($controller, $afterControlConcrete) && ($controller->{$afterControlConcrete}($process) === false)){
					return false;
				}
				if(method_exists($controller, 'afterControl')){
					$controller->afterControl($actionName, $result,$process);
				}
				$this->afterControl($process, $result);
				$dispatcher->afterControl($process, $result);

				return $process;
			}else{
				throw new Control('Action not found: ' . $referenceString);
			}
		}

		
		/**
		 * @param ProcessInterface $process
		 */
		protected function beforeControl($process){
			
		}
		
		/**
		 * @param ProcessInterface $process
		 * @param mixed $result
		 */
		protected function afterControl($process, $result){
			
		}
		
		
		/**
		 * @param $reference
		 * @param bool $withAction
		 * @return string
		 */
		protected function getQualifiedReferenceString($reference,$withAction = true){
			return ($reference['module']?'#'.$reference['module']:'').
				   ($reference['controller']?':'.$reference['controller']:'').
				   ($withAction && $reference['action']?':'.$reference['action']:'');
		}

		/**
		 * @param $qualified
		 * @param $reference
		 * @return string
		 */
		protected function appendQualifiedAction($qualified, $reference){
			return $qualified. ($reference['action']?':'.$reference['action']:'');
		}



		/**
		 * @param $controllerName
		 * @return bool
		 * @throws Exception
		 */
		public function hasController($controllerName){
			if(!isset($this->controllers[$controllerName])){
				$className = $this->prepareControllerClassName($controllerName);
				if(!class_exists($className)){
					return false;
				}
				return true;
			}else{
				return true;
			}
		}

		/**
		 * @param $controllerName
		 * @return bool
		 * @throws Exception
		 */
		public function loadController($controllerName){
			if(!isset($this->controllers[$controllerName])){
				$className = $this->prepareControllerClassName($controllerName);
				if(!class_exists($className)){
					return null;
				}
				$controller = new $className();
				if(method_exists($controller,'initialize')){
					$controller->initialize();
				}
				$this->controllers[$controllerName] = $controller;
				return $controller;
			}else{
				return $this->controllers[$controllerName];
			}
		}

		/**
		 * @param $controllerName
		 * @param $actionName
		 * @return bool
		 */
		public function hasControllerAction($controllerName, $actionName){
			$controller = $this->loadController($controllerName);
			if($controller){
				if($controller instanceof Dispatcher\Controller\ControllerManuallyInterface){
					return $controller->has($actionName);
				}else{
					return method_exists($controller, $this->prepareActionMethodName($actionName));
				}
			}else{
				return false;
			}
		}


		/**
		 * @param $controllerName
		 * @return string
		 * @throws Exception
		 */
		public function prepareControllerClassName($controllerName){
			if(strpos($controllerName,'.')!==false){
				$controllerName = explode('.',$controllerName);
				foreach($controllerName as $i => $chunk){
					if(!$chunk){
						throw new Exception('Error qualified controller name');
					}
					$controllerName[$i] = ucfirst($chunk);
				}
				$controllerName = implode('\\',$controllerName);
			}else{
				$controllerName = ucfirst($controllerName);
			}
			return $this->getControllerNamespace() . '\\' . $controllerName . $this->getControllerSuffix();
		}

		/**
		 * @param $actionName
		 * @return string
		 */
		public function prepareActionMethodName($actionName){
			return $actionName . $this->getActionSuffix();
		}
		
		/**
		 * Анализ структуры модуля
		 * Анализ контроллеров и их действий
		 * Анализ параметров контроллера и их действий
		 */
		public function analyzeStructure(){
			
		}
		
		/**
		 * @param $reference
		 * @return bool
		 */
		public function supportPublic($reference){
			
		}
		
		/**
		 * @param $reference
		 * @return bool
		 */
		public function supportHierarchy($reference){
			
		}


		/**
		 * @param $reference
		 * @return bool
		 */
		public function supportFormatting($reference){

		}
		
		/**
		 *
		 */
		public function getControllerNames(){
			$loader = Loader::getDefault();
			$controllerNamespaceName = $this->getControllerNamespace();
			$basedir = $loader->getInNamespacePath($controllerNamespaceName);
			$container = $this->scanClasses($basedir, null);
			$controllers = [];

			$controllerSuffix = $this->getControllerSuffix();
			foreach($container as $className => $path){
				if(fnmatch('*' . $controllerSuffix, $className)){
					$controllerName = basename($className,$controllerSuffix);
					$namespaceName = dirname($className);
					$namespaceName = $namespaceName && $namespaceName !=='.'?$namespaceName:null;
					$controllerFullName = strtolower(($namespaceName?$namespaceName.'.':'') . $controllerName);
					//$controllers[$controllerFullName] =  ($controllerNamespaceName?$controllerNamespaceName.'\\':'').$className;
					$controllers[] = $controllerFullName;
				}
			}
			return $controllers;
		}

		/**
		 * @param $basedir
		 * @param null $ns
		 * @param array $container
		 * @return array
		 */
		protected function scanClasses($basedir, $ns = null, & $container = []){
			foreach(glob($basedir . DIRECTORY_SEPARATOR . '*') as $path){
				if(is_dir($path)){
					$namespaceName = ($ns?$ns.'\\':'').basename($path);
					$this->scanClasses($path,$namespaceName, $container);
				}else{
					$extension = pathinfo($path,PATHINFO_EXTENSION);
					if(strcasecmp($extension,'php')===0){
						$className = ($ns?$ns.'\\':'').pathinfo($path,PATHINFO_FILENAME);
						$container[$className] = $path;
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
				'name' => null,
				'namespace' => null,
				'suffixes' => [
					'controller' => null,
					'action' => null
				],
				'reference' => [
					'controller' => null,
					'action' => null
				]
			],$definition);

			if($definition['name']){
				$this->setName($definition['name']);
			}
			if($definition['namespace']){
				$this->setControllerNamespace($definition['namespace']);
			}

			if($definition['reference']['controller']){
				$this->setDefaultController($definition['reference']['controller']);
			}
			if($definition['reference']['action']){
				$this->setDefaultAction($definition['reference']['action']);
			}

			if($definition['suffixes']['controller']){
				$this->setControllerSuffix($definition['reference']['action']);
			}
			if($definition['suffixes']['action']){
				$this->setActionSuffix($definition['reference']['action']);
			}


			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getDi(){
			// TODO: Implement getDi() method.
		}

		/**
		 * @param $dispatcher
		 * @param $controller
		 * @param $params
		 * @param $reference
		 * @param $initiator
		 * @return Process
		 */
		protected function factoryProcess($dispatcher, $controller, $params, $reference, $initiator){
			return new Process($dispatcher, $controller, $params, $reference, $this, $initiator);
		}

	}
}

