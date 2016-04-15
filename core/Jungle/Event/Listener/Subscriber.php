<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.04.2016
 * Time: 22:56
 */
namespace Jungle\Event\Listener {

	use Jungle\Event;
	use Jungle\Event\EventInterface;
	use Jungle\Event\EventProcess;
	use Jungle\Event\Listener;
	use Jungle\Util\Value\String;

	/**
	 * Class Subscriber
	 * @package Jungle\Event\ListenerInterface
	 */
	class Subscriber extends Listener{

		/** @var int  */
		protected $_use_limit = -1;

		/** @var int  */
		protected $_use_count = 0;

		/** @var  string|true|\Closure| */
		protected $_desired_event;

		/** @var  callable */
		protected $_handler;

		/** @var array  */
		protected $_args_behind = [];

		/**
		 * @param null $desired_event
		 * @param callable $handler
		 * @param array $arguments_behind
		 * @param null $priority
		 * @param int $use_limit
		 * @param bool $permanent
		 */
		public function __construct( $desired_event, callable $handler,
			$priority = null, $use_limit = null, $permanent = false, array $arguments_behind = []
		){
			$this->setHandler($handler);
			$this->setUseLimit($use_limit);
			$this->setPriority($priority);
			$this->setPermanent($permanent);
			$this->setDesiredEvent($desired_event);
			$this->setBehindArguments($arguments_behind);
		}

		/**
		 * @param array $arguments
		 * @return $this
		 */
		public function setBehindArguments(array $arguments){
			$this->_args_behind = $arguments;
			return $this;
		}

		/**
		 * @return array
		 */
		public function getBehindArguments(){
			return $this->_args_behind;
		}

		/**
		 * @param string|true|Event|array|\Closure $event
		 * @return $this
		 */
		public function setDesiredEvent($event){
			if($event === true || $event instanceof \Closure){
				$this->_desired_event = $event; // любые события или Injection Callable checker
			}else{
				if($event instanceof Event){
					$event = $event->getName();
				}
				if(!is_array($event)){
					$event = [$event];
				}
				$this->_desired_event = $event;
			}
			return $this;
		}

		/**
		 * @param EventInterface $event
		 * @return bool
		 */
		public function isDesiredEvent(EventInterface $event){
			if($this->_desired_event instanceof \Closure){
				return (bool) call_user_func($this->_desired_event, $event, $this);
			}

			return $this->_desired_event===true || String::match(
				$event->getName(),
				$this->_desired_event,
				true
			);
		}

		/**
		 * @return bool
		 */
		public function isActive(){
			return !$this->isUseLimitExceeded();
		}

		/**
		 * @param EventProcess $dispatching
		 * @param ListenerProcess $listener
		 * @return bool|void
		 * @throws \Exception
		 */
		public function __invoke(ListenerProcess $listener, EventProcess $dispatching){
			try{
				$dispatching->callListenerHandler($this->_handler, $listener, $this->getBehindArguments());
				$this->_use_count++;
			}catch(Event\Exception $e){
				$this->_use_count++;
				throw $e;
			}
		}

		/**
		 * @param callable $_handler
		 * @return $this
		 */
		public function setHandler(callable $_handler){
			$this->_handler = $_handler;
			return $this;
		}

		/**
		 * @return callable
		 */
		public function getHandler(){
			return $this->_handler;
		}

		/**
		 * @param int|null $limit
		 * @return $this
		 */
		public function setUseLimit($limit = null){
			$this->_use_limit = $limit===null?-1:intval($limit);
			return $this;
		}

		/**
		 * @return int
		 */
		public function getUseLimit(){
			return $this->_use_limit;
		}

		/**
		 * @param int $uses
		 * @return $this
		 */
		public function setUseCount($uses = 0){
			$this->_use_count = $uses;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getUseCount(){
			return $this->_use_count;
		}

		/**
		 * @return bool
		 */
		public function isUseLimitExceeded(){
			return $this->_use_limit>-1 && $this->_use_count >= $this->_use_limit;
		}


	}
}

