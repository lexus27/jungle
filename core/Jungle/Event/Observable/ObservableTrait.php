<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 28.03.2016
 * Time: 14:39
 */
namespace Jungle\Event\Observable {

	use Jungle\Event;
	use Jungle\Event\EventInterface;
	use Jungle\Event\ListenerInterface;
	use Jungle\Util\Value\Massive;

	/**
	 * Class ObservableTrait
	 * @package Jungle\Event\ObservableInterface
	 * @see ObservableInterface
	 */
	trait ObservableTrait{

		/** @var  \Jungle\Event\ListenerInterface[] */
		protected $_event_listeners        = [];

		/** @var  bool  */
		protected $_event_listeners_sorted = false;

		/** @var  EventInterface[] */
		protected static $_events = [];

		/** @var  bool  */
		protected static $_events_defined = false;

		/**
		 * @return void|array
		 */
		protected static function __define_events(){}

		/**
		 * @param array $events
		 */
		protected static function registerEventCollection(array $events){
			foreach($events as $key => $event){
				static::registerEvent($event,is_int($key)?null:$key);
			}
		}

		/**
		 * @param $definition
		 * @param null $key
		 *
		 * if is array (defaults) [
		 *      'name'              => '' (string),
		 *      'stoppable'         => false,
		 *      'collector'         => false,
		 *      'collector_dynamic' => false,
		 *      'data_references'   => false,
		 * ]
		 *
		 *
		 *
		 */
		protected static function registerEvent($definition, $key = null){
			if(is_array($definition)){
				$definition = array_replace([
					'name'                  => null,
					'stoppable'             => false,
					'cancelable'            => false,
					'collector'             => false,
					'collector_dynamic'     => false,
					'data_references'       => false
				],$definition);
				$name = $key?:$definition['name'];
				static::$_events[$name] = new Event(
					$name,
					$definition['stoppable'],
					$definition['cancelable'] ,
					$definition['collector'],
					$definition['collector_dynamic'],
					$definition['data_references']
				);
			}elseif($definition instanceof EventInterface){
				static::$_events[$definition->getName()] = $definition;
			}elseif(is_string($definition)){
				$name = $key?:$definition;
				static::$_events[$name] = new Event($name);
			}
		}

		/**
		 * @param array|string|Event $key
		 *
		 *
		 * @return Event
		 */
		protected static function _getEvent($key){
			if(!is_string($key)){
				throw new \InvalidArgumentException('event name must be "string", "'.gettype($key). '" given');
			}
			if(isset(static::$_events[$key])){
				return static::$_events[$key];
			}else{
				throw new \LogicException('Event "'.$key.'" is not have in "'.get_called_class().'"');
			}
		}

		/**
		 * @return Event\EventInterface[]
		 */
		public function getEvents(){
			return static::$_events;
		}

		/**
		 * @return string[]
		 */
		public function getEventNames(){
			return array_keys(static::$_events);
		}

		/**
		 * @return ListenerInterface[] &
		 */
		public function & getEventListeners(){
			if(!$this->_event_listeners_sorted){
				$this->_event_listeners_sorted = true;
				Massive::sortColumn($this->_event_listeners,function($listener){
					return $listener instanceof ListenerInterface?$listener->getPriority():0;
				},false);
			}
			return $this->_event_listeners;
		}


		/**
		 * @return $this
		 */
		public function purgeEventListeners(){
			$this->_event_listeners = [];
			return $this;
		}

		/**
		 * @param \Jungle\Event\ListenerInterface|callable $listener
		 * @param bool $checkExists
		 * @return $this
		 */
		public function attachEventListener(callable $listener, $checkExists = false){
			if(!$checkExists || $this->searchEventListener($listener)===false){
				$this->_event_listeners[]       = $listener;
				$this->_event_listeners_sorted  = false;
			}
			return $this;
		}

		/**
		 * @param ListenerInterface|callable $listener
		 * @return int|bool
		 */
		public function searchEventListener(callable $listener){
			return array_search($listener, $this->_event_listeners, true);
		}

		/**
		 * @param ListenerInterface|callable $listener
		 * @return $this
		 */
		public function detachEventListener(callable $listener){
			if(($i = $this->searchEventListener($listener)) !== false){
				array_splice($this->_event_listeners,$i,1);
			}
			return $this;
		}

		/**
		 * @param Event|array|string $event_key
		 * @param array|null $data
		 * @param callable|null $action
		 * @return bool
		 */
		public function invokeEvent($event_key,array $data = null,callable $action = null){
			if(!$this instanceof Event\ListenerAggregateInterface){
				throw new \LogicException('Instance of "'.get_class($this).'" must be implemented
				from "'. Event\ListenerAggregateInterface::class.'"');
			}
			/** @var \Jungle\Event\ListenerAggregateInterface|ObservableTrait $this */


			if(!static::$_events_defined){
				static::$_events_defined = true;
				$result = $this->__define_events();
				if(is_array($result)){
					$this->registerEventCollection($result);
				}
				unset($result);
			}

			$e = $this->_getEvent($event_key);
			if($e instanceof Event){
				return $e->invoke($this,$data,$action);
			}else{
				throw new \LogicException('Event not found by key "'.$event_key.'" in "'.get_class($this).'"');
			}
		}

		/**
		 * @var array
		 */
		protected $_event_branches = [];

		/**
		 * @param $key
		 * @param array $config
		 */
		protected function registerListenerBranch($key, array $config){
			$config = array_replace([
				'prefix'            => null,
				'events'            => null,
				'priority'          => null,
				'aggregate'         => null,
				'aggregate_before'  => false,
				'aggregate_prefix'  => null,
				'aggregate_aliases' => null,
			],$config);
			if(!$key){
				throw new \LogicException('Key empty!');
			}
			$prefix = $config['prefix'] === true?$key:$config['prefix'];
			$observer = new Event\Listener\Observer($this,$prefix,$config['events'],$config['priority']);
			if($config['aggregate']){

				$prefix= $config['aggregate_prefix']===true?$key.'.':$config['aggregate_prefix'];
				if($config['aggregate'] === true && $this instanceof Event\ListenerAggregateInterface){
					$observer->setAggregate($this,$prefix,$config['aggregate_before']);
					$observer->setAggregateEventAliases($config['aggregate_aliases']);
				}elseif($config['aggregate'] instanceof Event\ListenerAggregateInterface){
					$observer->setAggregate($config['aggregate'],$prefix,$config['aggregate_before']);
					$observer->setAggregateEventAliases($config['aggregate_aliases']);
				}
			}
			$this->_event_branches[$key] = $observer;
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		protected function getListenerBranch($key){
			return $this->_event_branches[$key];
		}

		/**
		 * @param $branch_key
		 * @param Event\ObservableInterface $observable
		 */
		protected function attachListenerBranchTo($branch_key, Event\ObservableInterface $observable){
			if(isset($this->_event_branches[$branch_key])){
				$observable->attachEventListener($this->_event_branches[$branch_key]);
			}else{
				throw new \LogicException(
					'Branch "'.$branch_key.'" is not registered in object "'.get_class($this) .'"'
				);
			}
		}

		/**
		 * @param $branch_key
		 * @param Event\ObservableInterface $observable
		 */
		protected function detachListenerBranchFrom($branch_key, Event\ObservableInterface $observable){
			if(isset($this->_event_branches[$branch_key])){
				$observable->detachEventListener($this->_event_branches[$branch_key]);
			}else{
				throw new \LogicException(
					'Branch "'.$branch_key.'" is not registered in object "'.get_class($this) .'"'
				);
			}
		}
	}
}
