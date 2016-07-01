<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 20:59
 */
namespace Jungle\Util\Data\Foundation\Collection\Ordered {

	/**
	 * Interface CollectionInterface
	 * @package Jungle\Util\Data\Foundation\Collection\Ordered
	 */
	interface CollectionInterface{

		/**
		 * @param $start
		 * @param $length
		 * @param null $replacement
		 * @return mixed
		 */
		public function splice($start, $length, $replacement = null);

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
		 * @param $offset
		 * @param $item
		 * @return mixed
		 */
		public function insert($offset, $item);

		/**
		 * @param $offset
		 * @param $item
		 * @return mixed
		 */
		public function replace($offset, $item);


		/**
		 * @param $offset
		 * @return mixed
		 */
		public function getByOffset($offset);


		/**
		 * @param $offset
		 * @return mixed
		 */
		public function whip($offset);

		/**
		 * @return mixed
		 */
		public function shift();

		/**
		 * @return mixed
		 */
		public function pop();

	}
}

