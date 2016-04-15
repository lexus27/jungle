<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 20.11.2015
 * Time: 13:55
 */
namespace Jungle\Basic\Collection_OLD {

	/**
	 * Interface ICollectionOrdered
	 * @package Jungle\Basic\Collection
	 *
	 * Коллекция с определяемым порядком элементов
	 *
	 */
	interface ICollectionOrdered{

		/**
		 * @param null $offset
		 * @param int $count
		 * @param array|null $replacement
		 * @return mixed
		 */
		public function splice($offset = null, $count = 0, array $replacement = null);

		/**
		 * @param $item
		 * @return mixed
		 */
		public function append($item);

		/**
		 * @param $item
		 * @return mixed
		 */
		public function prepend($item);

		/**
		 * Только если в коллекции контролируется
		 * @param mixed $item
		 * @param mixed $destinationItem item in collection
		 * @return mixed
		 */
		public function replace($item,$destinationItem);

		/**
		 * @param $offset
		 * @param $item
		 * @return mixed
		 */
		public function insert($offset, $item);



		/**
		 * Вычесть элемент с начала
		 * @return mixed
		 */
		public function shift();

		/**
		 * Вычесть элементы с конца
		 * @return mixed
		 */
		public function pop();


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

	}
}

