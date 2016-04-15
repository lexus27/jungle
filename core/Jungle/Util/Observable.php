<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 26.03.2016
 * Time: 0:03
 */
namespace Jungle\Util {

	use Jungle\Event;
	use Jungle\Event\IListener;
	use Jungle\Event\Listener;
	use Jungle\Util\Value\Massive;

	/**
	 * Trait Observable
	 * @package Jungle\Util
	 */
	trait Observable{

		/** @var Event[] */
		protected static $_observable_events = [];

		/** @var bool  */
		protected static $_observable_events_initialized = false;

		/** @var \Jungle\Event\IListener[]  */
		protected $_observable_listeners = [];


		protected static function initializeDefaultObservableEvents(){
			if(!static::$_observable_events_initialized){
				static::$_observable_events_initialized = true;
				static::_doInitializeDefaultObservableEvents();
			}
		}

		protected static function _addObservableEvent(Event $event){
			if(static::_searchObservableEvent($event)){

			}
		}

		protected static function _searchObservableEvent(Event $event){
			return array_search($event,static::$_observable_events,true);
		}

		protected static function _removeObservableEvent(Event $event){

		}

		protected static function _doInitializeDefaultObservableEvents(){

		}


		public function addEvent(Event $event){

		}

		public function searchEvent(Event $event){
			return array_search($event,static::$_observable_events,true);
		}

		public function isEventExists($event_name){

		}

		public function getEventByName($event_name){

		}

		public function removeEvent(Event $event){

		}

		/**
		 * @param \Jungle\Event\IListener|array|callable $listener
		 * @return $this
		 */
		public function attachEventListener($listener){
			if($listener instanceof IListener){
				$this->_observable_listeners[] = $listener;
			}elseif(is_callable($listener)){
				$this->_observable_listeners[] = new Listener($listener);
			}elseif(is_array($listener)){
				if(Massive::isAssoc($listener)){
					$this->_observable_listeners[] = new Listener(
						$listener['handler'],
						$listener['priority'],
						$listener['limit'],
						$listener['permanent']
					);
				}elseif(Massive::isIndexed($listener)){
					$this->_observable_listeners[] = new Listener(
						$listener[0],
						$listener[1],
						$listener[2],
						$listener[3]
					);

				}else{
					throw new \InvalidArgumentException(
						'Listener is invalid array definition , must be full indexed or full assoc'
					);
				}
			}else{
				throw new \InvalidArgumentException('Listener is invalid definition');
			}
			return $this;
		}

		public function hasEventListener(IListener $listener){
			return !!array_search($listener,$this->_observable_listeners,true);
		}

		/**
		 * @param \Jungle\Event\IListener $listener
		 * @return $this
		 */
		public function detachEventListener(IListener $listener){
			$i = array_search($listener,$this->_observable_listeners,true);
			if($i !==false){
				array_splice($this->_observable_listeners, $i , 1);
			}
			return $this;
		}

		/**
		 * @param $event_name
		 * @param &...$arguments
		 */
		public function invokeEvent($event_name, &...$arguments){

			if(is_array($event_name)){
				$event = new Event(
					$event_name['name'],
					$event_name['action']?:null,
					$event_name['stoppable']?:false,
					$event_name['collect_values']?:false,
					$event_name['dynamic_behaviour']?:false,
					$event_name['auto_reset_config']?:true,
					$event_name['references_allowed']?:false
				);
				$event->invoke($this->_observable_listeners,$arguments);
			}

		}

		public function invokeBeforeEvent($event_name,callable $action, &...$arguments){

		}

		protected function _getEvent($event,callable $action = null){
			if(!$event instanceof Event){
				$event = new Event($event,$action);
			}
		}

	}

}
