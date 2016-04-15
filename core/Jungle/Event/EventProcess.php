<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.04.2016
 * Time: 0:27
 */
namespace Jungle\Event {

	use Jungle\Event;
	use Jungle\Event\Exception\Handling;
	use Jungle\Event\Listener\ListenerProcess;

	/**
	 * Class EventProcess
	 * @package Jungle\Event
	 */
	class EventProcess{

		/** @var  Event  */
		protected $_event;

		/** @var  ListenerAggregateInterface */
		protected $_initiator;

		/** @var int  */
		protected $_handled_count = 0;


		/** @var null|callable */
		protected $_action;

		/** @var null|callable */
		protected $_action_default;

		/** @var array  */
		protected $_additional_actions = [];


		/** @var  object */
		protected $_data;

		/** @var  array  */
		protected $_collected = [];

		/** @var  bool|null  */
		protected $_collector_enabled = null;

		/** @var  bool  */
		protected $_stopped = false;

		/** @var bool  */
		protected $_canceled = false;

		/** @var  \stdClass[] */
		protected $_memories = [];


		/**
		 * @param Event $event
		 * @param ListenerAggregateInterface $initiator
		 * @param array $data
		 * @param callable|null $action
		 */
		public function __construct(
			Event $event,
			ListenerAggregateInterface $initiator,
			array $data = null,
			callable $action = null
		){
			$this->_event           = $event;
			$this->_initiator       = $initiator;
			$this->_action          = $action;
			$this->_action_default  = $action;

			if($data!==null){
				$d = $this->getData();
				if($event->isDataReferencesAllowed()){
					foreach($data as $k => & $v){$d->{$k} = & $v;}
				}else{
					foreach($data as $k => & $v){$d->{$k} = $v;}
				}
			}
			$this->process();
		}

		/**
		 * @throws Exception\Handling\Stop
		 */
		public function stop(){
			if($this->_event->isStoppable()){
				throw new Event\Exception\Handling\Stop;
			}
		}

		/**
		 * @return bool
		 */
		public function isStopped(){
			return $this->_stopped;
		}

		/**
		 * @throws Exception\Handling\Cancel
		 */
		public function cancel(){
			if($this->_event->isCancelable()){
				throw new Event\Exception\Handling\Cancel;
			}
		}

		/**
		 * @return bool
		 */
		public function isCanceled(){
			return $this->_canceled;
		}


		/**
		 * @param $callable
		 * @param ListenerProcess $level
		 * @param array $behind_arguments
		 */
		public function callListenerHandler($callable, ListenerProcess $level, array $behind_arguments = null){
			$args = [$level, $this];
			array_splice($args, 0, 0, $behind_arguments);
			call_user_func_array($callable,$args);
		}




		/**
		 * @param callable $action
		 * @return $this
		 */
		public function addAdditionalAction(callable $action){
			if(!in_array($action, $this->_additional_actions)){
				$this->_additional_actions[] = $action;
			}
			return $this;
		}

		/**
		 * @param callable $action
		 * @return $this
		 */
		public function changeAction(callable $action){
			if($this->_event->isActionChangeAllowed()){
				$this->_action = $action;
			}else{
				throw new \LogicException('Event, Action change is not allowed!');
			}
			return $this;
		}

		/**
		 * @return callable|null
		 */
		public function getAction(){
			return $this->_action;
		}

		/**
		 * @return bool
		 */
		public function collectorEnable(){
			if(!$this->_event->isCollector()){
				throw new \LogicException('Event is not included collector functions!');
			}
			if($this->_event->isCollectorDynamic()){
				$this->_collector_enabled = true;
			}else{
				throw new \LogicException('Event is not dynamic collector!');
			}
			return $this;
		}

		/**
		 * @return bool
		 */
		public function collectorDisable(){
			if($this->_event->isCollector()){
				if($this->_event->isCollectorDynamic()){
					$this->_collector_enabled = false;
				}else{
					throw new \LogicException('Event is not dynamic collector!');
				}
			}
			return $this;
		}

		/**
		 * @return bool|null
		 */
		public function isCollectorEnabled(){
			return is_bool($this->_collector_enabled)?$this->_collector_enabled:$this->_event->isCollector();
		}

		/**
		 * @param $value
		 * @return $this
		 */
		public function send($value){
			if($this->_event->isCollector()){
				$this->_collected[] = $value;
			}
			return $this;
		}

		/**
		 * @return array
		 */
		public function getCollected(){
			return $this->_collected;
		}

		/**
		 * @return $this
		 */
		public function restoreAction(){
			$this->_action = $this->_action_default;
			return $this;
		}

		/**
		 * @return ListenerAggregateInterface
		 */
		public function getInitiator(){
			return $this->_initiator;
		}

		/**
		 * @return Event
		 */
		public function getEvent(){
			return $this->_event;
		}

		/**
		 * @return object
		 */
		public function getData(){
			if(!$this->_data){
				$this->_data = new \stdClass();
			}
			return $this->_data;
		}


		public function __set($data_key, $value){
			$this->getData()->{$data_key} = $value;
		}
		public function __get($data_key){
			$d = $this->getData();
			return isset($d->{$data_key})?$d->{$data_key}:null;
		}
		public function __isset($data_key){
			$d = $this->getData();
			return isset($d->{$data_key});
		}
		public function __unset($data_key){
			return $this->getData()->{$data_key};
		}


