<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 15.11.2015
 * Time: 2:09
 */
namespace Jungle\_DesignPatterns\ParadigmLabs {

	use Jungle\_DesignPatterns\ParadigmLabs\OneWay\IContainer;

	/**
	 * Interface IRelatedItem
	 * @package Jungle\_DesignPatterns\ParadigmLabs\ItemCollection
	 */
	interface IParentChild{

		/**
		 * @param mixed $parent
		 * @return $this
		 */
		public function setParent($parent);

		/**
		 * @return IContainer
		 */
		public function getParent();


	}
}

