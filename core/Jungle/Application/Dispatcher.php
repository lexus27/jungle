<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.04.2016
 * Time: 21:42
 */
namespace Jungle\Application {

	use Jungle\Application;
	use Jungle\Application\Dispatcher\Controller\Process;
	use Jungle\Application\Dispatcher\Controller\ProcessInitiatorInterface;
	use Jungle\Application\Dispatcher\Controller\ProcessInterface;
	use Jungle\Application\Dispatcher\Exception;
	use Jungle\Application\Dispatcher\Exception\Control;
	use Jungle\Application\Dispatcher\Module;
	use Jungle\Application\Dispatcher\ModuleInterface;
	use Jungle\Application\Dispatcher\Router;
	use Jungle\Application\Dispatcher\Router\Exception\NotFound;
	use Jungle\Application\Dispatcher\RouterInterface;
	use Jungle\Application\View;
	use Jungle\Di\Injectable;
	use Jungle\Di\InjectionAwareInterface;
	use Jungle\FileSystem;

	/**
	 * Class Dispatcher
	 * @package Jungle\Application
	 */
	class Dispatcher extends Injectable{

		/** @var  RouterInterface[] */
		protected $routers = [];

		/** @var  ModuleInterface[]|array[]  */
		protected $modules = [];

		/** @var  string */
		protected $default_module = 'index';

		/** @var  string */
		protected $default_controller = 'index';

		/** @var  string */
		protected $default_action = 'index';

		/** @var  string */
		protected $action_suffix = 'Action';

		/** @var  string */
		protected $controller_suffix = 'Controller';

		/** @var  bool  */
		protected $dispatching = false;

		/** @var  RequestInterface */
		protected $dispatching_request;