		/**
		 * @return int
		 */
		public function getHandledCount(){
			return $this->_handled_count;
		}

		/**
		 * @param null|string $memory_key
		 * @return \stdClass
		 */
		public function getMemory($memory_key = null){
			if(is_object($memory_key)){
				return $memory_key;
			}
			if($memory_key === null){
				return $this->getMemory('default');
			}
			if(!isset($this->_memories[$memory_key])){
				$this->_memories[$memory_key] = new \stdClass();
			}
			return $this->_memories[$memory_key];
		}

		/**
		 * @param $memory_key
		 * @return \stdClass
		 */
		public function getEventMemory($memory_key = null){
			return $this->_event->getMemory($memory_key);
		}

		/**
		 * @param EventInterface $event
		 * @param $listener
		 * @return bool
		 */
		public static function checkEventDesired(EventInterface $event, $listener){
			return !$listener instanceof ListenerInterface || $listener->isDesiredEvent($event);
		}

		/**
		 * @param $listener
		 * @return bool
		 */
		public static function checkListenerLost($listener){
			return $listener instanceof ListenerInterface && !$listener->isActive() && !$listener->isPermanent();
		}

		/**
		 * @param ListenerAggregateInterface $aggregate
		 * @param EventInterface $event
		 * @param int $depth
		 * @return $this
		 * @throws Handling
		 * @throws \Exception
		 */
		public function branch(ListenerAggregateInterface $aggregate,EventInterface $event=null, $depth=0){
			if($aggregate !== $this->_initiator){
				//try{
					$this->_handle($aggregate,$event,$depth);
				//}catch(Event\Exception\Handling\SkipBranch $exception){}
			}
			return $this;
		}


		/**
		 * @return bool
		 */
		public function process(){
			$this->_beforeProcess();
			try{
				$this->_handle($this->getInitiator());
				$this->_doAction();
			}catch(Handling $exception){
				if($exception instanceof Handling\Cancel){
					if($this->_event->isCancelable()){
						$this->_canceled = true;
					}else{
						throw new \LogicException('Event is not cancelable!');
					}
				}
				if($exception instanceof Handling\Stop){
					if($this->_event->isStoppable()){
						$this->_stopped = true;
					}else{
						throw new \LogicException('Event is not stoppable!');
					}
				}
			}
			$this->_afterProcess();
		}

		/**
		 * Template method before invoking start handling
		 */
		protected function _beforeProcess(){}

		/**
		 * Template method after invoking end handling
		 */
		protected function _afterProcess(){}

		/**
		 * @param ListenerAggregateInterface $aggregate
		 * @param EventInterface $event
		 * @param int $depth
		 * @throws Handling
		 * @throws \Exception
		 */
		protected function _handle(ListenerAggregateInterface $aggregate,EventInterface $event = null, $depth = 0){
			$listeners = & $aggregate->getEventListeners();
			$deep = $depth + 1;
			$event = $event?:$this->getEvent();
			$level = ListenerProcess::getListenerProcess($this, $event, $aggregate, $deep);
			try{
				foreach($listeners as $i => $listener){
					$level->initializeListener($listener);
					if($this->_beforeListenerHandle($level)!==false){
						try{
							$this->callListenerHandler($listener,$level);
						}catch(Handling\SkipListener $e){}
						$this->_handled_count++;
						if($this->_checkListenerLost($level)){
							unset($listeners[$i]);
						}
						$this->_afterListenerHandle($level);
					}
				}
				$level->clear();
				$this->_afterHandling($listeners);
			}catch(Handling $exception){
				$level->clear();
				$this->_afterHandling($listeners);
				throw $exception;
			}
		}

		public static function getEventProcess(
			Event $event,
			ListenerAggregateInterface $initiator,
			array $data = null,
			callable $action = null
		){

		}

		/**
		 * @param ListenerProcess $level
		 * @return bool
		 */
		protected function _beforeListenerHandle(ListenerProcess $level){
			$listener = $level->getListener();
			if(!$this->checkEventDesired($level->getEvent(), $listener)){
				return false;
			}
			if($listener instanceof ListenerInterface && !$listener->isActive()){
				return false;
			}
			return true;
		}

		/**
		 * @param ListenerProcess $deleted
		 */
		protected function _afterListenerHandle(ListenerProcess $deleted){}

		/**
		 * @param ListenerProcess $listener
		 * @return bool
		 */
		protected function _checkListenerLost(ListenerProcess $listener){
			return $listener->isRemoved() || $this->checkListenerLost($listener->getListener());
		}

		/**
		 * @param & $listeners
		 */
		protected function _afterHandling( & $listeners ){
			$listeners = array_values($listeners);
		}


		/**
		 * Do action
		 */
		protected function _doAction(){
			if($this->_action!==null && !$this->_canceled){
				call_user_func($this->_action, $this);
			}
			if($this->_additional_actions && !$this->_canceled){
				foreach($this->_additional_actions as $action){
					call_user_func($action, $this);
				}
			}
		}

	}
}

