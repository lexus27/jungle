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

	use Jungle\Application\Dispatcher\Controller\ControllerInterface;
	use Jungle\Application\Dispatcher\Controller\Process;
	use Jungle\Application\Dispatcher\Controller\ProcessInitiatorInterface;
	use Jungle\Application\Dispatcher\Controller\ProcessInterface;
	use Jungle\Application\Dispatcher\Exception;
	use Jungle\Application\Dispatcher\Exception\Control;
	use Jungle\Application\Dispatcher\Exception\NotFound;
	use Jungle\Application\Dispatcher\ModuleInterface;
	use Jungle\Application\Dispatcher\Router;
	use Jungle\Application\Dispatcher\RouterInterface;
	use Jungle\Util\Value\Massive;

	/**
	 * Class Dispatcher
	 * @package Jungle\Application
	 */
	class Dispatcher{

		/** @var  Router */
		protected $router;

		/**
		 * @var array
		 */
		protected $routers = [];

		/**
		 * @var
		 */
		protected $router_recognizer;



		/**
		 * @var array
		 */
		protected $default_reference = [
			'module' 		=> null,
			'controller' 	=> 'index',
			'action' 		=> 'index'
		];

		/** @var  string */
		protected $controller_namespace;

		/** @var string  */
		protected $controller_suffix = 'Controller';

		/** @var string  */
		protected $action_suffix = 'Action';

		/** @var  array  */
		protected $controller_objects = [];


		/** @var  ModuleInterface[]  */
		protected $modules = [];


		/**
		 * @param ModuleInterface $module
		 * @param bool $check
		 * @return $this
		 */
		public function addModule(ModuleInterface $module, $check = true){
			if($check){
				$name = $module->getName();
				foreach($this->modules as $index => $module){
					if($module->getName() === $name){
						throw new \LogicException('Module "'.$name.'" already exists!');
					}
				}
			}
			$this->modules[] = $module;
			return $this;
		}

		/**
		 * @param $module
		 * @return null
		 */
		public function getModule($module){
			if(is_string($module)){
				$i = false;
				foreach($this->modules as $index => $module){
					if($module->getName() === $module){
						$i = $index;
						break;
					}
				}
			}else{
				$i = array_search($module,$this->modules, true);
			}
			if($i !== false){
				return $this->modules[$i];
			}
			return null;
		}

		/**
		 * @param $module
		 */
		public function removeModule($module){
			if(is_string($module)){
				$i = false;
				foreach($this->modules as $index => $module){
					if($module->getName() === $module){
						$i = $index;
						break;
					}
				}
			}else{
				$i = array_search($module,$this->modules, true);
			}

			if($i !== false){
				array_splice($this->modules, $i,1);
			}

		}

		/**
		 * @param RouterInterface $router
		 * @return $this
		 */
		public function setRouter(RouterInterface $router){
			$this->router = $router;
			return $this;
		}

		/**
		 * @return Router
		 */
		public function getRouter(){
			return $this->router;
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
		 * @param $suffix
		 * @return $this
		 */
		public function setControllerSuffix($suffix){
			$this->controller_suffix = $suffix;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getControllerSuffix(){
			return $this->controller_suffix;
		}

		/**
		 * @param $suffix
		 * @return $this
		 */
		public function setActionSuffix($suffix){
			$this->action_suffix = $suffix;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getActionSuffix(){
			return $this->action_suffix;
		}

		/**
		 * @param RequestInterface $request
		 * @return mixed
		 * @throws Control
		 * @throws NotFound
		 */
		public function dispatch(RequestInterface $request){
			$routing = $this->router->match($request);
			if($routing->isNotFound()){
				if($routing->isUnknown()){
					throw new NotFound('Not found route by request!');
				}else{
					return $this->control($routing->getReference(),$routing->getParams(), $routing);
				}
			}else{
				return $this->control($routing->getReference(),$routing->getParams(), $routing);
			}
		}


		/**
		 * @param $reference
		 * @param $params
		 * @param null|ProcessInitiatorInterface|ProcessInterface|Router\Routing $initiator
		 * @return mixed
		 * @throws Control
		 * @throws Exception
		 */
		public function control($reference, $params,ProcessInitiatorInterface $initiator = null){
			$reference = $this->_normalizeReference($reference);
			list($m,$c,$a) = Massive::orderedKeys($reference,['module','controller','action']);

			$controllerQualified = $this->getQualifiedReferenceString($reference,false);
			$referenceString = $this->appendQualifiedAction($controllerQualified,$reference);

			if(!isset($this->controller_objects[$controllerQualified])){
				$controller_class = $this->_prepareControllerClassName($c);
				if(class_exists($controller_class)){
					$controller = new $controller_class;
					if(method_exists($controller, 'initialize')){
						$controller->initialize();
					}
					$this->controller_objects[$controllerQualified] = $controller;
				}else{
					throw new Control('Controller with name: '.$referenceString.' not found (class: '.$controller_class.')');
				}
			}



			$controller = $this->controller_objects[$controllerQualified];

			$process = new Process($this, $controller, $params, $reference,$initiator);

			$action_method = $this->_prepareActionMethodName($a);
			if($controller instanceof ControllerInterface){
				return $this->_controlControllerInterface($process, $c, $a, $controller);
			}elseif(method_exists($controller,$action_method)){
				return $this->_controlNativeObject($process, $c, $a, $action_method, $controller);
			}else{
				throw new Control('Action not found: ' . $referenceString);
			}
		}

		/**
		 * @param $reference
		 * @return array
		 */
		protected function _normalizeReference($reference){
			if(!is_array($reference)){
				$reference = [];
			}
			foreach($this->default_reference as $k => $v){
				if(!isset($reference[$k])){
					$reference[$k] = $v;
				}
			}
			return array_map('strtolower',$reference);
		}

		/**
		 *
		 * Call:
		 *
		 * 		beforeControl					system
		 * 			beforeControl				controller
		 * 				beforeControl{Action}	controller
		 *
		 * 					execution
		 *
		 * 				afterControl{Action}	controller
		 * 			afterControl				controller
		 * 		afterControl					system
		 *
		 * @param ProcessInterface $process
		 * @param $controllerName
		 * @param $actionName
		 * @param $actionMethod
		 * @param $controller
		 * @return bool
		 */
		protected function _controlNativeObject(ProcessInterface $process, $controllerName, $actionName, $actionMethod, $controller){

			$beforeControlMethod = 'beforeControl';
			$beforeControlActionMethod = $beforeControlMethod . $actionName;
			if($this->_beforeControl($process, $controllerName, $actionName, $controller) === false) return false;
			if(method_exists($controller, $beforeControlMethod)){
				if($controller->{$beforeControlMethod}($process, $actionName, $controllerName) === false) return false;
			}
			if(method_exists($controller, $beforeControlActionMethod)){
				if($controller->{$beforeControlActionMethod}($process, $actionName, $controllerName) === false) return false;
			}


			$result = $controller->{$actionMethod}($process, $this);


			$afterControlMethod = 'afterControl';
			$afterControlActionMethod = $afterControlMethod . $actionName;
			if(method_exists($controller, $afterControlActionMethod)){
				$controller->{$afterControlActionMethod}($process, $result, $actionName, $controllerName);
			}
			if(method_exists($controller, $afterControlMethod)){
				$controller->{$afterControlMethod}($process, $result, $actionName, $controllerName);
			}
			$this->_afterControl($process, $result, $controllerName, $actionName, $controller);

			return $result;

		}

		/**
		 * TODO
		 * @param $context
		 * @param $controllerName
		 * @param $actionName
		 * @param ControllerInterface $controller
		 * @return mixed
		 */
		public function _controlControllerInterface($context, $controllerName, $actionName,ControllerInterface $controller){

		}


		/**
		 * @param $context
		 * @param $controllerName
		 * @param $actionName
		 * @param $controllerObject
		 */
		protected function _beforeControl($context, $controllerName, $actionName, $controllerObject){

		}

		/**
		 * @param $context
		 * @param $result
		 * @param $controllerName
		 * @param $actionName
		 * @param $controllerObject
		 */
		protected function _afterControl($context, $result, $controllerName, $actionName, $controllerObject){

		}

		/**
		 * @param $controller_name
		 * @return string
		 * @throws Exception
		 */
		protected function _prepareControllerClassName($controller_name){
			if(strpos($controller_name,'.')!==false){
				$controller_name = explode('.',$controller_name);
				foreach($controller_name as $i => $chunk){
					if(!$chunk){
						throw new Exception('Error qualified controller name');
					}
					$controller_name[$i] = ucfirst($chunk);
				}
				$controller_name = implode('\\',$controller_name);
			}
			return $this->controller_namespace . '\\' . $controller_name . $this->controller_suffix;
		}

		/**
		 * @param $action_name
		 * @return string
		 */
		protected function _prepareActionMethodName($action_name){
			return $action_name . $this->action_suffix;
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

	}
}

