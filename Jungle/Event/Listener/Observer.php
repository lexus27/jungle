<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 26.03.2016
 * Time: 3:20
 */
namespace Jungle\Event\Listener {
	
	use Jungle\Event;
	use Jungle\Event\EventInterface;
	use Jungle\Event\ListenerAggregateInterface;
	use Jungle\Util\Value\String;
	
	/**
	 * Class Observer
	 * @package Jungle\Util
	 *
	 *
	 * Класс является представлением объекта содержащего методы-слушатели события
	 *
	 * class ItemsConvert{
	 *
	 *      protected $_storage;
	 *
	 *      protected $_storage_observer;
	 *
	 *      function __construct(){
	 *          // variant prefixed auto search method
	 *          $this->_storage_observer = new Observer($this, 'storage', null, 10);
	 *          // variant concrete event point callable
	 *          $this->_storage_observer = new Observer($this, 'storage', [
	 *              'load' => [ $this, 'storageLoad' ]
	 *          ], 10);
	 *          // variant concrete event closure handler
	 *          $this->_storage_observer = new Observer($this, 'storage', [
	 *              'load' => function(Event $event){
	 *                  return $this->storageLoad($event);
	 *              }
	 *          ], 10);
	 *
	 *      }
	 *
	 *      function setStorage(Storage $storage){
	 *          $this->_storage = $storage;
	 *          $storage->addEventListener($this->_storage_observer);
	 *      }
	 *
	 *      function storageLoad(Event $event){
	 *          //perfect code handling event firing
	 *          //$event->stop();    //Остановка обработчиков
	 *          //$event->skipBranch();         //Остановка ветки
	 *          //$event->cancel();             // Остановка действия в целом
	 *      }
	 * }
	 *
	 */
	class Observer extends Event\Listener{

		/** @var  string|null */
		protected $_prefix;

		/** @var  callable|null */
		protected $_before_handler;

		/** @var  callable|null */
		protected $_after_handler;

		/** @var  array */
		protected $_events;

		/** @var  object */
		protected $_object;


		/** @var  ListenerAggregateInterface|null */
		protected $_aggregate;

		/** @var  array */
		protected $_aggregate_event_aliases = [];

		/** @var  string */
		protected $_aggregate_prefix;

		/** @var  bool */
		protected $_aggregate_before;


		/**
		 * @param object $object
		 * @param null|string $prefix
		 * @param null|array|string $event_names
		 * @param null|float $priority
		 */
		public function __construct($object, $prefix = null, $event_names = null, $priority = null){
			if($event_names!==null){
				if(is_string($event_names)){
					$this->addPreferredEventName($event_names);
				}elseif(is_array($event_names)){
					foreach($event_names as $eName => $alias){
						if(is_int($eName)){
							$this->addPreferredEventName($alias, null);
						}else{
							$this->addPreferredEventName($eName, $alias);
						}
					}
				}else{
					throw new \InvalidArgumentException('Wrong argument $event_names');
				}
			}
			if(!is_object($object)){
				throw new \InvalidArgumentException('Wrong $object, must be object');
			}
			$this->_object   = $object;
			$this->_prefix   = strval($prefix);
			$this->_priority = floatval($priority);
		}


		/**
		 * @param callable $handler
		 * @return $this
		 */
		public function setBeforeHandler(callable $handler){
			$this->_before_handler = $handler;
			return $this;
		}

		/**
		 * @return callable|null
		 */
		public function getBeforeHandler(){
			return $this->_before_handler;
		}

		/**
		 * @param callable $handler
		 * @return $this
		 */
		public function setAfterHandler(callable $handler){
			$this->_after_handler = $handler;
			return $this;
		}

		/**
		 * @return callable|null
		 */
		public function getAfterHandler(){
			return $this->_after_handler;
		}

		/**
		 * @param array $event_aliases
		 * @return $this
		 */
		public function setAggregateEventAliases(array $event_aliases = null){
			$this->_aggregate_event_aliases = (array)$event_aliases;
			return $this;
		}

		/**
		 * @param ListenerAggregateInterface $aggregate
		 * @param null $prefix
		 * @param bool|false $before
		 * @return $this
		 */
		public function setAggregate(ListenerAggregateInterface $aggregate, $prefix = null, $before = false){
			$this->_aggregate = $aggregate;
			$this->_aggregate_prefix = $prefix;
			$this->_aggregate_before = boolval($before);
			$this->_aggregate_event_aliases = [];
			return $this;
		}

