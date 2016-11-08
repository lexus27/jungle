<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 29.10.2016
 * Time: 18:08
 */
namespace Jungle\EventManager {

	/**
	 * Class Plugin
	 * @package Jungle\Application
	 */
	class Plugin{

		/** @var array  */
		protected $preferred_events = [];

		/** @var  float */
		protected $priority;

		/**
		 * Plugin constructor.
		 * @param $priority
		 */
		public function __construct($priority = 0){
			$this->priority = $priority;
		}

		/**
		 * @param $event
		 * @return bool
		 */
		public function matchPreferredEvent($event){
			foreach($this->preferred_events as $event_pattern){
				if(fnmatch($event_pattern,$event, FNM_CASEFOLD)){
					return true;
				}
			}
			return false;
		}

		/**
		 * @param $event
		 * @return $this
		 */
		public function preferEvent($event){
			$this->preferred_events[] = $event;
			return $this;
		}

		/**
		 * @param array $events
		 * @param bool|true $merge
		 * @return $this
		 */
		public function preferEvents(array $events, $merge = true){
			$this->preferred_events = array_unique($merge?array_merge($this->preferred_events,$events):$events);
			return $this;
		}

		/**
		 * @return float
		 */
		public function getPriority(){
			return $this->priority;
		}

	}
}

