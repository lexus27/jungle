<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 23.02.2016
 * Time: 10:32
 */
namespace Jungle\_DesignPatterns\Observer {

	class Object{


		/** @var Observer[] */
		protected $observers = [];


		public function notify(){
			foreach($this->observers as $observer){
				$observer->update($this);
			}
		}

		/**
		 * @param Observer $observer
		 */
		public function subscribe(Observer $observer){
			$this->observers[] = $observer;
		}

	}
}

