<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.04.2016
 * Time: 2:35
 */
namespace Jungle\Event\Listener {

	use Jungle\Event\EventInterface;
	use Jungle\Event\EventProcess;
	use Jungle\Event\Exception\Handling\SkipBranch;
	use Jungle\Event\Exception\Handling\SkipListener;
	use Jungle\Event\ListenerAggregateInterface;
	use Jungle\Event\ListenerInterface;

	/**
	 * Class Listening
	 * @package Jungle\Event\ListenerInterface
	 */
	class ListenerProcess{

		/**
		 * @var ListenerProcess[]
		 * Object POOL! for many object collection performance, lazy creation (already existing storing)instances for
		 * next listener processing
		 */
		protected static $_idle_stack = [];


		/** @var bool */
		protected $removed_from_aggregate = false;

		/** @var  ListenerInterface|callable */
		protected $listener;

		/** @var  EventProcess */
		protected $dispatching;

		/** @var EventInterface */
		protected $event;

		/** @var  ListenerAggregateInterface */
		protected $aggregate;

		/** @var int  */
		protected $depth = 0;

		/**
		 * @param EventProcess $dispatching
		 * @param EventInterface $event
		 * @param ListenerAggregateInterface $aggregate
		 * @param $depth
		 * @return $this
		 */
		protected function _init(
			EventProcess $dispatching,
			EventInterface $event,
			ListenerAggregateInterface $aggregate,
			$depth
		){
			$this->dispatching  = $dispatching;
			$this->aggregate    = $aggregate;
			$this->depth        = $depth;
			$this->event        = $event;
			$this->removed_from_aggregate = false;
			return $this;
		}

		/**
		 * @param $listener
		 * @return $this
		 */
		public function initializeListener($listener){
			$this->listener = $listener;
			$this->removed_from_aggregate = false;
			return $this;
		}

		/***
		 * @param EventProcess $dispatching
		 * @param EventInterface $event
		 * @param ListenerAggregateInterface $aggregate
		 * @param $depth
		 * @return ListenerProcess
		 */
		public static function getListenerProcess(
			EventProcess $dispatching,
			EventInterface $event,
			ListenerAggregateInterface $aggregate,
			$depth
		){
			$process = self::_shiftIdle();
			if($process !== null){
				return $process->_init($dispatching,$event,$aggregate,$depth);
			}else{
				$process = new ListenerProcess();
				return $process->_init($dispatching,$event,$aggregate,$depth);
			}
		}

		/**
		 *
		 */
		public static function idleReset(){
			self::$_idle_stack = [];
		}

		protected static function _toIdle(ListenerProcess $l){
			self::$_idle_stack[] = $l;
		}

		/**
		 * @return ListenerProcess|null
		 */
		protected static function _shiftIdle(){
			if(self::$_idle_stack){
				return array_shift(self::$_idle_stack);
			}
			return null;
		}

		/**
		 *
		 */
		public function clear(){
			$this->removed_from_aggregate   = false;
			$this->listener                 = null;
			$this->dispatching              = null;
			$this->aggregate                = null;
			$this->event                    = null;
			self::_toIdle($this);
		}





		/**
		 * @return int
		 */
		public function getDepth(){
			return $this->depth;
		}

		/**
		 *
		 */
		public function remove(){
			$this->removed_from_aggregate = true;
		}

		/**
		 * @throws SkipListener
		 */
		public function skip(){
			throw new SkipListener;
		}

		/**
		 * @throws SkipBranch
		 */
		public function skipBranch(){
			if($this->aggregate !== $this->dispatching->getInitiator()){
				throw new SkipBranch;
			}
		}


		/**
		 * @return bool
		 */
		public function isRemoved(){
			return $this->removed_from_aggregate;
		}

		/**
		 * @return callable|ListenerInterface
		 */
		public function getListener(){
			return $this->listener;
		}

		/**
		 * @return \Jungle\Event
		 */
		public function getEvent(){
			return $this->event?:$this->dispatching->getEvent();
		}

		/**
		 * @return EventProcess
		 */
		public function getDispatching(){
			return $this->dispatching;
		}

		/**
		 * @return ListenerAggregateInterface
		 */
		public function getAggregate(){
			return $this->aggregate;
		}

	}
}

