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
	
	use Jungle\Application\Dispatcher;
	use Jungle\Application\Dispatcher\Exception\Control;
	use Jungle\Application\Notification\Responsible\AccessDenied;
	use Jungle\Application\Strategy\Http\Router;
	use Jungle\Application\StrategyInterface;
	use Jungle\Di;
	use Jungle\Di\InjectionAwareInterface;
	use Jungle\Di\InjectionAwareTrait;
	use Jungle\Loader;
	
	/**
	 * Class Module
	 * @package Jungle\Application\Dispatcher
	 */
	abstract class Module extends Di implements InjectionAwareInterface, ModuleInterface{

		use InjectionAwareTrait;

		/** @var  string */
		protected $name;

		/** @var  Dispatcher */
		protected $dispatcher;

		/** @var  string */
		protected $controller_namespace;

		/** @var  string */
		protected $controller_suffix;

		/** @var  string */
		protected $action_suffix;

		/** @var  string */
		protected $default_controller;

		/** @var  string */
		protected $default_action;

		/** @var  object[]|ControllerInterface[]|ControllerManuallyInterface[]  */
		protected $controllers = [];

		/** @var  array */
		protected $default_metadata = [];

		/** @var  array  */
		protected $metadata_cache = [];

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

		/**
		 * @param Dispatcher $dispatcher
		 * @return void
		 */
		public function initialize(Dispatcher $dispatcher){
			$this->dispatcher = $dispatcher;
			$this->setDi($dispatcher->getDi());
		}

		/**
		 * @return Dispatcher
		 */
		public function getDispatcher(){
			return $this->dispatcher;
		}

		/**
		 *
		 */
		public function getMemory(){

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
			$this->default_controller = $controller;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getDefaultController(){
			if(!$this->default_controller && $this->dispatcher){
				return $this->dispatcher->getDefaultController();
			}
			return $this->default_controller;
		}

		/**
		 * @param string $action
		 * @return $this
		 */
		public function setDefaultAction($action){
			$this->default_action = $action;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getDefaultAction(){
			if(!$this->default_action && $this->dispatcher){
				return $this->dispatcher->getDefaultAction();
			}
			return $this->default_action;
		}

		/**
		 * @param $suffix
		 * @return mixed
		 */
		public function setControllerSuffix($suffix=null){
			$this->controller_suffix = $suffix;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getControllerSuffix(){
			if(is_null($this->controller_suffix) && $this->dispatcher){
				return $this->dispatcher->getControllerSuffix();
			}
			return $this->controller_suffix;
		}

		/**
		 * @return array
		 */
		public function getDefaultMetadata(){
			return $this->default_metadata;
		}

		/**
		 * @param array $meta
		 * @return $this
		 */
		public function setDefaultMetadata(array $meta){
			$this->default_metadata = $meta;
			return $this;
		}


		/**
		 * @param $suffix
		 * @return mixed
		 */
		public function setActionSuffix($suffix=null){
			$this->action_suffix = $suffix;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getActionSuffix(){
			if(is_null($this->action_suffix) && $this->dispatcher){
				return $this->dispatcher->getActionSuffix();
			}
			return $this->action_suffix;
		}

		/**
		 * @param array|null $reference
		 * @param array $params
		 * @param \Jungle\Application\Dispatcher\ProcessInitiatorInterface|null $initiator
		 * @param $initiator_type
		 * @param ProcessInitiatorInterface|null $forwarder
		 * @param array $options
		 * @return mixed
		 * @throws Control
		 * @throws Exception
		 * @throws \Exception
		 */
		public function control(array $reference,array $params, ProcessInitiatorInterface $initiator, $initiator_type,ProcessInitiatorInterface $forwarder = null, $options = null){
			$controller = $this->loadController($reference['controller']);
			$action = $reference['action'];
			if($controller instanceof ControllerManuallyInterface){

				if(!$controller->has($action)){
					throw new Control('mca path not found(on:action) ' . var_export($reference, true));
				}

				$result = null;
				$process = $this->factoryProcess($controller, $params, $reference, $initiator, $initiator_type, $forwarder);
				$this->dispatcher->storeProcess($process);
				try{
					$this->set('process', $process, true);
					$process->startBuffering();

					$process->setStage(Process::STAGE_PREPARE);
					$this->dispatcher->beforeControl($process);
					$this->beforeControl($process);

					$process->setStage(Process::STAGE_EXECUTE);
					$result = $controller->call($action, $process);
					$process->setResult($result,Process::STAGE_DONE,Process::STATE_SUCCESS);

					$this->afterControl($process,$result);
					$this->dispatcher->afterControl($process, $result);

				}catch(\Exception $e){
					if($this->_runExceptionIntercepting($action, $controller, $e, $process)===false){
						throw $e;
					}
				}finally{
					$process->endBuffering();
				}

			}else{

				$actionMethod = $this->prepareActionMethodName($action);
				if(!method_exists($controller,$actionMethod)){
					throw new Control('mca path not found(on:action) ' . var_export($reference, true));
				}

				$result = null;
				$process = $this->factoryProcess($controller, $params, $reference, $initiator, $initiator_type, $forwarder);
				$this->dispatcher->storeProcess($process);
				try{
					$this->set('process', $process, true);

					$process->startBuffering();
					$process->setStage(Process::STAGE_PREPARE);

					$this->dispatcher->beforeControl($process);
					$this->beforeControl($process);

					method_exists($controller, 'beforeControl')
						&& $controller->beforeControl($action, $process);

					method_exists($controller, ($m = $action.'BeforeControl'))
					   && $controller->{$m}($process);

					$process->setStage(Process::STAGE_EXECUTE);
					$result = $controller->{$actionMethod}($process);
					$process->setResult($result,Process::STAGE_DONE,Process::STATE_SUCCESS);

					method_exists($controller, ($m = $action.'AfterControl'))
						&& $controller->{$m}($process);

					method_exists($controller, 'afterControl')
						&& $controller->afterControl($action, $result,$process);

					$this->afterControl($process, $result);
					$this->dispatcher->afterControl($process, $result);
				}catch(\Exception $e){
					if($this->_runExceptionIntercepting($action, $controller, $e, $process)===false){
						throw $e;
					}
				}finally{
					$process->endBuffering();
				}

			}
			return $process;
		}

		/**
		 * @param $controllerName
		 * @param $actionName
		 * @return bool
		 */
		public function hasControl($controllerName, $actionName){
			$controllerName = $controllerName?:$this->default_controller;
			if(!($controller = $this->loadController($controllerName))){
				return false;
			}
			$actionName = $actionName?:$this->default_action;
			if($controller instanceof ControllerManuallyInterface){
				return $controller->has($actionName);
			}else{
				return method_exists($controller,$this->prepareActionMethodName($actionName));
			}
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
		 * @return ControllerInterface
		 * @throws Exception
		 */
		public function loadController($controllerName){
			if(!isset($this->controllers[$controllerName])){
				$className = $this->prepareControllerClassName($controllerName);
				if(!class_exists($className)){
					throw new Control('Controller with name: '.$this->name.':'.$controllerName.' not found (class: '.$className.')');
				}
				$controller = new $className();
				$this->dispatcher->prepareControllerBeforeInitialize($controller);
				if(method_exists($controller,'initialize')){
					$controller->initialize();
				}
				return $this->controllers[$controllerName] = $controller;
			}else{
				return $this->controllers[$controllerName];
			}
		}



		/**
		 * @param $controllerName
		 * @param $actionName
		 * @return array
		 */
		public function getMetadata($controllerName, $actionName){
			$cache_key = $controllerName.':'.$actionName;
			if(!isset($this->metadata_cache[$cache_key])){
				$controller = $this->loadController($controllerName);
				$metadata = (array)$this->getDefaultMetadata();
				if(method_exists($controller,'getDefaultMetadata')){
					$metadata = array_replace($metadata, (array)$controller->getDefaultMetadata());
				}
				if($controller instanceof ControllerManuallyInterface){
					$metadata = array_replace($metadata, (array)$controller->getActionMetadata($actionName));
				}else{
					$mName = $actionName.'Metadata';
					if(method_exists($controller,$actionName.'Metadata')){
						$metadata = array_replace($metadata, (array)$controller->$mName());
					}
				}
				$default = $this->dispatcher->getDefaultMetadata();
				$this->metadata_cache[$cache_key] = array_replace($default,$metadata);
				return $metadata;
			}
			return $this->metadata_cache[$cache_key];
		}


		
		/**
		 * Анализ структуры модуля
		 * Анализ контроллеров и их действий
		 * Анализ параметров контроллера и их действий
		 */
		public function analyzeStructure(){
			
		}

		/**
		 *
		 */
		public function getControllerNames(){
			/** @var Loader $loader */
			$loader = $this->loader;
			$controllerNamespaceName = $this->getControllerNamespace();
			$basedir = $loader->getPathnameByNamespace($controllerNamespaceName);
			$container = $loader->scanClasses($basedir, null);
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
		 * @param array $definition
		 * @return $this
		 */
		public function fromArray(array $definition){
			$definition = array_replace_recursive([
				'name'      => null,
				'namespace' => null,
				'suffixes'  => [
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
		 * @return Di
		 */
		public function getDi(){
			return $this->dispatcher->getDi();
		}

		/**
		 * @param $controller
		 * @param $params
		 * @param $reference
		 * @param $initiator
		 * @param $initiator_type
		 * @param $forwarder
		 * @return Process
		 * @throws Exception
		 */
		public function factoryProcess($controller, $params, $reference, $initiator, $initiator_type, $forwarder){
			if(method_exists($controller, 'factoryProcess')){
				$process = call_user_func([$controller, 'factoryProcess'],$this->dispatcher, $this, $params, $reference, $initiator,$initiator_type, $forwarder);
				if($process){
					if(!$process instanceof ProcessInterface){
						throw new Exception('Controller '.get_class($controller) . '::factoryProcess return value is not instanceof "'.ProcessInterface::class.'"');
					}
					return $process;
				}
			}
			return $this->dispatcher->factoryProcess($params, $reference, $this, $controller, $initiator, $initiator_type,$forwarder);
		}












		/** @var  \Jungle\Application\View\RendererInterface */
		protected $renderer;

		/** @var  string */
		protected $cache_dirname;

		/** @var  string */
		protected $view_dirname;

		/***
		 * @param $dirname
		 * @return $this
		 */
		public function setCacheDirname($dirname){
			$this->cache_dirname = $dirname;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getCacheDirname(){
			return $this->cache_dirname;
		}

		/**
		 * @param ProcessInterface $process
		 * @throws AccessDenied
		 */
		protected function beforeControl(ProcessInterface $process){}

		/**
		 * @param ProcessInterface $process
		 * @param mixed $result
		 */
		protected function afterControl(ProcessInterface $process, $result){}


		/**
		 * @param \Exception $e
		 * @param ProcessInterface $process
		 * @return bool
		 * @throws \Exception
		 */
		protected function interceptException(\Exception $e, ProcessInterface $process){}

		/**
		 * @param \Exception $e
		 * @param ProcessInterface $process
		 */
		protected function interceptedException(\Exception $e, ProcessInterface $process){}

		/**
		 * @param $actionName
		 * @param $controller
		 * @param \Exception $e
		 * @param ProcessInterface $process
		 * @return bool intercepted
		 */
		protected function _runExceptionIntercepting($actionName, $controller,\Exception $e,ProcessInterface $process){
			if($controller instanceof ControllerManuallyInterface){
				if($controller->interceptException($actionName, $e, $process) === true){
					$this->_handleExceptionIntercepted($actionName, $controller, $e, $process);
					return true;
				}
			}else{
				if(method_exists($controller, ($exceptionControlMethod = $actionName.'InterceptException'))
				   && $controller->{$exceptionControlMethod}($e, $process)===true){
					$this->_handleExceptionIntercepted($actionName, $controller, $e, $process);
					return true;
				}
				if(method_exists($controller, ($exceptionControlMethod = 'InterceptException'))
				   && $controller->{$exceptionControlMethod}($actionName, $e, $process)===true){
					$this->_handleExceptionIntercepted($actionName, $controller, $e, $process);
					return true;
				}
			}

			if($this->interceptException($e, $process)===true){
				$this->_handleExceptionIntercepted($actionName, $controller, $e, $process);
				return true;
			}

			if($this->dispatcher->interceptException($e, $process)===true){
				$this->_handleExceptionIntercepted($actionName, $controller, $e, $process);
				return true;
			}

			return false;
		}

		/**
		 * @param $actionName
		 * @param $controller
		 * @param \Exception $e
		 * @param ProcessInterface $process
		 * @return bool
		 */
		protected function _handleExceptionIntercepted($actionName, $controller,\Exception $e,ProcessInterface $process){
			if($controller instanceof ControllerManuallyInterface){
				$controller->interceptedException($actionName, $e, $process);
			}else{
				method_exists($controller, ($exceptionControlMethod = $actionName.'InterceptedException'))
				   && $controller->{$exceptionControlMethod}($e, $process);

				method_exists($controller, ($exceptionControlMethod = 'InterceptedException'))
				   && $controller->{$exceptionControlMethod}($actionName, $e, $process);
			}
			$this->interceptedException($e, $process);
			$this->dispatcher->interceptedException($e, $process);
		}



		/**
		 * @param $controllerName
		 * @return string
		 * @throws Exception
		 */
		protected function prepareControllerClassName($controllerName){
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
		protected function prepareActionMethodName($actionName){
			return $actionName . $this->getActionSuffix();
		}
		
		protected static $prepared_strategies = [];
		
		/**
		 * @param StrategyInterface $strategy
		 * @return void
		 */
		final public static function prepareDispatchToStrategy($moduleName, StrategyInterface $strategy, Dispatcher $dispatcher){
			$strategy->getName();
			$k = serialize([$moduleName, $strategy->getName()]);
			if(!in_array($k,self::$prepared_strategies,true)){
				static::_prepareDispatchStrategy($moduleName, $strategy, $dispatcher);
				self::$prepared_strategies[] = $k;
			}
		}
		
		/**
		 * @param $moduleName
		 * @param StrategyInterface $strategy
		 * @param Dispatcher $dispatcher
		 */
		protected static function _prepareDispatchStrategy($moduleName, StrategyInterface $strategy, Dispatcher $dispatcher){}
		
		/**
		 * self::httpAnyRoute($router, $moduleName, '/register', 'account:register',[]);
		 * self::httpAnyRoute($router, $moduleName, '/login', 'account:login',[]);
		 * self::httpAnyRoute($router, $moduleName, '/logout', 'account:logout',[]);
		 *
		 * @param Router $router
		 * @param $moduleName
		 * @param $pattern
		 * @param $referenceInModule
		 * @param array $options
		 */
		protected static function httpAnyRoute(Router $router, $moduleName, $pattern, $referenceInModule, array $options){
			$router->any("/{$moduleName}{$pattern}",array_replace([
				'reference' => "#{$moduleName}:{$referenceInModule}"
			], $options));
		}

	}
}

