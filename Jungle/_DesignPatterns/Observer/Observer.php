<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 23.02.2016
 * Time: 10:24
 */
namespace Jungle\_DesignPatterns\Observer {

	/**
	 * Class Observer
	 * @package Jungle\_DesignPatterns\Observer
	 */
	class Observer{

		/**
		 * @param \Jungle\_DesignPatterns\Observer\Object|Object $object
		 */
		public function update(Object $object){

		}

		public function subscribe(Object $object){

			$object->subscribe($this);

		}

	}
}

