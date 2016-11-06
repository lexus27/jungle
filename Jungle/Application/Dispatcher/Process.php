<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.05.2016
 * Time: 16:51
 */
namespace Jungle\Application\Dispatcher {
	
	use Jungle\Application\Dispatcher;
	use Jungle\Application\Notification\Responsible\NeedIntroduce;
	use Jungle\Application\Router\RoutingInterface;

	/**
	 * Class Process
	 * @package Jungle\Application\Dispatcher\Controller
	 */
	class Process implements ProcessInterface{

		/** @var  Dispatcher */
		protected $dispatcher;

		/** @var string  */
		protected $state = 'processing';

		/** @var string  */
		protected $stage = 'prepare';

		/** @var  \Jungle\Application\Dispatcher\ProcessInitiatorInterface|RoutingInterface|ProcessInterface|null */
		protected $initiator;

		/** @var  string */
		protected $initiator_type;

		/** @var bool  */
		protected $initiator_forwarder = false;

		/** @var  ModuleInterface|null */
		protected $module;

		/** @var  object|ControllerInterface|ControllerManuallyInterface */
		protected $controller;

		/** @var  mixed */
		protected $reference;

		/** @var  array */
		protected $params = [];

		/** @var  mixed */
		protected $result;

		/** @var  string */
		protected $rendered;

		/** @var  bool */
		protected $buffering = false;

		/** @var  string|null */
		protected $buffered;

		/** @var  array  */
		protected $options = [];

		/** @var  array  */
		protected $tasks = [];


		/**
		 * Process constructor.
		 * @param Dispatcher $dispatcher
		 * @param mixed $reference
		 * @param ModuleInterface $module
		 * @param object|ControllerInterface|ControllerManuallyInterface $controller
		 * @param array $params
		 * @param \Jungle\Application\Dispatcher\ProcessInitiatorInterface|RoutingInterface|ProcessInterface $initiator
		 * @param string $initiator_type
		 * @param ProcessInitiatorInterface $forwarder
		 */
		public function __construct(
			Dispatcher $dispatcher, array $reference, ModuleInterface $module, $controller,
			array $params,
			ProcessInitiatorInterface $initiator, $initiator_type,
			ProcessInitiatorInterface $forwarder = null
		){

			if($initiator instanceof RoutingInterface){
				$initiator_type = self::CALL_ROUTING;
			}elseif($forwarder){
				$initiator_type = self::CALL_FORWARD;
			}else{
				$initiator_type = self::CALL_HIERARCHY;
			}

			$this->initiator = $initiator;
			$this->initiator_type = $initiator_type;

			if($forwarder){
				$this->initiator_forwarder = $forwarder;
			}

			$this->dispatcher = $dispatcher;
			$this->reference = $reference;
			$this->module = $module;
			$this->controller = $controller;

			$this->params = $params;

		}



		/**
		 * @return Dispatcher
		 */
		public function getDispatcher(){
			return $this->dispatcher;
		}

		/**
		 * @return ModuleInterface|null
		 */
		public function getModule(){
			return $this->module;
		}

		/**
		 * @return ControllerInterface|ControllerManuallyInterface|object
		 */
		public function getController(){
			return $this->controller;
		}

		/**
		 * @return string
		 */
		public function getActionName(){
			return $this->reference['action'];
		}


		/**
		 * @return mixed
		 */
		public function getReference(){
			return $this->reference;
		}

		/**
		 * @return string
		 */
		public function getReferenceString(){
			return Reference::stringify($this->reference);
		}

		/**
		 * @param array $param_names
		 * @param bool $return
		 * @return $this
		 * @throws NeedIntroduce
		 */
		public function requires(array $param_names, $return = false){
			if($param_names !== null && $this->stage !== self::STAGE_RENDERING && $param_names !== array_intersect($param_names, array_keys($this->params))){
				throw new NeedIntroduce("Params ".implode(', ',(array)$param_names)." required");
			}
			if($return && $param_names){
				return array_intersect_key(array_flip($param_names), $this->params);
			}
			return $this;
		}

