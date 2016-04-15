<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 15.11.2015
 * Time: 2:20
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\OneWay {

	/**
	 * Interface IItem
	 * @package Jungle\TestPattern\OneWay
	 */
	interface IItem{

		/**
		 * @param IContainer $collection
		 * @return $this
		 */
		public function setParent(IContainer $collection = null);

		/**
		 * @return IContainer
		 */
		public function getParent();

	}
}

