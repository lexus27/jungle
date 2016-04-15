<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 15.11.2015
 * Time: 2:12
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\TwoWayCollection {

	/**
	 * Interface IItem
	 * @package Jungle\TestPattern\TwoWay
	 */
	interface IItem{

		/**
		 * @param IContainer|null $container
		 *
		 * --- TWO WAY RELATION SUPPORT --------
		 *      @param bool $appliedInNew
		 *      @param bool $appliedInOld
		 * -------------------------------------
		 *
		 * @return $this
		 */
		public function setParent(IContainer $container = null,$appliedInNew = false,$appliedInOld = false);

		/**
		 * @return IContainer
		 */
		public function getParent();

	}
}

