<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.11.2015
 * Time: 15:01
 */
namespace Jungle\Basic\Collection_OLD {

	/**
	 * Interface ICollectionInternalSortable
	 * @package Jungle\Basic\Collection
	 */
	interface ICollectionSortableInternal extends ICollectionSortable, ICollection{

		/**
		 * @see setInternalSorter
		 * @return void
		 */
		public function applyInternalSorter();

		/**
		 * @see applyInternalSorter
		 * @param callable $sorter
		 * @return mixed
		 */
		public function setInternalSorter(callable $sorter);

	}
}

