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
	use Jungle\Application\Dispatcher\ControllerInterface;
	use Jungle\Application\Dispatcher\Exception;
	use Jungle\Application\Dispatcher\Exception\Control;
	use Jungle\Application\Dispatcher\Exception\NotCertainBehaviour;
	use Jungle\Application\Dispatcher\Module;
	use Jungle\Application\Dispatcher\ModuleInterface;
	use Jungle\Application\Dispatcher\Process;
	use Jungle\Application\Dispatcher\ProcessInitiatorInterface;
	use Jungle\Application\Dispatcher\ProcessInterface;
	use Jungle\Application\Dispatcher\Reference;
	use Jungle\Application\Notification\Responsible\AccessDenied;
	use Jungle\Application\Notification\Responsible\AuthenticationMissed;
	use Jungle\Application\Notification\Responsible\NeedIntroduce;
	use Jungle\Application\Router;
	use Jungle\Application\Router\Routing;
	use Jungle\Application\Router\RoutingInterface;
	use Jungle\Application\View;
	use Jungle\Data\Record\Validation\ValidationResult;
	use Jungle\Di\HolderChains;
	use Jungle\Di\Injectable;
	use Jungle\Di\InjectionAwareInterface;
	use Jungle\FileSystem;
	use Jungle\Http\Response;
	use Jungle\User\AccessControl\Context\Substitute;
	use Jungle\User\AccessControl\Manager;
	use Jungle\User\Verification\Hint;

	/**
	 * Class Dispatcher
	 * @package Jungle\Application
	 */
	class Dispatcher extends Injectable implements DispatcherInterface{


		/** @var  StrategyInterface */
		protected $dispatching_strategy;

		/** @var  StrategyInterface[] */
		protected $strategies = [];

		/** @var  array[]  */
		protected $strategies_definitions = [];

		/** @var array|null  */
		protected $strategies_order;

		/** @var  ModuleInterface[]|array[]  */
		protected $modules = [];


		/** @var array  */
		protected $error_reference      = '#index:index:error';

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

		/** @var bool  */
		protected $dispatching_error = false;

		/** @var  RequestInterface */
		protected $dispatching_request;

		/** @var  RoutingInterface|ProcessInitiatorInterface */
		protected $dispatching_routing;

		/** @var ProcessInterface[]  */
		protected $dispatching_processes = [];

		/** @var  ProcessInterface */
		protected $restored_process;


		/**
		 * Dispatcher constructor.
		 */
		public function __construct(){
			register_shutdown_function(function(){

				$this->_errorsOnShutdown();

			});
		}

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
		 * @param StrategyInterface|string $alias
		 * @param StrategyInterface|string $strategy
		 * @param float $priority
		 * @return $this
		 */
		public function setStrategy($alias, $strategy, $priority = 0.0){
			if(is_string($strategy)){
				$this->strategies[$alias] = null;
				$this->strategies_definitions[$alias] = [
					'class'     => $strategy,
					'priority'  => floatval($priority)
				];
				$this->strategies_order = null;
			}elseif($strategy instanceof StrategyInterface){
				$this->strategies[$alias] = null;
				$this->strategies_definitions[$alias] = [
					'class'    => get_class($strategy),
					'priority' => floatval($priority)
				];
				$this->strategies_order = null;
			}
			return $this;
		}

		/**
		 * @param $alias
		 * @param $priority
		 * @return $this
		 */
		public function setStrategyPriority($alias, $priority){
			if(!isset($this->strategies_definitions[$alias])){
				throw new \LogicException('Strategy "'.$alias.'" not defined!');
			}
			$this->strategies_definitions[$alias]['priority'] = floatval($priority);
			return $this;
		}


		/**
		 * @param array $strategies
		 */
		public function setStrategies(array $strategies){
			$this->strategies = [];
			$this->strategies_definitions = [];
			$this->strategies_order = [];
			$i = 0;
			foreach($strategies as $alias => $strategy){
				$this->strategies_order[] = $alias;
				if(is_array($strategy)){
					$this->strategies[$alias] = null;
					$this->strategies_definitions[$alias] = array_replace($strategy,['priority' => $i]);
				}elseif(is_string($strategy)){
					$this->strategies[$alias] = null;
					$this->strategies_definitions[$alias] = [
						'class'     => $strategy,
						'priority'  => floatval($i)
					];
				}elseif($strategy instanceof StrategyInterface){
					$this->strategies[$alias] = null;
					$this->strategies_definitions[$alias] = [
						'class'    => get_class($strategy),
						'priority' => floatval($i)
					];
				}
				$i = $i + 5;
			}
		}

		/**
		 * @param $alias
		 * @return StrategyInterface
		 * @throws Exception
		 */
		public function getStrategy($alias){
			if(!isset($this->strategies_definitions[$alias])){
				throw new \LogicException('Strategy "'.$alias.'" not defined!');
			}
			if(!isset($this->strategies[$alias])){
				$strategy = $this->_loadStrategy($alias, $this->strategies_definitions[$alias]);
				$this->strategies[$alias] = $strategy;
			}
			return $this->strategies[$alias];
		}

		/**
		 * @return StrategyInterface
		 */
		public function getDispatchingStrategy(){
			return $this->dispatching_strategy;
		}

		/**
		 * @param RequestInterface $request
		 * @return StrategyInterface|null
		 */
		public function matchStrategy(RequestInterface $request){
			if($this->strategies_order === null){
				$this->strategies_order = array_keys($this->strategies_definitions);
				usort($this->strategies_order, [$this,'_strategy_sort']);
			}
			foreach($this->strategies_order as $name){
				$definition = $this->strategies_definitions[$name];
				/** @var StrategyInterface $className */
				$className = $definition['class'];
				$strategy = $this->strategies[$name];
				if($className::check($request)){
					if(!$strategy){
						$this->strategies[$name] = $strategy = $this->_loadStrategy($name, $definition);
					}
					return $strategy;
				}
			}
			return null;
		}



		/**
		 * @param $a
		 * @param $b
		 * @return int
		 */
		protected function _strategy_sort($a,$b){
			$a = $this->strategies_definitions[$a]['priority'];
			$b = $this->strategies_definitions[$b]['priority'];
			if($a == $b){
				return 0;
			}
			return $a > $b?1:-1;
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


		public function getDefaultMetadata(){
			return [
				'private'   => false,
				'hierarchy' => true,
				'strategy'  => null,
			];
		}

		/**
		 * @param $reference
		 * @return array
		 */
		public function getMetadata($reference){
			$default = $this->getDefaultMetadata();
			$reference = Reference::normalize($reference, [ 'module' => $this->default_module]);
			$moduleName = $reference['module'];
			if(!isset($this->modules[$moduleName])){
				return $default;
			}
			$module = $this->modules[$moduleName];
			if(!$module instanceof ModuleInterface){
				$module = $this->_loadModule($moduleName,$module);
				$this->modules[$moduleName] = $module;
			}
			return $module->getMetadata($reference['controller'], $reference['action']);
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
				throw new Exception('dispatch already run!');
			}
			
			try{
				$strategy = $this->matchStrategy($request);
				if(!$strategy){
					throw new Exception('Not have matched Strategy for current request!');
				}
				$this->_prepareDispatch($request, $strategy);
				if($this->_beforeDispatch($request, $strategy) !== false){

					/**
					 * @var Routing $routing
					 * @var RouterInterface $router
					 */
					$router = $strategy->getShared('router');
					$matching = $router->getMatchGenerator($request);
					foreach($matching as $routing){
						try{
							$this->dispatching_routing = $routing;
							if($routing->isUnknown()){
								throw new NotCertainBehaviour('Defined router, no have "not_found" route');
							}else{
								$process = $this->control(
									$routing->getReference(),
									(array) $routing->getParams(),
									$routing, Process::CALL_ROUTING
								);
								$response = $this->prepareResponse($process);
								$this->_afterDispatch($request,$routing,$process);
								return $response;
							}
						}catch(Exception\Forwarded $forwarded){
							$process = $forwarded->process;
							$response = $this->prepareResponse($process);
							$this->_afterDispatch($request,$routing,$process);
							return $response;
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
				$this->_continueDispatch();
			}
		}

		/**
		 * @param ProcessInterface $process
		 */
		public function storeProcess(ProcessInterface $process){
			$this->dispatching_processes[] = $process;
		}

		/**
		 * @param ProcessInterface $process
		 * @return ProcessInterface|null
		 * @throws \Exception
		 */
		public function restoreProcess(ProcessInterface $process = null){
			$r = array_pop($this->dispatching_processes);
			if($process && $r !== $process){
				throw new \Exception('Process is not valid for restoring');
			}
			$this->restored_process = $r;
			return $r;
		}

		/**
		 * @return ProcessInterface|null
		 */
		public function mainProcess(){
			return $this->dispatching_processes?$this->dispatching_processes[0]:null;
		}

		/**
		 * @return ProcessInterface|null
		 */
		public function currentProcess(){
			return $this->dispatching_processes?$this->dispatching_processes[count($this->dispatching_processes)-1]:null;;
		}

		/**
		 *
		 * Подмена главного действия контроллера
		 *
		 * @TODO Проработка завершения всех предшествующих hmvc процессов
		 *
		 * @param $reference
		 * @param array $params
		 * @param ProcessInitiatorInterface|null $initiator
		 * @return Response
		 * @throws Control
		 * @throws Exception\Forwarded
		 */
		public function forward($reference,array $params, ProcessInitiatorInterface $initiator){
			try{
				$initiator = $initiator?: $this->currentProcess();
				if($initiator instanceof ProcessInterface){
					$root = $initiator->getRoot();
					$root->setState($root::STATE_FAILURE);
				}else{
					$root = $initiator;
				}
				$process = $this->control($reference, $params, $root, Process::CALL_FORWARD,$initiator, null);
				throw new Exception\Forwarded($process);
			}catch(Exception\Forwarded $forwarded){
				if($this->dispatching_error){
					return $this->prepareResponse($forwarded->process);
				}else{
					throw $forwarded;
				}
			}
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
		 * @param array $params
		 * @param \Jungle\Application\Dispatcher\ProcessInitiatorInterface $initiator
		 * @param string $initiator_type
		 * @param ProcessInitiatorInterface $forwarder
		 * @param $options
		 * @return Process|mixed
		 * @throws Control
		 * @throws Exception\ContinueRoute
		 * @throws \Jungle\Exception
		 */
		public function control($reference, array $params, ProcessInitiatorInterface $initiator, $initiator_type,ProcessInitiatorInterface $forwarder = null, $options = null){
			$reference = Reference::normalize($reference,[ 'module' => $this->default_module ]);

			if(!($module = $this->getModule( ($moduleName = $reference['module']) ))){
				throw new Control('Module "'.$moduleName.'" not found!');
			}

			$di = $this->getDi();
			try{
				$di->insertInjection('module', $module, 10);
				$this->preControl($reference, $module, $initiator, $initiator_type,$forwarder);
				$process = $module->control($reference,$params, $initiator, $initiator_type, $forwarder, $options);
				$output = $this->_output($process, $options);
				$this->postControl($output,$process);
				return $output;
			}finally{
				$di->restoreInjection('module', $module);
			}
		}


		/**
		 * @param ProcessInterface $process
		 * @return Response
		 */
		public function prepareResponse(ProcessInterface $process){
			$response = $this->dispatching_request->getResponse();
			/** @var ViewInterface $view */
			$view = $this->getDi()->getShared('view');
			if($response->getContent() === null){
				$rendered = $view->render(null,$process);
				$response->setContent($rendered);
			}
			$this->dispatching_strategy->complete($response, $view);
			return $response;
		}

		/**
		 * @param ProcessInterface $process
		 * @param array|null $options
		 * @return ProcessInterface|mixed
		 */
		protected function _output(ProcessInterface $process, $options = null){
			try{
				if(is_array($options)){
					return $this->_renderProcess($process, $options);
				}elseif(is_string($options) && $options){
					$options = strtolower($options);
					switch($options){
						case'result': return $process->getResult();
						case'buffered': return $process->getBuffered();
						default:
							/** @var ViewInterface $view */
							$view   = $this->getDi()->getShared('view');
							return $view->render($options,$process);
					}
				}
				return $process;
			}finally{
				$this->restoreProcess($process);
				$this->restored_process = $process;
			}
		}

		/**
		 * @param ProcessInterface $process
		 * @param array $options
		 * @return string|null
		 */
		protected function _renderProcess(ProcessInterface $process,array $options){
			if(isset($options['render']) && $options['render']){
				/** @var ViewInterface $view */
				$view   = $this->getDi()->getShared('view');
				$alias  = null;
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
			}elseif(isset($options['result']) && $options['result']){
				return $process->getResult();
			}elseif(isset($options['buffer']) && $options['buffer']){
				return $process->getBuffered();
			}
			return null;
		}

		/**
		 * @param array $params
		 * @param array $reference
		 * @param ModuleInterface $module
		 * @param ControllerInterface $controller
		 * @param ProcessInitiatorInterface $initiator
		 * @param $initiator_type
		 * @param ProcessInitiatorInterface $forwarder
		 * @return Process
		 */
		public function factoryProcess(
			array $params,
			array $reference,
			ModuleInterface $module,
			ControllerInterface $controller,
			ProcessInitiatorInterface $initiator,
			$initiator_type,
			ProcessInitiatorInterface $forwarder = null
		){
			return new Process($this, $reference, $module, $controller, $params, $initiator, $initiator_type,$forwarder);
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
		 * @param \Exception $e
		 * @param ProcessInterface $process
		 * @return mixed
		 */
		public function interceptException(\Exception $e, ProcessInterface $process){
			$this->event_manager->invokeEvent('dispatcher:interceptException', $this, $process, $e);
			if($e instanceof NeedIntroduce){
				$process->setTask('introduce', $e);
			}
			if($e instanceof ValidationResult){
				$process->setTask('validation', $e);
			}
			if($e instanceof AuthenticationMissed){
				$process->setTask('authentication', $e);
			}
			if($e instanceof AccessDenied){
				$process->setTask('access', $e);
			}
			if($e instanceof Hint){
				$process->setTask('verification', $e);
			}
			return $process->hasTasks();
		}

		/**
		 * @param \Exception $e
		 * @param ProcessInterface $process
		 */
		public function interceptedException(\Exception $e, ProcessInterface $process){
			$this->event_manager->invokeEvent('dispatcher:interceptedException', $this, $process, $e);
		}


		/**
		 * @param ProcessInterface $process
		 * @throws AccessDenied
		 */
		public function beforeControl(ProcessInterface $process){
			$this->event_manager->invokeEvent('dispatcher:beforeControl', $this, $process);

			if($process->getInitiatorType() === Process::CALL_ROUTING){
				/** @var Manager $access */
				if($access = $this->getDi()->getShared('access',false)){
					$substitute = new Substitute();
					$substitute->setClass('MCA');
					$substitute->setValue($process->getReference() + ['mca' => $process->getReferenceString()]);

					$allowed = $access->enforce('control', $substitute);
					if(!$allowed){
						throw new AccessDenied();
					}
				}
			}

		}

		/**
		 * @param ProcessInterface $process
		 * @param $result
		 */
		public function afterControl(ProcessInterface $process, $result){
			$this->event_manager->invokeEvent('dispatcher:afterControl', $this, $process);
		}

		/**
		 *
		 * Метод вызывается перед запуском инициализации Процесса,
		 * здесь можно проверить правовую состоятельность вызова
		 * Ответственным за ошибку в этом методе,
		 * является контекст Инициатора в котором mca Был вызван.
		 * В противном случае - ContinueRoute как переключатель
		 *
		 * @param $reference
		 * @param ModuleInterface $module
		 * @param null|\Jungle\Application\Dispatcher\ProcessInitiatorInterface|ProcessInterface|Router\Routing $initiator
		 * @param $initiator_type
		 * @param null $forwarder
		 * @throws Control
		 * @throws Exception\ContinueRoute
		 */
		protected function preControl($reference, ModuleInterface $module, ProcessInitiatorInterface $initiator, $initiator_type,$forwarder = null){
			$this->event_manager->invokeEvent('dispatcher:preControl', $this, $reference, $module, $initiator);

			/**
			 * В данном методе происходит пре-контроль
			 * : Проверка, может ли контроллер быть запущен из другого контроллера
			 * : Проверка, Можно ли запускать контроллер публично, если нет, то пропустить текущий маршрут
			 * : Проверка, поддерживает ли контроль текущую стратегию
			 * : Проверка, если Маршрут имеет динамичные ссылки и в системе нету такого действия - то пропустить маршрут
			 */

			$this->getMetadata($reference);

			$meta = $module->getMetadata($reference['controller'], $reference['action']);

			// check hmvc support
			if($initiator instanceof ProcessInterface && (!isset($meta['hierarchy']) || !$meta['hierarchy'])){
				throw new Control('mca "'.Reference::stringify($reference).'" is not supported Hierarchy call');
			}
			// check private
			if($initiator instanceof RoutingInterface){
				if(!$initiator->isNotFound() && isset($meta['private']) && $meta['private']){
					throw new Exception\ContinueRoute();
				}elseif(
					!$module->hasControl($reference['controller'], $reference['action'])
					&& $initiator->getRoute()->isDynamic()
				){
					throw new Exception\ContinueRoute();
				}
			}

			// check support request strategy
			if(isset($meta['strategy']) && $meta['strategy']){
				$strategy = $meta['strategy'];
				if(!is_array($strategy)) $strategy = [$strategy];
				$current = $this->dispatching_strategy->getName();
				if(!in_array($current, $strategy, true)){

					if($initiator instanceof RoutingInterface){
						// if routed
						throw new Exception\ContinueRoute();
					}else{
						// if hmvc call
						throw new Control('Application request strategy "'.$current.'" not support in "'.Reference::stringify($reference).'"');
					}
				}
			}

		}

		/**
		 * @param $output
		 * @param ProcessInterface $process
		 */
		protected function postControl($output,ProcessInterface $process){
			$this->event_manager->invokeEvent('dispatcher:postControl', $this, $process, $output);
		}

		/**
		 * @param \Exception $e
		 * @param bool $return
		 * @return ResponseInterface
		 * @throws \Jungle\Application\Exception
		 */
		public function handleException(\Exception $e, $return = true){
			if(ob_get_level()){
				ob_end_clean();
			}

			$this->dispatching_error = true;
			$reporter = $this->crash_reporter;
			$reporter->report($e);
			$process = $this->currentProcess()?: $this->restored_process;
			try{
				if($process){
					$process->setState($process::STATE_FAILURE);
					$response = $this->forward($this->error_reference,[
						'exception' => $e
					], $process);
				}elseif($this->dispatching_routing){
					$response = $this->forward($this->error_reference,[
						'exception' => $e
					], $this->dispatching_routing);
				}else{
					throw new \Jungle\Application\Exception('Initiator is not recognized!');
				}
				if(!$return){
					$response->send();
					exit();
				}
				return $response;
			}catch(\Exception $e){
				if(ob_get_level()){
					ob_end_clean();
				}
				echo '500 Internal Server Error, sorry please';
				exit();
			}finally{
				$this->event_manager->invokeEvent('dispatcher:afterDispatch',true,$this, $process->getRouting()->getRequest(), $process->getRouting(), $process);
			}
		}


		/**
		 * @param $num
		 * @param $message
		 * @param $filename
		 * @param $line
		 * @throws \Jungle\Application\Exception
		 */
		protected function _handleFatalError($num, $message, $filename, $line){

			if(ob_get_level()){
				ob_end_clean();
			}
			$this->dispatching_error = true;
			$reporter = $this->crash_reporter;
			$e =  new \ErrorException($message,0,$num, $filename,$line);
			$reporter->report($e);
			$process = $this->currentProcess()?: $this->restored_process;
			try{
				if($process){
					$process->setState($process::STATE_FAILURE);
					$response = $this->forward($this->error_reference,[
						'exception' => $e
					], $process);
				}elseif($this->dispatching_routing){
					$response = $this->forward($this->error_reference,[
						'exception' => $e
					], $this->dispatching_routing);
				}else{
					throw new \Jungle\Application\Exception('Initiator is not recognized!');
				}
				$response->send();
			}catch(\Exception $e){
				if(ob_get_level()){
					ob_end_clean();
				}
				echo '500 Internal Server Error, sorry please';
			}
			$this->event_manager->invokeEvent('dispatcher:afterDispatch',true,$this, $process->getRouting()->getRequest(), $process->getRouting(), $process);
			exit();
		}

		protected function _errorsOnShutdown(){
			if($this->dispatching){
				$error = error_get_last();

				$listen_error =
					E_ERROR |
					E_PARSE |
					E_COMPILE_ERROR |
					E_CORE_ERROR |
					E_USER_ERROR |
					E_CORE_ERROR |
					E_COMPILE_ERROR |
					E_RECOVERABLE_ERROR;

				if(isset($error) && ($error['type'] & $listen_error)){
					$this->_handleFatalError($error['type'], $error['message'], $error['file'], $error['line']);
				}
			}
		}


		/**
		 * @param RequestInterface $request
		 * @param StrategyInterface $strategy
		 */
		protected function _beforeDispatch(RequestInterface $request, StrategyInterface $strategy){
			$this->event_manager->invokeEvent('dispatcher:beforeDispatch',false,$this, $request, $strategy);
		}

		/**
		 * @param RequestInterface $request
		 * @param RoutingInterface $routing
		 * @param ProcessInterface $result
		 */
		protected function _afterDispatch(RequestInterface $request, Router\RoutingInterface $routing, ProcessInterface $result){
			$this->event_manager->invokeEvent('dispatcher:afterDispatch',false,$this, $request, $routing, $result);
		}

		/**
		 * @param RequestInterface $request
		 * @param StrategyInterface $strategy
		 * @throws \Jungle\Exception
		 */
		protected function _prepareDispatch(RequestInterface $request, StrategyInterface $strategy){
			$diChains = $this->getDi();
			$diChains->insertInjection('strategy',$strategy, 5);

			$default = $diChains->getInjection('default');
			$default->setShared('request',$request);
			$default->setShared('response',$request->getResponse());

			$this->dispatching              = true;
			$this->dispatching_error        = false;
			$this->dispatching_request      = $request;
			$this->dispatching_strategy     = $strategy;
			$strategy->registerServices();
		}

		protected function _continueDispatch(){
			$diChains = $this->getDi();
			$diChains->restoreInjection('strategy');

			$default = $diChains->getInjection('default');
			$default->removeService('request');
			$default->removeService('response');

			$current_process = $this->currentProcess();
			if($current_process){
				$routing = $current_process->getRouting();
				$request = $routing->getRequest();

				$this->event_manager->invokeEvent('dispatcher:continueDispatch',false,$this, $request, $routing, $current_process);
			}else{
				$this->event_manager->invokeEvent('dispatcher:continueDispatch',false,$this);
			}



			$this->dispatching              = false;
			$this->dispatching_strategy     = null;
			$this->dispatching_request      = null;
			$this->restored_process             = null;
			$this->dispatching_processes    = [];
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



		/**
		 * @param $alias
		 * @param array $definition
		 * @return StrategyInterface
		 * @throws Exception
		 */
		protected function _loadStrategy($alias,array $definition){
			if(!isset($definition['class'])){
				throw new Exception('Strategy load error: "' . $alias . '" not defined class in definition');
			}
			$className = $definition['class'];
			if(class_exists($className)){
				$strategy = new $className();
				if(!$strategy instanceof StrategyInterface){
					throw new Exception('Module instance is not '.StrategyInterface::class);
				}
				$strategy->setName($alias);
				return $strategy;
			}else{
				throw new Exception('Strategy load error: "' . $alias . '" not found strategy class "' . $className . '"');
			}
		}

		public function __destruct(){
			if($this->dispatching){
				$this->event_manager->invokeEvent('dispatcher:afterDispatch',$this->dispatching_error,$this, $this->dispatching_request, $this->dispatching_routing, $this->mainProcess());
			}
		}

	}
}

