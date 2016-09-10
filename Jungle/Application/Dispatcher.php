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
	use Jungle\Application\Dispatcher\Exception;
	use Jungle\Application\Dispatcher\Exception\Control;
	use Jungle\Application\Dispatcher\Exception\NotCertainBehaviour;
	use Jungle\Application\Dispatcher\Module;
	use Jungle\Application\Dispatcher\ModuleInterface;
	use Jungle\Application\Dispatcher\Process;
	use Jungle\Application\Dispatcher\Process\ProcessInitiatorInterface;
	use Jungle\Application\Dispatcher\ProcessInterface;
	use Jungle\Application\Dispatcher\Reference;
	use Jungle\Application\Router;
	use Jungle\Application\Router\RoutingInterface;
	use Jungle\Application\View;
	use Jungle\Di\Injectable;
	use Jungle\Di\InjectionAwareInterface;
	use Jungle\FileSystem;

	/**
	 * Class Dispatcher
	 * @package Jungle\Application
	 */
	class Dispatcher extends Injectable implements DispatcherInterface{

		/** @var  StrategyInterface[] */
		protected $strategies = [];

		/** @var  StrategyInterface */
		protected $strategy;

		/** @var  ModuleInterface[]|array[]  */
		protected $modules = [];

		protected $error_reference = [
			'module'        => 'index',
			'controller'    => 'index',
			'action'        => 'error'
		];

		/** @var  string */
		protected $default_module       = 'index';

		/** @var  string */
		protected $default_controller   = 'index';

		/** @var  string */
		protected $default_action       = 'index';

		/** @var  string */
		protected $action_suffix        = 'Action';

		/** @var  string */
		protected $controller_suffix    = 'Controller';

		/** @var  bool  */
		protected $dispatching = false;

		/** @var  RequestInterface */
		protected $last_request;

		/** @var  ProcessInterface */
		protected $last_process;

		/** @var  RoutingInterface */
		protected $last_routing;

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
		 * @param $reference
		 * @return bool
		 * @throws Exception
		 */
		public function hasControl($reference){
			$reference = Reference::normalize($reference, [ 'module' => $this->default_module]);
			$moduleName = $reference['module'];
			if(!isset($this->modules[$moduleName])){
				return false;
			}
			$module = $this->modules[$moduleName];
			if(!$module instanceof ModuleInterface){
				$module = $this->_loadModule($moduleName,$module);
				$this->modules[$moduleName] = $module;
			}
			return $module->hasControl($reference['controller'], $reference['action'] );
		}

		/**
		 * @param $reference
		 * @return array
		 */
		public function getMetadata($reference){
			$reference = Reference::normalize($reference, [ 'module' => $this->default_module]);
			$moduleName = $reference['module'];
			if(!isset($this->modules[$moduleName])){
				return [];
			}
			$module = $this->modules[$moduleName];
			if(!$module instanceof ModuleInterface){
				$module = $this->_loadModule($moduleName,$module);
				$this->modules[$moduleName] = $module;
			}
			return $module->getMetadata($reference['controller'], $reference['action'] );
		}

		/**
		 * @param $alias
		 * @param StrategyInterface $strategy
		 * @return $this
		 */
		public function setStrategy($alias, StrategyInterface $strategy){
			$this->strategies[$alias] = $strategy;
			return $this;
		}

		/**
		 * @param $alias
		 * @return StrategyInterface|null
		 */
		public function findStrategy($alias){
			return isset($this->strategies[$alias])?$this->strategies[$alias]:null;
		}

		/**
		 * @return StrategyInterface
		 */
		public function getStrategy(){
			return $this->strategy;
		}

		/**
		 * @param RequestInterface $request
		 * @return StrategyInterface|null
		 */
		public function matchStrategyBy(RequestInterface $request){
			foreach($this->strategies as $name => $strategy){
				if($strategy->check($request)){
					return $strategy;
				}
			}
			return null;
		}

		/**
		 * Dispatcher constructor.
		 */
		public function __construct(){
			register_shutdown_function(function(){
				if($this->dispatching){
					$error = error_get_last();
					if(isset($error) && ($error['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR))){
						$this->handleFatalError($error['type'], $error['message'], $error['file'], $error['line']);
					}else{
						ob_end_flush();
					}
				}
			});
		}

		/**
		 * @param $num
		 * @param $message
		 * @param $filename
		 * @param $line
		 * @throws \Jungle\Application\Exception
		 * @internal param bool $return
		 */
		public function handleFatalError($num, $message, $filename, $line){
			if(ob_get_level()) ob_end_clean();
			if($this->last_process){
				$response = $this->forward($this->error_reference,[
					'exception' => new \ErrorException($message,0,$num, $filename,$line)
				], $this->last_process);
			}elseif($this->last_routing){
				$response = $this->forward($this->error_reference,[
					'exception' => new \ErrorException($message,0,$num, $filename,$line)
				], $this->last_routing);

			}else{
				throw new \Jungle\Application\Exception('Initiator is not recognized!');
			}
			$response->send();
			exit();
		}

		/**
		 * @param \Exception $e
		 * @param bool $return
		 * @return ResponseInterface
		 * @throws \Jungle\Application\Exception
		 */
		public function handleException(\Exception $e, $return = true){
			if(ob_get_level()) ob_end_clean();

			if($this->last_process){
				$response = $this->forward($this->error_reference,[
					'exception' => $e
				], $this->last_process);
			}elseif($this->last_routing){
				$response = $this->forward($this->error_reference,[
					'exception' => $e
				], $this->last_routing);
			}else{
				throw new \Jungle\Application\Exception('Initiator is not recognized!');
			}

			if(!$return){
				$response->send();
				exit();
			}

			return $response;
		}

		/**
		 * Диспетчеризация непосредственно изначального запроса.
		 * Происходит старт обертки HMVC в которой Контроль может вызываться рекурсивно для разных действий
		 * в последствии генерируется итоговый "Ответ" на переданный "Запрос"
		 *
		 * В диспетчеризации учавствуют:
		 * Маршрутизаторы! происходит выбор предпочитаемого запросу - маршрутизатора с индивидуальным набором маршрутов.
		 * Целевой "Контроль"
		 * Формирование Ответа.
		 *
		 * @param RequestInterface $request
		 * @return ResponseInterface
		 * @throws \Exception
		 *
		 */
		public function dispatch(RequestInterface $request){
			if($this->dispatching){
				throw new \LogicException('dispatch already run!');
			}
			try{
				$strategy = $this->matchStrategyBy($request);
				if(!$strategy){
					throw new Exception('Not have matched Strategy for current request!');
				}
				$this->_onStrategyRecognized($strategy);
				if($this->_beforeDispatch($request) !== false){
					$this->_onDispatchStarted($request);
					/**
					 * @var RoutingInterface $routing
					 * @var RouterInterface $router
					 */
					$router = $strategy->getShared('router');
					$matching = $router->getMatchGenerator($request);
					foreach($matching as $routing){
						try{
							$this->last_routing = $routing;
							if($routing->isUnknown()){
								throw new NotCertainBehaviour('Defined router, no have "not_found" route');
							}else{
								$process = $this->control($routing->getReference(), (array) $routing->getParams(), null, $routing);
								$response = $this->prepareResponse($process);
								$this->_afterDispatch($request,$routing,$process);
								return $response;
							}
						}catch(Exception\ContinueRoute $e){
							$routing->reset();
							continue;
						}
					}
				}
				throw new NotCertainBehaviour('Defined router, no have "not_found" route');
			}catch(\Exception $e){
				return $this->handleException($e);
			}finally{
				$this->_onDispatchContinue();
			}
		}

		/**
		 *
		 * Подмена целевого действия контроллера
		 *
		 * @param $reference
		 * @param $params
		 * @param ProcessInitiatorInterface|null $initiator
		 * @return ResponseInterface
		 * @throws Control
		 */
		public function forward($reference, $params, ProcessInitiatorInterface $initiator = null){
			try{
				$process = $this->control($reference, $params, null, $initiator);
				throw new Exception\Forwarded($process);
			}catch (Exception\Forwarded $forward){
				$process = $forward->getProcess();
				return $this->prepareResponse($process);
			}
		}

		/**
		 * Специфично для HTTP
		 *
		 * @param $reference
		 * @param $permanent
		 */
		public function redirect($reference, $permanent = false){

		}

		/**
		 *
		 * Метод производит "Контроль" - это непосредственный вызов модулем-контроллером-действием.
		 * Происходит вызов действия и после этого в зависимости от настроек вывода контроля
		 * - генерируется возвращаемое значение, оно может быть разное для HTTP / Cli специфики,
		 * также для разных типов клиентского HTTP запроса в том числе.
		 * JSON, HTML, XML и так далее
		 *
		 * @param $reference
		 * @param $data
		 * @param $options
		 * @param null|\Jungle\Application\Dispatcher\Process\ProcessInitiatorInterface|ProcessInterface|Router\Routing $initiator
		 * @return Process|mixed
		 * @throws Control
		 */
		public function control($reference, $data = null, $options = null, ProcessInitiatorInterface $initiator = null){
			$reference = Reference::normalize(
				$reference,[
					'module' => $this->default_module
				]
			);
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
		 * @return ResponseInterface
		 */
		public function prepareResponse(ProcessInterface $process){
			$response = $this->last_request->getResponse();
			/** @var ViewInterface $view */
			$view = $this->_dependency_injector->getShared('view');
			if($response->getContent() === null){
				$rendered = $view->render(null,$process);
				$response->setContent($rendered);
			}
			$this->strategy->complete($response, $view);
			return $response;
		}

		/**
		 * @param ProcessInterface $process
		 * @param array|null $options
		 * @return ProcessInterface|mixed
		 */
		public function checkoutProcess(ProcessInterface $process,array $options = null){
			$this->last_process = $process;
			if(is_array($options)){
				if(isset($options['render']) && $options['render']){
					/** @var ViewInterface $view */
					$view = $this->_dependency_injector->getShared('view');
					$alias = null;
					$render_variables  = [];
					$render_options    = [];
					if(is_array($options['render'])){
						$alias              = $options['render']['alias'];
						$render_variables   = (array)$options['render']['variables'];
						$render_options     = (array)$options['render']['options'];
					}elseif(is_string($options['render'])){
						$alias = $options['render'];
					}
					return $view->render($alias,$process,$render_variables, $render_options);
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
		 * @return ModuleInterface
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

		protected function _afterDispatch(RequestInterface $request, Router\RoutingInterface $routing, ProcessInterface $result){

		}

		/**
		 * @param StrategyInterface $strategy
		 */
		protected function _onStrategyRecognized(StrategyInterface $strategy){
			$this->strategy = $strategy;
		}

		/**
		 * @param RequestInterface $request
		 */
		protected function _onDispatchStarted(RequestInterface $request){
			if($this->_dependency_injector){
				$this->_dependency_injector->setShared('request',$request);
				$this->_dependency_injector->setShared('response',$request->getResponse());
				$this->_dependency_injector->setShared('strategy',$this->strategy);
				$this->_dependency_injector->setNext($this->strategy);
			}
			$this->dispatching = true;
			$this->last_request = $request;
		}

		/**
		 *
		 */
		protected function _onDispatchContinue(){
			if($this->_dependency_injector){
				$this->_dependency_injector->remove('request');
				$this->_dependency_injector->remove('response');
				$this->_dependency_injector->remove('strategy');
			}
			$this->strategy = null;
			$this->dispatching = false;
			$this->last_request = null;
			$this->last_process = null;
		}


		/**
		 * @param $reference
		 * @param ModuleInterface $module
		 * @param null|\Jungle\Application\Dispatcher\Process\ProcessInitiatorInterface|ProcessInterface|Router\Routing $initiator
		 * @throws Control
		 * @throws Exception\ContinueRoute
		 */
		protected function _beforeControl($reference, ModuleInterface $module, ProcessInitiatorInterface $initiator = null){

			/**
			 * В данном методе происходит пре-контроль
			 * : Проверка, может ли контроллер быть запущен из другого контроллера
			 * : Проверка, Можно ли запускать контроллер публично, если нет, то пропустить текущий маршрут
			 * : Проверка, поддерживает ли контроль текущую стратегию
			 * : Проверка, если Маршрут имеет динамичные ссылки и в системе нету такого действия - то пропустить маршрут
			 */
			if($initiator){

				$meta = $module->getMetadata($reference['controller'], $reference['action']);
				if($initiator instanceof ProcessInterface && (!isset($meta['hierarchy']) || !$meta['hierarchy'])){
					throw new Control("{$module->getName()}:{$reference['controller']}:{$reference['action']} not support hierarchy calling!");
				}
				if($initiator instanceof RoutingInterface && (
					   (isset($meta['private']) && $meta['private']) ||
				       (!$module->hasControl($reference['controller'], $reference['action']) && $initiator->getRoute()->isDynamic())
				   )
				){
					throw new Exception\ContinueRoute();
				}
			}
			if(isset($meta['strategy']) && $meta['strategy']){
				$strategy = $meta['strategy'];
				if(!is_array($strategy)) $strategy = [$strategy];
				$current = $this->strategy->getType();
				if(!in_array($this->strategy->getType(), $strategy, true)){
					if($initiator instanceof RoutingInterface){
						throw new Exception\ContinueRoute();
					}else{
						throw new Control("{$module->getName()}:{$reference['controller']}:{$reference['action']} not support \"{$current}\" application strategy!");
					}
				}
			}

		}

		/**
		 * @param $output
		 * @param ProcessInterface $process
		 */
		protected function _afterControl($output,ProcessInterface $process){

		}


	}
}

