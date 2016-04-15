<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.04.2016
 * Time: 13:46
 */
namespace Jungle\Util\Data {

	/**
	 * Interface OrderedCollectionInterface
	 * @package Jungle\Data
	 */
	interface OrderedCollectionInterface{

		/**
		 * @param $offset
		 * @param null $count
		 * @param null $replacement
		 * @return mixed
		 */
		public function splice($offset, $count = null, $replacement = null);


		/**
		 * @param $position
		 * @param $item
		 * @return mixed
		 */
		public function insert($position, $item);

		/**
		 * @param $position
		 * @param $item
		 * @return mixed
		 */
		public function replace($position, $item);

		/**
		 * @param $position
		 * @return mixed
		 */
		public function whip($position);

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

	}
}