		/**
		 * @param $name
		 * @return $this
		 */
		public function setDefaultModule($name){
			$this->default_module = $name;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getDefaultModule(){
			return $this->default_module;
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setDefaultController($name){
			$this->default_controller = $name;
			return $this;
		}
		/**
		 * @return string
		 */
		public function getDefaultController(){
			return $this->default_controller;
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setDefaultAction($name){
			$this->default_action = $name;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getDefaultAction(){
			return $this->default_action;
		}


		/**
		 * @param ModuleInterface $module
		 * @param bool $check
		 * @return $this
		 */
		public function addModule(ModuleInterface $module, $check = true){
			if($check){
				$name = $module->getName();
				foreach($this->modules as $index => $m){
					if($m->getName() === $name){
						throw new \LogicException('Module "'.$name.'" already exists!');
					}
				}
			}
			$this->modules[$module->getName()] = $module;
			return $this;
		}

		/**
		 * @param $moduleName
		 * @return null|ModuleInterface
		 */
		public function getModule($moduleName){
			if(isset($this->modules[$moduleName])){
				$module = $this->modules[$moduleName];
				if($module instanceof ModuleInterface){
					return $module;
				}else{
					$module = $this->_loadModule($moduleName,$module);
					$this->modules[$moduleName] = $module;
					return $module;
				}
			}
			return null;
		}

		/**
		 * @param array $modules key pairs assoc
		 * @param bool|false $merge
		 * @return $this
		 */
		public function registerModules(array $modules, $merge = false){
			foreach($modules as $moduleName => & $moduleDefinition){
				if($moduleDefinition instanceof ModuleInterface){
					$moduleDefinition->setName($moduleName);
				}elseif(is_string($moduleDefinition)){
					$moduleDefinition = [
						'class' => $moduleDefinition
					];
				}
			}
			$this->modules = $merge?array_replace($this->modules,$modules):$modules;
			return $this;
		}

		/**
		 * @param $module
		 * @return $this
		 */
		public function removeModule($module){
			if(isset($this->modules[$module])){
				unset($this->modules[$module]);
			}
			return $this;
		}

		/**
		 * @param $alias
		 * @param RouterInterface $router
		 * @return $this
		 */
		public function addRouter($alias, RouterInterface $router){
			$this->routers[$alias] = $router;
			return $this;
		}

		/**
		 * @param RouterInterface $router
		 * @return string
		 */
		public function searchRouter(RouterInterface $router){
			return array_search($router,$this->routers,true);
		}

		/**
		 * @param $alias
		 * @return RouterInterface|null
		 */
		public function getRouter($alias){
			return isset($this->routers[$alias])?$this->routers[$alias]:null;
		}

		/**
		 * @param string $alias
		 * @return $this
		 */
		public function removeRouter($alias){
			unset($this->routers[$alias]);
			return $this;
		}

		/**
		 * @param RequestInterface $request
		 * @return RouterInterface|null
		 */
		public function getDesiredRouter(RequestInterface $request){
			foreach($this->routers as $name => $router){
				if($router->isDesiredRequest($request)){
					$router->setBeforeRouteMatchedChecker([$this,'beforeRouteMatched']);
					return $router;
				}
			}
			return null;
		}

		/**
		 * @param $route
		 * @param $reference
		 * @param $routing
		 * @return bool|mixed
		 */
		public function beforeRouteMatched($route, $reference, $routing){
			$reference = self::normalizeReference($reference,null,false);
			$moduleName = $reference['module']?:$this->getDefaultModule();
			$module = $this->getModule($moduleName);
			if(!$module){
				return true;
			}
			return $module->supportPublic($reference['controller'],$reference['action']);
		}

		/**
		 * @param RequestInterface $request
		 * @return ResponseInterface
		 * @throws Control
		 * @throws Exception
		 * @throws \Jungle\Application\Dispatcher\Router\Exception\NotFound
		 */
		public function dispatch(RequestInterface $request){
			if($this->dispatching){
				throw new \LogicException('dispatch already run!');
			}
			try{
				if($this->_beforeDispatch($request) !== false){
					$this->_onDispatchStarted($request);
					$router = $this->getDesiredRouter($request);
					if(!$router){
						throw new Exception('Not found desired Router by Request!');
					}
					if($this->_dependency_injector){
						$this->_dependency_injector->setShared('router',$router);
					}
					$routing = $router->match($request);
					if($routing->isUnknown()){
						throw new NotFound('Not Found Affected Routes!');
					}else{
						$process = $this->control($routing->getReference(), $routing->getParams(), null, $routing);
					}
					$response = $this->prepareResponse($process);
					$this->_afterDispatch($request,$routing,$process);
					if($this->_dependency_injector){
						$this->_dependency_injector->remove('router');
					}
					return $response;
				}
				return null;
			}finally{
				$this->_onDispatchContinue();
			}
		}



		/**
		 * @param $reference
		 * @param $data
		 * @param $options
		 * @param null|ProcessInitiatorInterface|ProcessInterface|Router\Routing $initiator
		 * @return Process|mixed
		 * @throws Control
		 */
		public function control($reference,$data = null,array $options = null, ProcessInitiatorInterface $initiator = null){
			$reference = self::normalizeReference($reference,[
				'module' => $this->default_module
			]);
			$moduleName = $reference['module'];
			$module = $this->getModule($moduleName);
			if(!$module){
				throw new Control('Module "'.$moduleName.'" not found!');
			}
			$this->_beforeControl($reference, $module, $initiator);
			$process = $module->control($reference,(array) $data, $options, $initiator);
			$output = $this->checkoutProcess($process, $options);
			$this->_afterControl($output,$process);
			return $output;
		}


		/**
		 * @param ProcessInterface $process
		 * @param array|null $options
		 * @return ProcessInterface|mixed
		 */
		public function checkoutProcess(ProcessInterface $process,array $options = null){
			if($options){
				if(isset($options['render'])){
					return $this->view->render($process,$options['render']);
				}
				if(isset($options['result']) && $options['result']){
					return $process->getResult();
				}
				if(isset($options['buffer']) && $options['buffer']){
					return $process->getOutputBuffer();
				}
			}
			return $process;
		}

		/**
		 * @param Process $process
		 * @return ResponseInterface
		 */
		public function prepareResponse(Process $process){
			$response = $this->dispatching_request->getResponse();
			if($response->getContent() === null){
				$response->setContent($this->view->render($process));
			}
			return $response;
		}

		/**
		 * @param $controller
		 */
		public function prepareControllerBeforeInitialize($controller){
			if($controller instanceof InjectionAwareInterface){
				$controller->setDi($this->getDi());
			}
		}



		/**
		 * @param $process
		 * @return bool|void
		 */
		public function beforeControl(ProcessInterface $process){ }

		/**
		 * @param $process
		 * @param $result
		 */
		public function afterControl(ProcessInterface $process, $result){ }


		/**
		 * @param $suffix
		 * @return $this
		 */
		public function setActionSuffix($suffix){
			$this->action_suffix = $suffix;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getActionSuffix(){
			return $this->action_suffix;
		}

		/**
		 * @param $suffix
		 * @return $this
		 */
		public function setControllerSuffix($suffix){
			$this->controller_suffix = $suffix;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getControllerSuffix(){
			return $this->controller_suffix;
		}



		/**
		 * @param $moduleName
		 * @param array $definition
		 * @return mixed
		 * @throws Exception
		 */
		protected function _loadModule($moduleName,array $definition){
			$definition = array_replace([
				'class' => Module\DynamicModule::class,
			],$definition);
			$className = $definition['class'];
			if(class_exists($className)){
				$module = new $className();
				if(!$module instanceof ModuleInterface){
					throw new Exception('Module instance is not '.ModuleInterface::class);
				}
				$module->fromArray($definition);
				$module->setName($moduleName);
				$module->initialize($this);
				return $module;
			}else{
				throw new Exception('Module load error: "'.$moduleName.'" module, not found module class "'.$className.'"');
			}
		}

		protected function _beforeDispatch(RequestInterface $request){

		}

		protected function _afterDispatch(RequestInterface $request,Router\RoutingInterface $routing, ProcessInterface $result){

		}

		/**
		 * @param RequestInterface $request
		 */
		protected function _onDispatchStarted(RequestInterface $request){
			if($this->_dependency_injector){
				$this->_dependency_injector->setShared('request',$request);
				$this->_dependency_injector->setShared('response',$request->getResponse());
			}
			$this->dispatching = true;
			$this->dispatching_request = $request;
		}

		/**
		 *
		 */
		protected function _onDispatchContinue(){
			if($this->_dependency_injector){
				$this->_dependency_injector->remove('request');
				$this->_dependency_injector->remove('response');
			}
			$this->dispatching = false;
			$this->dispatching_request = null;
		}



		/**
		 * @param $reference
		 * @param ModuleInterface $module
		 * @param null|ProcessInitiatorInterface|ProcessInterface|Router\Routing $initiator
		 * @throws Control
		 */
		protected function _beforeControl($reference, ModuleInterface $module, ProcessInitiatorInterface $initiator = null){
			if($initiator && $initiator instanceof ProcessInterface && !$module->supportHierarchy($reference['controller'],$reference['action'])){
				throw new Control("{$module->getName()}:{$reference['controller']}:{$reference['action']} not support hierarchy calling!");
			}
		}

		/**
		 * @param $output
		 * @param ProcessInterface $process
		 */
		protected function _afterControl($output,ProcessInterface $process){

		}

		/**
		 * @param $reference
		 * @param array|null $default_reference
		 * @param bool $finallyNormalize
		 * @return array
		 */
		public static function normalizeReference($reference = null,array $default_reference = null, $finallyNormalize = true){
			if($reference === null){
				$reference = [];
			}
			if(is_string($reference)){
				$module     = null;
				$controller = null;
				$action     = null;
				if(strpos($reference,':')!==false){
					$reference = explode(':',$reference);
					if(isset($reference[0])){
						if($reference[0]{0}==='#'){
							$module = substr($reference[0],1);
						}else{
							$controller = $reference[0];
						}
					}
					if(isset($reference[1])){
						if($controller!==null){
							$action = $reference[1];
						}else{
							$controller = $reference[1];
						}
					}
					if($action === null && isset($reference[2])){
						$action = $reference[2];
					}
				}else{
					$action = $reference;
				}

				if(strpos($action,'.')!==false){
					throw new \LogicException('Wrong string reference');
				}

				$reference = [
					'module'        => $module,
					'controller'    => $controller,
					'action'        => $action
				];
			}
			if($finallyNormalize){
				if($default_reference === null){
					$default_reference = [
						'module'		=> null,
						'controller'	=> null,
						'action'		=> null,
					];
				}
				foreach($default_reference as $k => $v){
					if(!isset($reference[$k])){
						$reference[$k] = $v;
					}
				}
			}
			return $reference;
		}

	}
}

