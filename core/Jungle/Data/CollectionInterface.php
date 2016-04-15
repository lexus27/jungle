<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.04.2016
 * Time: 13:34
 */
namespace Jungle\Data {

	/**
	 * Interface CollectionInterface
	 * @package Jungle\Data
	 *
	 *
	 * Unique collection
	 * Duplicate collection (position control)
	 *
	 */
	interface CollectionInterface{

		/**
		 * @param $items
		 * @return mixed
		 */
		public function setItems($items);

		/**
		 * @return mixed
		 */
		public function getItems();


		/**
		 * @param $item
		 * @return mixed
		 */
		public function add($item);

		/**
		 * @param $item
		 * @return mixed
		 */
		public function remove($item);


		/**
		 * @param $offset
		 * @param null $count
		 * @return mixed
		 */
		public function slice($offset, $count = null);

		/**
		 * Выбить из коллекции элемент по позиции
		 * @param $position
		 * @return mixed
		 */
		public function whip($position);

		/**
		 * @return mixed
		 */
		public function shift();

		/**
		 * @return mixed
		 */
		public function pop();


		/**
		 * @return mixed
		 */
		public function first();

		/**
		 * @return mixed
		 */
		public function last();


		/**
		 * @return int
		 */
		public function count();


		/**
		 * @param callable $checker
		 * @return mixed
		 */
		public function collect(callable $checker);

		/**
		 * @param callable $filter
		 * @return $this
		 */
		public function filter(callable $filter);

	}
}

