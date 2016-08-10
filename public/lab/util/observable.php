<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 21.03.2016
 * Time: 16:43
 */

/**
 * Observable:
 * Есть объект Observable, объект может вызывать внутрение события, к нему можно подключить обработчики этих событий
 */


class Observable{

	/** @var Event[] */
	protected $events = [];

	public function defineEvents(){

	}

	public function getEvent($event){

	}

	public function removeEvent($event){

	}

	public function addEvent($event){

	}

	public function hasEvent($event){

	}

	public function addEventListener($event, $listener){

	}

	public function removeEventListener($event, $listener){

	}

	public function hasEventListener($event, $listener){

	}

}

/**
 * Event (Событие)
 *
 * Событие можно вызвать в отдельном источнике кода, либо же сделать событие комплексом этих вызовов.
 *
 * event = record_delete
 * before.record_delete
 * after.record_delete
 *
 *
 */

/**
 * Class Event
 */
class Event{

	/** @var  string */
	protected $name;

	/** @var bool  */
	protected $stoppable = false;

	/** @var EventListener[]  */
	protected $listeners = [];

	/**
	 * @param EventListener $listener
	 */
	public function addListener(EventListener $listener){

	}

	/**
	 * @param EventListener $listener
	 */
	public function searchListener(EventListener $listener){

	}

	/**
	 * @param EventListener $listener
	 */
	public function removeListener(EventListener $listener){

	}

}

/**
 * Class ActiveEvent
 */
class ActiveEvent{

	/** @var  Observable */
	protected $observable;

	/** @var  Event */
	protected $event;

	/** @var  array */
	protected $data = [];

	/** @var  bool */
	protected $stopped = false;

	/**
	 * @param Event $event
	 */
	public function __construct(Event $event){
		$this->event = $event;
	}

	/**
	 * @return Event
	 */
	public function getEvent(){
		return $this->event;
	}

	/**
	 *
	 */
	public function resume(){

	}

}


/**
 * Event Listener (Слушатель события)
 */

/**
 * Class EventListener
 */
class EventListener{

	/**
	 * @param ActiveEvent $event
	 */
	public function __invoke(ActiveEvent $event){

	}

}