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
	use Jungle\Application\Dispatcher\Process\ProcessInitiatorInterface;
	use Jungle\Application\Notification\Responsible\NeedIntroduce;
	use Jungle\Application\Router\RoutingInterface;

	/**
	 * Class Process
	 * @package Jungle\Application\Dispatcher\Controller
	 */
	class Process implements ProcessInterface, ProcessInitiatorInterface{

		const STATUS_SUCCESS    = 'success';
		const STATUS_CANCELED   = 'canceled';
		const STATUS_DETAINED   = 'detained';
		const STATUS_FAILURE    = 'failure';




		/** @var  Dispatcher */
		protected $dispatcher;

		/** @var  bool */
		protected $initiator_external;

		/** @var  \Jungle\Application\Dispatcher\Process\ProcessInitiatorInterface|RoutingInterface|ProcessInterface|null */
		protected $initiator;

		/** @var  ModuleInterface|null */
		protected $module;

		/** @var  object|ControllerInterface|ControllerManuallyInterface */
		protected $controller;

		/** @var  mixed */
		protected $reference;

		/** @var  array */
		protected $params = [];


		/** @var string  */
		protected $status = 'success';


		/** @var  bool */
		protected $completed = false;

		/** @var  bool */
		protected $canceled = false;

		/**  TODO impl.
		 * @var bool
		 */
		protected $failure = false;



		protected $rendering = false;


		/** @var  mixed */
		protected $result = null;

		/** @var  bool */
		protected $output_buffering = false;

		/** @var  string|null */
		protected $output_buffer;

		/** @var  array  */
		protected $options = [];

		/** @var  array  */
		protected $tasks = [];

		/**
		 * Process constructor.
		 * @param Dispatcher $dispatcher
		 * @param object|ControllerInterface|ControllerManuallyInterface $controller
		 * @param array $params
		 * @param mixed $reference
		 * @param ModuleInterface $module
		 * @param \Jungle\Application\Dispatcher\Process\ProcessInitiatorInterface|RoutingInterface|ProcessInterface $initiator
		 */
		public function __construct(
				Dispatcher $dispatcher,
				$controller,
				array $params,
				$reference,
				ModuleInterface $module = null,
				ProcessInitiatorInterface $initiator = null
		){
			$this->module				= $module;
			$this->dispatcher 			= $dispatcher;
			$this->initiator_external 	= $initiator instanceof RoutingInterface;
			$this->initiator 			= $initiator;
			$this->controller 			= $controller;
			$this->params 				= $params;
			$this->reference 			= $reference;
		}

		/**
		 * @param bool|true $rendering
		 * @return $this
		 */
		public function setRendering($rendering = true){
			$this->rendering = $rendering;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isRendering(){
			return $this->rendering;
		}

		/**
		 * @return ProcessInterface|null
		 */
		public function getRoot(){
			$initiator = $this->getInitiator();
			if(!$initiator){
				return null;
			}
			if($initiator instanceof ProcessInterface){
				return $initiator;
			}else{
				return $initiator->getRoot();
			}
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

			$module = $this->reference['module']?$this->reference['module']:false;
			$controller = $this->reference['controller']?$this->reference['controller']:false;
			$action = $this->reference['action']?$this->reference['action']:false;
			if($module){
				return '#' . $module . ':' . $controller . ':' . $action;
			}else{
				if($controller){
					return $controller . ':' . $action;
				}else{
					return  $action;
				}
			}
		}

		/**
		 * @param array $required
		 * @param bool $onlyRequired
		 * @return array
		 * @throws NeedIntroduce
		 */
		public function getParams(array $required = null, $onlyRequired = false){
			if($required!==null && !$this->rendering && $required !== array_intersect($required, array_keys($this->params))){
				throw new NeedIntroduce();
			}
			if($required!==null && $onlyRequired){
				return array_intersect_key(array_flip($required), $this->params);
			}
			return $this->params;
		}

		/**
		 * @param $key
		 * @param null $default
		 * @return mixed
		 */
		public function getParam($key, $default = null){
			return array_key_exists($key, $this->params)?$this->params[$key]:$default;
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasParam($key){
			return array_key_exists($key, $this->params);
		}

		/**
		 * @param $key
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
		 * HMVC-Architecture
		 * @return mixed
		 */
		public function isExternal(){
			return $this->initiator_external;
		}

		/**
		 * @return ProcessInterface|RoutingInterface|null
		 */
		public function getInitiator(){
			return $this->initiator;
		}

		/**
		 * @return RoutingInterface|null
		 */
		public function getRouting(){
			$initiator = $this->initiator;
			if(!$initiator){
				return null;
			}
			if($initiator instanceof RoutingInterface){
				return $initiator;
			}else{
				return $initiator->getRouting();
			}
		}


		/**
		 * @return \Jungle\Application\RouterInterface|null
		 */
		public function getRouter(){
			$routing = $this->getRouting();
			if(!$routing){
				return null;
			}
			return $routing->getRouter();
		}


		/**
		 * @return ProcessInterface|null
		 */
		public function getParent(){
			return $this->initiator_external? null : $this->initiator;
		}

		/**
		 * @param $reference
		 * @param $data
		 * @param array $options
		 * @return mixed
		 * @throws Dispatcher\Exception\Control
		 */
		public function call($reference, $data = null, array $options = null){
			$reference = Reference::normalize($reference, null, false);
			return $this->dispatcher->control($reference, $data, $options, $this);
		}

		/**
		 * @param $reference
		 * @param $data
		 * @param array $options
		 * @return mixed
		 * @throws Dispatcher\Exception\Control
		 */
		public function callIn($reference, $data = null, array $options = null){
			$reference = Reference::normalize($reference, null, false);
			$reference['module']		= $this->reference['module'];
			$reference['controller']	= $this->reference['controller'] . '.' . $reference['controller'];
			return $this->dispatcher->control($reference, $data, $options, $this);
		}

		/**
		 * @param $action
		 * @param $data
		 * @param array $options
		 * @return mixed
		 * @throws Dispatcher\Exception\Control
		 */
		public function callCurrent($action, $data, array $options = null){
			$reference = $this->reference;
			if(strcasecmp($reference['action'],$action)===0){
				throw new Dispatcher\Exception\Control('Executing current action not allowed');
			}
			$reference['action'] = $action;
			return $this->dispatcher->control($reference, $data, $options, $this);
		}

		/**
		 * @param $action
		 * @param $data
		 * @param array|null $options
		 * @return Process|mixed
		 * @throws Exception\Control
		 */
		public function callBubble($action, $data, array $options = null){
			$reference = $this->reference;
			if(strcasecmp($reference['action'],$action)===0){
				throw new Dispatcher\Exception\Control('Executing current action not allowed');
			}
			$references = Reference::generateSequence($reference,[ 'action' => Reference::SAFE_STRICT ]);
			foreach($references as $reference){
				if($this->dispatcher->hasControl($reference)){
					return $this->dispatcher->control($reference, $data, $options, $this);
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
		public function callBubbleWithController($action, $data, array $options = null){
			$reference = $this->reference;
			if(strcasecmp($reference['action'],$action)===0){
				throw new Dispatcher\Exception\Control('Executing current action not allowed');
			}
			$references = Reference::getSequence($reference,[ 'action' => Reference::SAFE_STRICT ],'controller');
			foreach($references as $reference){
				if($this->dispatcher->hasControl($reference)){
					return $this->dispatcher->control($reference, $data,$options, $this);
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
		public function callParent($data, $action = null, array $options = null){
			if($this->initiator instanceof Process){
				$reference = $this->initiator->reference;
				if($action!==null){
					$reference['action'] = $action;
				}
				return $this->dispatcher->control($reference, $data, $options, $this);
			}else{
				throw new Dispatcher\Exception\Control('Call Parent: initiator is not Process');
			}
		}

		/**
		 * @param $reference
		 * @param null $data
		 * @return void
		 */
		public function forward($reference, $data = null){
			if($this->initiator_external){
				$this->dispatcher->forward($reference, $data = null);
			}
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
		 * @param $key
		 * @return mixed
		 * @throws NeedIntroduce
		 */
		public function __get($key){
			if(array_key_exists($key, $this->params)){
				return $this->params[$key];
			}else{
				if(!$this->rendering){
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
		 * @return bool
		 */
		public function isCompleted(){
			return $this->completed;
		}

		/**
		 * @param bool|true $completed
		 * @return $this
		 */
		public function setCompleted($completed = true){
			$this->completed = $completed;
			return $this;
		}

		/**
		 * @param $result
		 * @param bool $completed
		 * @return $this
		 */
		public function setResult($result, $completed = true){
			$this->result = $result;
			$this->completed = $completed;
			return $this;
		}

		/**
		 * @return $this
		 */
		public function cancel(){
			$this->canceled = true;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isCanceled(){
			return $this->canceled;
		}

		/**
		 * @return mixed
		 */
		public function getResult(){
			return $this->result;
		}

		/**
		 * @return mixed
		 */
		public function startOutputBuffering(){
			if(!$this->output_buffering){
				ob_start();
				$this->output_buffer = null;
				$this->output_buffering = true;
			}
		}

		/**
		 * @return mixed
		 */
		public function endOutputBuffering(){
			if($this->output_buffering){
				$this->output_buffer = ob_get_clean();
				$this->output_buffering = false;
			}
		}

		/**
		 * @return mixed
		 */
		public function getOutputBuffer(){
			return $this->output_buffer;
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
		 * @param $key
		 * @param $value
		 * @return $this
		 */
		public function setOption($key, $value){
			$this->options[$key] = $value;
			return $this;
		}

		/**
		 * @param $key
		 * @param $default
		 * @return mixed
		 */
		public function getOption($key, $default = null){
			return isset($this->options[$key])?$this->options[$key]: $default;
		}

		/**
		 * @param $key
		 * @return bool
		 */
		public function hasOption($key){
			return isset($this->options[$key]);
		}



		/**
		 * @param $type
		 * @param $data
		 * @return $this
		 */
		public function setTask($type, $data){
			$this->tasks[$type] = $data;
			$this->canceled = true;
			return $this;
		}

		/**
		 * @param $type
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


	}
}

