<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 15.11.2015
 * Time: 2:12
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\TwoWayCollection {

	/**
	 * Interface IItemParent (Collection container)
	 * @package Jungle\TestPattern\TwoWay
	 */
	interface IContainer{

		/**
		 * @param IItem $item
		 *
		 * --- TWO WAY RELATE ---------
		 * @param bool $appliedInParent
		 * @param bool $appliedInOld
		 * ----------------------------
		 *
		 * @return $this
		 * @impl Collection EQUAL owner (has 1 or 2 way reference)
		 */
		public function addItem(IItem $item,$appliedInParent = false,$appliedInOld = false);

		/**
		 * @param IItem $item
		 * @return mixed
		 *
		 *
		 * @impl Collection EQUAL owner (has 1 or 2 way reference)
		 */
		public function searchItem(IItem $item);

		/**
		 * @param IItem $item
		 *
		 * @param bool $appliedInOld
		 *
		 * @return $this
		 * @impl Collection EQUAL owner (has 1 or 2 way reference)
		 */
		public function removeItem(IItem $item,$appliedInOld = false);


	}
}

