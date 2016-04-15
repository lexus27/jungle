<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 20.11.2015
 * Time: 16:08
 */
namespace Jungle\Basic\Collection\Listing {

	use Jungle\Basic\Collection_OLD\ICollection;

	/**
	 * Interface ICollection
	 * @package Jungle\Basic
	 *
	 * Список
	 *
	 */
	interface IUnique extends ICollection {

		/**
		 * @param $item
		 * @return $this
		 */
		public function add($item);

		/**
		 * @param $item
		 * @return mixed
		 */
		public function search($item);

		/**
		 * @param $item
		 * @return $this
		 */
		public function remove($item);

	}
}

