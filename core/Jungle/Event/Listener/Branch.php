<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 26.03.2016
 * Time: 3:21
 */
namespace Jungle\Event\Listener {

	use Jungle\Event;
	use Jungle\Event\EventInterface;
	use Jungle\Event\ListenerAggregateInterface;
	use Jungle\Event\ListenerInterface;
	use Jungle\Util\Value\Massive;

	/**
	 * Class ListenerSet
	 * @package Jungle\Util
	 */
	class Branch extends Event\Listener implements ListenerAggregateInterface{

		/** @var  callable */
		protected $_before_handler;

		/** @var  callable */
		protected $_after_handler;

		/** @var  ListenerInterface[] */
		protected $_listeners   = [];

		/** @var  bool */
		protected $_sorted     = false;


		/**
		 * @param array $listeners
		 * @param null $priority
		 * @param bool $permanent
		 */
		public function __construct(array $listeners, $priority = null, $permanent = false){
			foreach($listeners as $l){
				if($l instanceof ListenerInterface || $l instanceof \Closure){
					$this->_listeners[] = $l;
				}
			}
			$this->setPriority($priority);
			$this->setPermanent($priority);
		}


		/**
		 * @param EventInterface $event
		 * @return bool
		 */
		public function isDesiredEvent(EventInterface $event){
			return true;
		}

		/**
		 * @param Event\EventProcess $dispatching
		 * @param ListenerProcess $listener
		 * @return bool|void
		 */
		public function __invoke(ListenerProcess $listener, Event\EventProcess $dispatching){
			if($this->_before_handler){
				$dispatching->callListenerHandler($this->_before_handler, $listener);
			}
			$dispatching->branch($this,$listener->getDepth());
			if($this->_after_handler){
				$dispatching->callListenerHandler($this->_after_handler, $listener);
			}
		}

		/**
		 * @return bool
		 */
		public function isActive(){
			return $this->_before_handler || !empty($this->_listeners);
		}


		/**
		 * @return ListenerInterface[]
		 */
		public function & getEventListeners(){
			if(!$this->_sorted){
				$this->_sorted = true;
				Massive::sortColumn($this->_listeners,function(ListenerInterface $listener){
					return $listener->getPriority();
				},false);
			}
			return $this->_listeners;
		}


		/**
		 * @return $this
		 */
		public function purgeEventListeners(){
			$this->_listeners = [];
			return $this;
		}

		/**
		 * @param callable|ListenerInterface $listener
		 * @return $this
		 */
		public function attachEventListener(callable $listener){
			if($this->searchEventListener($listener)===false){
				$this->_listeners[] = $listener;
				$this->_sorted = false;
			}
			return $this;
		}

		/**
		 * @param callable|ListenerInterface $listener
		 * @return bool|int
		 */
		public function searchEventListener(callable $listener){
			return array_search($listener, $this->_listeners, true);
		}

		/**
		 * @param callable|ListenerInterface $listener
		 * @return $this
		 */
		public function detachEventListener(callable $listener){
			if(($i = $this->searchEventListener($listener)) !== false){
				array_splice($this->_listeners,$i,1);
			}
			return $this;
		}


	}
}

