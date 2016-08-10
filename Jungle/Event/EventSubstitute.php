<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.04.2016
 * Time: 13:06
 */
namespace Jungle\Event {

	use Jungle\Event;

	/**
	 * Class EventSubstitute
	 * @package Jungle\Event
	 */
	class EventSubstitute implements EventInterface{

		/** @var string|null */
		protected $name = null;

		/** @var  EventInterface */
		protected $event;

		/**
		 * @param EventInterface $event
		 * @param null $name
		 */
		public function __construct(EventInterface $event, $name = null){
			$this->setName($name);
			$this->event = $event;
		}

		/**
		 * @return string
		 */
		public function getName(){
			if($this->name === null){
				return $this->event->getName();
			}
			return $this->name;
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isCancelable(){
			return $this->event->isCancelable();
		}

		/**
		 * @return bool
		 */
		public function isStoppable(){
			return $this->event->isStoppable();
		}

		/**
		 * @return bool
		 */
		public function isCollector(){
			return $this->event->isCollector();
		}

		/**
		 * @return bool
		 */
		public function isCollectorDynamic(){
			return $this->event->isCollectorDynamic();
		}

		/**
		 * @return bool
		 */
		public function isActionChangeAllowed(){
			return $this->event->isActionChangeAllowed();
		}

		/**
		 * @return bool
		 */
		public function isDataReferencesAllowed(){
			return $this->event->isDataReferencesAllowed();
		}

		/**
		 * @param null|string $key
		 * @return \stdClass
		 */
		public function getMemory($key = null){
			return $this->event->getMemory($key);
		}

		/**
		 * @param ListenerAggregateInterface $initiator
		 * @param array|null $data
		 * @param callable|null $action
		 * @return EventProcess
		 */
		public function invoke(ListenerAggregateInterface $initiator, array $data = null, callable $action = null){
			return $this->event->invoke($initiator, $data, $action);
		}
	}
}

