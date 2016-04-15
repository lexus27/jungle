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
		 * @param \Jungle\_DesignPatterns\Observer\Object $object
		 */
		public function update(\Jungle\_DesignPatterns\Observer\Object $object){

		}

		public function subscribe(\Jungle\_DesignPatterns\Observer\Object $object){

			$object->subscribe($this);

		}

	}
}

