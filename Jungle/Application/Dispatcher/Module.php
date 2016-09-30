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
	use Jungle\Application\Dispatcher\Exception\Control;
	use Jungle\Application\Dispatcher\Process;
	use Jungle\Application\Dispatcher\Process\ProcessInitiatorInterface;
	use Jungle\Application\Notification\Responsible\AuthenticationMissed;
	use Jungle\Application\Notification\Responsible\NeedIntroduce;
	use Jungle\Di;
	use Jungle\Di\InjectionAwareInterface;
	use Jungle\Di\InjectionAwareTrait;
	use Jungle\FileSystem;
	use Jungle\Loader;
	use Jungle\Util\Data\Validation\Message\ValidationCollector;
	use Jungle\Util\Data\Validation\Message\ValidatorMessage;
	use Jungle\Util\Value\Massive;

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
			if(is_null($this->controller_suffix) && $this->dispatcher){
				return $this->dispatcher->getActionSuffix();
			}
			return $this->action_suffix;
		}

		/**
		 * @param \Exception $e
		 * @param \Jungle\Application\Dispatcher\Process $process
		 * @param $result
		 * @return bool
		 * @throws \Exception
		 */
		protected function interceptException(\Exception $e, Process $process, $result){
			if($e instanceof ValidatorMessage || $e instanceof ValidationCollector){
				$process->setTask('validation', $e);
			}
			if($e instanceof AuthenticationMissed){
				$process->setTask('authentication', $e);
			}
			if($e instanceof NeedIntroduce){
				$process->setTask('introduce', $e);
			}

			if(!$process->hasTasks()) throw $e;
			else return true;
		}

		/**
		 * @param array|null $reference
		 * @param array $data
		 * @param array $options
		 * @param \Jungle\Application\Dispatcher\Process\ProcessInitiatorInterface|null $initiator
		 * @return mixed
		 * @throws Control
		 */
		public function control(array $reference = null,array $data, array $options = null, ProcessInitiatorInterface $initiator = null){

			$reference = array_replace([
				'module'     => $this->name,
				'controller' => $this->getDefaultController(),
				'action'     => $this->getDefaultAction()
			],(array)$reference);

			list($controllerName, $actionName) = Massive::orderedKeys($reference, ['controller','action']);

			$controllerQualified = $this->getQualifiedReferenceString($reference,false);
			$referenceString = $this->appendQualifiedAction($controllerQualified,$reference);
			$controller = $this->loadController($controllerName);

			if($controller instanceof Dispatcher\ControllerManuallyInterface){

				if(!$controller->has($actionName)){
					throw new Control('Action not found: ' . $referenceString);
				}

				$result = null;
				$process = $this->factoryProcess($this->dispatcher, $controller, $data, $reference, $initiator);
				$this->getAttachedDi()->set('process', $process, true);
				try{
					$process->startOutputBuffering();
					if($this->beforeControl($process)===false){
						$process->cancel();
						goto checkout;
					}
					if($process->isCanceled()){
						goto checkout;
					}
					$result = $controller->call($actionName, $process);
					$process->setResult($result,true);
					$this->afterControl($process,$result);
				}catch(\Exception $e){
					if($controller->intercept($actionName, $e, $process, $result)!==true){
						$this->interceptException($e, $process, $result);
					}
				}finally{
					$process->endOutputBuffering();
				}

			}else{

				$actionMethod = $this->prepareActionMethodName($actionName);
				if(!method_exists($controller,$actionMethod)){
					throw new Control('Action not found: ' . $referenceString);
				}

				$result = null;
				$process = $this->factoryProcess($this->dispatcher, $controller, $data, $reference, $initiator);
				try{
					$process->startOutputBuffering();

					if($this->beforeControl($process)===false){
						$process->cancel();
					}
					if(method_exists($controller, 'beforeControl')){
						if($controller->beforeControl($actionName, $process)===false){
							$process->cancel();
							goto checkout;
						}
					}
					$beforeConcreteAction = $actionName.'BeforeControl';
					if(method_exists($controller, $beforeConcreteAction) && ($controller->{$beforeConcreteAction}($process) === false)){
						$process->cancel();
						goto checkout;
					}

					if($process->isCanceled()){
						goto checkout;
					}

					$result = $controller->{$actionMethod}($process);
					$process->setResult($result,true);

					$afterControlConcrete = $actionName.'AfterControl';
					if(method_exists($controller, $afterControlConcrete)){
						$controller->{$afterControlConcrete}($process);
					}
					if(method_exists($controller, 'afterControl')){
						$controller->afterControl($actionName, $result,$process);
					}
					$this->afterControl($process, $result);

				}catch(\Exception $e){
					$exceptionControlMethod = $actionName.'InterceptException';
					if(method_exists($controller, $exceptionControlMethod)){
						if($controller->{$exceptionControlMethod}($e, $process, $result)===true){
							return $process;
						}
					}
					$exceptionControlMethod = 'InterceptException';
					if(method_exists($controller, $exceptionControlMethod)){
						if($controller->{$exceptionControlMethod}($actionName, $e, $process, $result)===true){
							return $process;
						}
					}
					$this->interceptException($e, $process, $result);
				}finally{
					$process->endOutputBuffering();
				}

			}
			checkout:
			return $this->checkoutProcess($process, $options);
		}

		/**
		 * @param ProcessInterface $process
		 * @param array|null $options
		 * @return bool
		 */
		public function checkoutProcess(ProcessInterface $process,array $options = null){
			return $process;
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
			return $qualified.($reference['action']?':'.$reference['action']:'');
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
		 * @param $controllerName
		 * @param $actionName
		 * @return array
		 */
		public function getMetadata($controllerName, $actionName){
			$controllerName = $controllerName?:$this->default_controller;
			$actionName = $actionName?:$this->default_action;
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
				$this->metadata_cache[$cache_key] = $metadata;
				return $metadata;
			}
			return $this->metadata_cache[$cache_key];
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
			return $this->dispatcher->getDi();
		}

		/**
		 * @param $dispatcher
		 * @param $controller
		 * @param $params
		 * @param $reference
		 * @param $initiator
		 * @return \Jungle\Application\Dispatcher\Process
		 * @throws Exception
		 */
		protected function factoryProcess($dispatcher, $controller, $params, $reference, $initiator){
			if(method_exists($controller, 'factoryProcess')){
				$process = call_user_func([$controller, 'factoryProcess'],$dispatcher, $this, $params, $reference, $initiator);
				if($process){
					if(!$process instanceof ProcessInterface){
						throw new Exception('Controller '.get_class($controller) . '::factoryProcess return value is not instanceof "'.ProcessInterface::class.'"');
					}
					return $process;
				}
			}
			return new Process($dispatcher, $controller, $params, $reference, $this, $initiator);
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

	}
}

