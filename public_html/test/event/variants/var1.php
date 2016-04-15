<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 29.03.2016
 * Time: 5:01
 */

namespace test\event\variants\var1;


/**
 *
 * Опишу общую структуру системы событий:
 * Observable
 * Event - Событие
 * Listener
 *
 *      Observable
 *      Event  <-   Listener    Listener Listener
 *      Event  <-   Listener    Listener Listener
 *      Event  <-   Listener    Listener
 *      Event  <-   {Listener}  Listener Listener
 *        |              |
 *        |          Observable
 *        |         Event   <-  Listener Listener Listener Listener
 *        |         Event   <-  Listener Listener
 *        |         Event   <-  Listener Listener
 *        \         Event   <-  Listener
 *         -------  {Event}  <- Listener  Listener Listener Listener Listener
 *                  {Event}  <- Listener
 *                  {Event}  <- Listener
 *                  {Event}  <- Listener
 *
 */

/**
 * Interface IEvent
 * @package test\event\variants\var1
 *
 * Событие может быть предвестником действия {Action}
 * Можно прекратить {Stoppable}
 * Может собирать значения {Collector}
 */

interface IEventPropagation{

	public function isStopped();

	public function getEvent();

	public function getCollected();

}

interface IEvent{

	public function getName();

	public function isStoppable();

	public function isStopped();

	public function isCollector();

}

interface IEventCollector{

	public function send();

	public function getCollected();

}

interface IEventAction{

	public function getAction();

}

interface IListener{

	public function invoke(IEvent $event);

}

interface IListenerEventPreferred{

	public function isPreferredEvent(IEvent $event);

}

/**
 * Class Event
 * @package test\event\variants\var1
 */
class Event{

	protected $name;

	protected $stoppable;

	protected $collector;


	public function setName($name){
		$this->name = $name;
		return $this;
	}

	public function getName(){
		return $this->name;
	}



	public function setStoppable($stoppable = true){
		$this->stoppable = (bool)$stoppable;
		return $this;
	}

	public function isStoppable(){
		return $this->stoppable;
	}


	public function setCollector($collector = true){
		$this->collector = boolval($collector);
		return $this;
	}

	public function isCollector(){
		return $this->collector;
	}


}
class Dispatcher{

	/** @var  Event */
	protected $event;

	/** @var  Listener[] */
	protected $listeners = [];

	/** @var  array  */
	protected $data = [];

	/** @var  callable */
	protected $action;



	public function setData(array $data){
		$this->data = $data;
	}

	public function getData(){
		return $this->data;
	}

	public function setEvent(Event $event){
		$this->event = $event;
		return $this;
	}

	public function getEvent(){
		return $this->event;
	}

	public function setAction(callable $action){
		$this->action = $action;
		return $this;
	}

	public function getAction(){
		return $this->action;
	}


	/**
	 * @param Listener[] $listeners
	 */
	public function addListeners(array $listeners){
		$clean = !$this->listeners;
		foreach($listeners as $listener){
			if(
				(!$clean || !in_array($listener, $this->listeners,true)) &&
			    $listener->isWantedEvent($this->event)
			){
				$this->listeners[] = $listener;
			}
		}
	}

	/**
	 * @return Listener[]
	 */
	public function getListeners(){
		return $this->listeners;
	}


	public function dispatch(){

		foreach($this->listeners as $listener){
			call_user_func($listener,$this);
		}

	}


}


class Listener{

	protected $event;

	protected $handler;

	protected $priority = 0;

	public function isWantedEvent(Event $event){
		return $event->getName() === $this->event;
	}

	public function setWantedEvent($event){
		if(!is_array($event)){
			$event = [$event];
		}
		foreach($event as & $e){
			if($e instanceof Event){
				$e = $e->getName();
			}
		}
		$this->event = $event;
	}

	public function setPriority($priority){
		$this->priority = $priority;
		return $this;
	}

	public function getPriority(){
		return $this->priority;
	}

	/**
	 * @param Event $event
	 * @param ...$arguments
	 */
	public function __invoke(Event $event, & ...$arguments){

	}


}

$event = new Event();
$event->setName('send');
$event->setStoppable(true);
$event->setCollector(true);


$listener = new Listener();
$listener->setWantedEvent('send');
$listener->setPriority(10);

$dispatcher = new Dispatcher();
$dispatcher->setEvent($event);
$dispatcher->setAction(function(){echo 'Message is sent!';});
$dispatcher->addListeners([$listener]);


$observable1 = new Observable();
$observable1->attachEventListener();





$observable2 = new Observable();
$observable2->delegate($observable1);


class Observable{

	public function attachEventListener(Listener $listener){

	}

	public function detachEventListener(Listener $listener){

	}

	public function fireEvent(){

	}

}