		/**
		 * @param array $required_names
		 * @param bool $returnOnlyRequired
		 * @return array
		 * @throws NeedIntroduce
		 */
		public function getParams(array $required_names = null, $returnOnlyRequired = false){
			if($required_names !== null && $this->stage !== self::STAGE_RENDERING && $required_names !== array_intersect($required_names, array_keys($this->params))){
				throw new NeedIntroduce("Params ".implode(', ',(array)$required_names)." required");
			}
			if($required_names !== null && $returnOnlyRequired){
				return array_intersect_key(array_flip($required_names), $this->params);
			}
			return $this->params;
		}

		/**
		 * @param mixed $key
		 * @param null $default
		 * @return mixed
		 */
		public function getParam($key, $default = null){
			return array_key_exists($key, $this->params)?$this->params[$key]:$default;
		}

		/**
		 * @param mixed $key
		 * @param bool $allowNull
		 * @throws NeedIntroduce
		 */
		public function requireParam($key, $allowNull = true){
			if(array_key_exists($key,$this->params) && ($allowNull || isset($this->params[$key]))){
				return $this->params[$key];
			}else{
				throw new NeedIntroduce("Param ".$key." required");
			}
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasParam($key){
			return array_key_exists($key, $this->params);
		}

		/**
		 * @param mixed $key
		 * @param $value
		 * @return mixed
		 */
		public function setParam($key, $value){
			$this->params[$key] = $value;
			return $this;
		}


		/**
		 * @return array
		 */
		public function getMeta(){
			$reference = $this->reference;
			return $this->module->getMetadata($reference['controller'],$reference['action']);
		}


		/**
		 * @param $stage
		 * @return mixed
		 */
		public function setStage($stage){
			$this->stage = $stage;
		}

		/**
		 * @return mixed
		 */
		public function getStage(){
			return $this->stage;
		}

		/**
		 * @param $state
		 * @return mixed
		 */
		public function setState($state){
			$this->state = $state;
		}

		/**
		 * @return mixed
		 */
		public function getState(){
			return $this->state;
		}

		/**
		 * @return mixed
		 */
		public function getInitiatorType(){
			return $this->initiator_type;
		}

		/**
		 * @return ProcessInterface|RoutingInterface|null
		 */
		public function getInitiator(){
			return $this->initiator;
		}

		/**
		 * @return ProcessInterface
		 */
		public function getBase(){
			return $this->initiator_type !== self::CALL_ROUTING? $this->initiator->getBase() : $this ;
		}

		/**
		 * @return bool
		 */
		public function isStartupFail(){
			return $this->initiator_forwarder && $this->initiator_type === self::CALL_ROUTING;
		}

		/**
		 * @return ProcessInitiatorInterface|ProcessInterface|null
		 */
		public function getPredecessor(){
			return $this->initiator_forwarder && $this->initiator_type === self::CALL_FORWARD? $this->initiator : null ;
		}


		/**
		 * @return ProcessInitiatorInterface|ProcessInterface|null
		 */
		public function getForwarder(){
			return $this->initiator_forwarder;
		}

		/**
		 * @return ProcessInterface
		 */
		public function getRoot(){
			return $this->initiator_type === self::CALL_HIERARCHY? $this->initiator->getRoot() : $this ;
		}

		/**
		 * @return ProcessInterface|null
		 */
		public function getParent(){
			return $this->initiator_type === self::CALL_HIERARCHY? $this->initiator : null ;
		}

		/**
		 * @return RoutingInterface|null
		 */
		public function getRouting(){
			return $this->initiator_type === self::CALL_ROUTING? $this->initiator: $this->initiator->getRouting();
		}

		/**
		 * @return \Jungle\Application\RouterInterface|null
		 */
		public function getRouter(){
			return $this->initiator->getRouter();
		}



		/**
		 * @param $reference
		 * @param $data
		 * @param array $options
		 * @return mixed
		 * @throws Dispatcher\Exception\Control
		 */
		public function call($reference, $data = null, $options = null){
			$reference = Reference::normalize($reference, null, false);
			return $this->dispatcher->control($reference, $data, $this, self::CALL_HIERARCHY, null, $options);
		}

		/**
		 * @param $reference
		 * @param $data
		 * @param array $options
		 * @return mixed
		 * @throws Dispatcher\Exception\Control
		 */
		public function callIn($reference, $data = null, $options = null){
			$reference = Reference::normalize($reference, null, false);
			$reference['module']		= $this->reference['module'];
			$reference['controller']	= $this->reference['controller'] . '.' . $reference['controller'];
			return $this->dispatcher->control($reference, $data, $this, self::CALL_HIERARCHY, null, $options);
		}

		/**
		 * @param $action
		 * @param $data
		 * @param $options
		 * @return mixed
		 * @throws Dispatcher\Exception\Control
		 */
		public function callCurrent($action, $data, $options = null){
			$reference = $this->reference;
			if(strcasecmp($reference['action'],$action)===0){
				throw new Dispatcher\Exception\Control('Executing current action not allowed');
			}
			$reference['action'] = $action;
			return $this->dispatcher->control($reference, $data, $this, self::CALL_HIERARCHY, null, $options);
		}

		/**
		 * @param $action
		 * @param $data
		 * @param array|null $options
		 * @return Process|mixed
		 * @throws Exception\Control
		 */
		public function callBubble($action, $data, $options = null){
			$reference = $this->reference;
			if(strcasecmp($reference['action'],$action)===0){
				throw new Dispatcher\Exception\Control('Executing current action not allowed');
			}
			$references = Reference::generateSequence($reference,[ 'action' => Reference::SAFE_STRICT ]);
			foreach($references as $reference){
				if($this->dispatcher->hasControl($reference)){
					return $this->dispatcher->control($reference, $data, $this, self::CALL_HIERARCHY, null, $options);
				}
			}
			throw new Dispatcher\Exception\Control('Not found suitable bubble for action "'.$action.'"');
		}


		/**
		 * @param $action
		 * @param $data
		 * @param array|null $options
		 * @return Process|mixed
		 * @throws Exception\Control
		 */
		public function callBubbleWithController($action, $data, $options = null){
			$reference = $this->reference;
			if(strcasecmp($reference['action'],$action)===0){
				throw new Dispatcher\Exception\Control('Executing current action not allowed');
			}
			$references = Reference::getSequence($reference,[ 'action' => Reference::SAFE_STRICT ],'controller');
			foreach($references as $reference){
				if($this->dispatcher->hasControl($reference)){
					return $this->dispatcher->control($reference, $data, $this, self::CALL_HIERARCHY, null, $options);
				}
			}
			throw new Dispatcher\Exception\Control('Not found suitable bubble for action "'.$action.'"');
		}

		/**
		 * @param $data
		 * @param $action
		 * @param array $options
		 * @return mixed
		 * @throws Dispatcher\Exception\Control
		 */
		public function callParent($data, $action = null, $options = null){
			if($this->initiator instanceof Process){
				$reference = $this->initiator->reference;
				if($action!==null){
					$reference['action'] = $action;
				}
				return $this->dispatcher->control($reference, $data, $this, self::CALL_HIERARCHY, null, $options);
			}else{
				throw new Dispatcher\Exception\Control('Call Parent: initiator is not Process');
			}
		}

		/**
		 * @param $reference
		 * @param null $params
		 * @return mixed|void
		 * @throws Exception\Forwarded
		 */
		public function forward($reference, $params = null){
			if(in_array($this->initiator_type,[self::CALL_ROUTING,self::CALL_FORWARD])){
				$this->dispatcher->forward($reference, (array)$params, $this);
			}
		}

		/**
		 * @param $reference
		 * @param array $params
		 * @param int $code
		 * @param bool $exit
		 */
		public function redirect($reference, array $params = null, $code = null, $exit = false){
			$link = $this->getRouter()->generateLink($params,$reference);
			$this->dispatcher->response->setRedirect($link, $code?$code:302);
		}


		/**
		 * @param null $params
		 * @param null $reference
		 * @return mixed
		 */
		public function linkTo($reference = null, $params = null){
			return $this->getRouter()->generateLink($params,$reference);
		}

		/**
		 * @param $routeName
		 * @param null $params
		 * @param null $reference
		 * @return mixed
		 */
		public function linkBy($routeName, $params = null, $reference = null){
			return $this->getRouter()->generateLinkBy($routeName, $params, $reference);
		}

		/**
		 * @param $params
		 * @return mixed
		 */
		public function linkMain($params = null){
			return $this->getRouter()->generateLinkToMain($params);
		}


		/**
		 * @param mixed $key
		 * @return mixed
		 * @throws NeedIntroduce
		 */
		public function __get($key){
			if(array_key_exists($key, $this->params)){
				return $this->params[$key];
			}else{
				if($this->stage !== self::STAGE_RENDERING){
					throw new NeedIntroduce('Required param "'.$key.'"');
				}
				return null;
			}
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function __isset($key){
			return array_key_exists($key, $this->params);
		}

		/**
		 * @param $key
		 * @param $value
		 * @return mixed
		 */
		public function __set($key, $value){
			throw new \LogicException('Not Effect');
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function __unset($key){
			throw new \LogicException('Not Effect');
		}

		/**
		 * @param $result
		 * @param null $stage
		 * @param null $state
		 * @return $this
		 */
		public function setResult($result, $stage = null, $state = null){
			$this->result = $result;
			if($stage) $this->stage = $stage;
			if($state) $this->state = $state;
			return $this;
		}


		/**
		 * @return mixed
		 */
		public function getResult(){
			return $this->result;
		}

		/**
		 * @return bool
		 */
		public function hasErrors(){
			// TODO: Implement hasErrors() method.
		}




		/**
		 * @param array $options
		 * @param bool|false|false $merge
		 * @return mixed
		 */
		public function setOptions(array $options = [ ], $merge = false){
			$this->options = $merge?array_replace_recursive($this->options, $options):$options;
			return $this;
		}

		/**
		 * @return array
		 */
		public function getOptions(){
			return $this->options;
		}

		/**
		 * @param mixed $key
		 * @param $value
		 * @return $this
		 */
		public function setOption($key, $value){
			$this->options[$key] = $value;
			return $this;
		}

		/**
		 * @param mixed $key
		 * @param $default
		 * @return mixed
		 */
		public function getOption($key, $default = null){
			return isset($this->options[$key])?$this->options[$key]: $default;
		}

		/**
		 * @param mixed $key
		 * @return bool
		 */
		public function hasOption($key){
			return isset($this->options[$key]);
		}



		/**
		 * @param mixed $type
		 * @param $data
		 * @return $this
		 */
		public function setTask($type, $data){
			$this->tasks[$type] = $data;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getTasks(){
			return $this->tasks;
		}


		/**
		 * @param mixed $type
		 * @return null|mixed
		 */
		public function getTask($type){
			return isset($this->tasks[$type])?$this->tasks[$type]:null;
		}

		/**
		 * @return bool
		 */
		public function hasTasks(){
			return !empty($this->tasks);
		}


		/**
		 * @return mixed
		 */
		public function startBuffering(){
			if(!$this->buffering){
				ob_start();
				$this->buffered = null;
				$this->buffering = true;
			}
		}

		/**
		 * @return mixed
		 */
		public function endBuffering(){
			if($this->buffering){
				$this->buffered = ob_get_clean();
				$this->buffering = false;
			}
		}

		/**
		 * @return mixed
		 */
		public function getBuffered(){
			return $this->buffered;
		}



		const FLASH_INFO        = 'info';
		const FLASH_SUCCESS     = 'success';
		const FLASH_FAILURE     = 'failure';
		const FLASH_NOTICE      = 'notice';
		const FLASH_WARNING     = 'warning';


		public function setFlash($category, $value, $type = self::FLASH_INFO){}

		public function hasFlash($category){}

		public function getFlash($category){}

	}
}

