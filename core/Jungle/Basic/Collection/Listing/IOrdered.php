<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 2:25
 */
namespace Jungle\Basic\Collection\Listing {

	/**
	 * Interface IListingOrdered
	 * @package Jungle\Basic\Collection
	 */
	interface IOrdered{

		/**
		 * @param $item
		 * @return $this
		 */
		public function append($item);

		/**
		 * @param $item
		 * @return $this
		 */
		public function prepend($item);

		/**
		 * @param $offset
		 * @param $item
		 * @param bool $add
		 * @return $this
		 */
		public function insert($offset,$item,$add = true);

		/**
		 * Получить первый элемент
		 * @return mixed
		 */
		public function first();

		/**
		 * Получить последний элемент
		 * @return mixed
		 */
		public function last();


		/**
		 * Высекает начало из коллекции
		 * @param $returnPair
		 * @return mixed item
		 */
		public function shift($returnPair = false);

		/**
		 * Высекает конец из коллекции
		 * @param $returnPair
		 * @return mixed item
		 */
		public function pop($returnPair = false);


		/**
		 * @param $offset
		 * @param $count
		 * @param array|null $replacement
		 * @return mixed
		 */
		public function splice($offset,$count,array $replacement =  null);


	}
}

