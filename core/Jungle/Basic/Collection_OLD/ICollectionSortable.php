<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.11.2015
 * Time: 13:25
 */
namespace Jungle\Basic\Collection_OLD {

	/**
	 * Interface ICollectionSortable
	 * @package Jungle\Basic\Collection
	 */
	interface ICollectionSortable{

		/**
		 * Сортировка (setSorter)
		 * @param callable $sorter
		 */
		public function sortBy(callable $sorter);

		public function sort();

	}
}

