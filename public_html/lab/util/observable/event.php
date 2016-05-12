<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.03.2016
 * Time: 0:05
 */
namespace Util;
use Jungle\Basic\INamed;
use Jungle\Util\Value\Massive;

/**
 * Class Observable
 * @package Util
 */
class Observable{

	protected $events = [];

	protected $listeners = [];

	protected $subscribers = [];

	public function attachEventListener($event, $listener){

	}

	/**
	 * @param $subscriber
	 */
	public function attachEventSubscriber($subscriber){
		$this->subscribers[] = $subscriber;
	}

	/**
	 * @param $subscriber
	 * @return $this
	 */
	public function detachEventSubscriber($subscriber){
		$index = array_search($subscriber, $this->subscribers,true);
		if($index!==false){
			array_splice($this->subscribers,$i,1);
		}
		return $this;
	}

}
class EventAggregate{

	protected $events = [];

	protected $event_listeners = [];

	protected $event_subscribers = [];

	public function hasEvent($event){

	}

	public function fireEvent($event_key){



	}

}

/**
 * Class Event
 * @package Util
 */
class Event implements INamed{

	protected static $event_name_delimiter = ':';

	/** @var  string */
	protected $name;

	/** @var  Event */
	protected $parent;

	/** @var  Event[] */
	protected $children = [];

	/**
	 * @param $name
	 * @return Event|null
	 */
	public function getChild($name){
		return Massive::getNamed($this->children,$name,'strcmp');
	}

	public function __toString(){
		return $this->name;
	}

	/**
	 * @param Event $parent
	 */
	public function setParent(Event $parent){
		$old = $this->parent;
		if($old !== $parent){

		}
	}

	/**
	 * Получить имя объекта
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * Выставить имя объекту
	 * @param $name
	 * @return $this
	 */
	public function setName($name){
		$this->name = $name;
		return $this;
	}

	/**
	 * @param $listener
	 */
	public function fire($listener){

	}

}
$Event = [

	'name' => 'load:before',

	'listeners' => [
		function(){

		},
		function(){

		}
	],

];