		/**
		 * @param EventInterface $event
		 * @return bool
		 */
		public function isDesiredEvent(EventInterface $event){
			$e_name = $event->getName();
			if(is_array($this->_events)){
				return array_key_exists($e_name,$this->_events);
			}else{
				return $this->_aggregate || method_exists($this->_object,$this->_prefix($e_name));
			}
		}

		/**
		 * @param Event\EventProcess $dispatching
		 * @param ListenerProcess $listener
		 * @return mixed
		 */
		public function __invoke(ListenerProcess $listener, Event\EventProcess $dispatching){
			$event = $listener->getEvent();//TODO $event = $dispatching->getEvent();
			$e_name = $event->getName();//TODO listener.getName();
			if(is_array($this->_events)){
				if(array_key_exists($e_name,$this->_events)){
					$handler = $this->_events[$e_name];
					if($handler){
						if($handler === true){
							$handler = str_replace('.','_',$e_name);
						}
						if(is_string($handler) && method_exists($this->_object,$handler)){
							$this->_call([$this->_object,$handler],$dispatching, $listener);
							return;
						}
						if(is_callable($handler)){
							$this->_call($handler,$dispatching,$listener);
							return;
						}
					}
				}else{
					return; // not preferred
				}
			}

			$method_name = $this->_prefix($e_name);
			if(method_exists($this->_object,$method_name)){
				$this->_call([$this->_object,$method_name],$dispatching,$listener);
				return;
			}elseif($this->_aggregate){
				$this->_call(null,$dispatching,$listener);
				return;
			}else{
				return;
			}

		}

		/**
		 * @param callable $callback
		 * @param Event\EventProcess $dispatching
		 * @param ListenerProcess $listener
		 */
		protected function _call($callback, Event\EventProcess $dispatching, ListenerProcess $listener){
			if($this->_aggregate && $this->_aggregate_before){
				$dispatching->branch($this->_aggregate, $this->_prepareEvent($listener), $listener->getDepth());
				// branch handle before
			}
			if($this->_before_handler){
				$dispatching->callListenerHandler($this->_before_handler, $listener);
			}
			if($callback){
				$dispatching->callListenerHandler($callback, $listener);
			}
			if($this->_after_handler){
				$dispatching->callListenerHandler($this->_after_handler, $listener);
			}
			if($this->_aggregate && !$this->_aggregate_before){
				$dispatching->branch($this->_aggregate, $this->_prepareEvent($listener),$listener->getDepth());
				// branch handle after
			}
		}

		/**
		 * @param ListenerProcess $level
		 * @return EventInterface|false
		 */
		protected function _prepareEvent(ListenerProcess $level){
			$eName = $level->getEvent()->getName();
			if(isset($this->_aggregate_event_aliases[$eName])){
				if(is_string($this->_aggregate_event_aliases[$eName])){
					$this->_aggregate_event_aliases[$eName] =
						new Event\EventSubstitute($level->getEvent(), $this->_aggregate_event_aliases[$eName]);
				}elseif(is_callable($this->_aggregate_event_aliases[$eName])){
					$this->_aggregate_event_aliases[$eName] = call_user_func($this->_aggregate_event_aliases[$eName]);
				}
				if($this->_aggregate_event_aliases[$eName] instanceof Event\EventInterface){
					return $this->_aggregate_event_aliases[$eName];
				}else{
					throw new \LogicException('Aggregate event alias "'.$eName.'" is not valid');
				}
			}elseif($this->_aggregate_prefix){
				$this->_aggregate_event_aliases[$eName]
					= new Event\EventSubstitute($level->getEvent(), $this->_aggregate_prefix . $eName);
				return $this->_aggregate_event_aliases[$eName];
			}
			return null;
		}

		/**
		 * @param $event_name
		 * @param $associated_method
		 * @return $this
		 */
		public function addPreferredEventName($event_name, $associated_method = null){
			if(!is_array($this->_events))$this->_events = [];
			if(is_string($associated_method) && !is_callable($associated_method) && !method_exists($this->_object, $associated_method)){
				throw new \LogicException(
					'Object of class "'.get_class($this->_object).'" not have method "'.$associated_method .'".'
				);
			}
			$this->_events[$event_name] = $associated_method;
			return $this;
		}

		/**
		 * @param string $prefix
		 * @return $this
		 */
		public function setMethodPrefix($prefix = ''){
			$this->_prefix = strval($prefix);
			return $this;
		}

		/**
		 * @param $event_name
		 * @return string
		 */
		protected function _prefix($event_name){
			return $this->_prefix . String::ucFirst($event_name);
		}



		/**
		 * @return bool
		 */
		public function isActive(){
			return true;
		}

		/**
		 * @return bool
		 */
		public function isPermanent(){
			return true;
		}

	}

}

