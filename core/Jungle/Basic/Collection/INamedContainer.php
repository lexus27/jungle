<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 5:25
 */
namespace Jungle\Basic\Collection {

	use Jungle\Basic\Collection\Listing\IUnique;
	use Jungle\Basic\INamed;


	/**
	 * Interface INamedContainer
	 * @package Jungle\Basic\Collection
	 */
	interface INamedContainer extends ICollection, IUnique{

		/** Lazy method
		 * @param string|INamed $key
		 * @return INamed
		 */
		public function get($key);

		/**
		 * @param INamed $item
		 * @return $this
		 */
		public function add($item);

		/**
		 * @param INamed $item
		 * @return mixed
		 */
		public function search($item);

		/**
		 * @param INamed $item
		 * @return $this
		 */
		public function remove($item);


	}
}

