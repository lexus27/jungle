<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:10
 */
namespace Jungle\Data\Foundation\Collection\Ordered {

	/**
	 * Class Collection
	 * @package Jungle\Data\Foundation\Collection
	 */
	abstract class Collection extends \Jungle\Data\Foundation\Collection implements CollectionInterface{


		/**
		 * @param $start
		 * @param $length
		 * @param null $replacement
		 * @return array
		 */
		public function splice($start, $length, $replacement = null){
			return array_splice($this->items, $start, $length, $replacement);
		}

		/**
		 * @param $item
		 * @return $this
		 */
		public function append($item){
			$this->items[] = $item;
			return $this;
		}

		/**
		 * @param $item
		 * @return $this
		 */
		public function prepend($item){
			array_unshift($this->items, $item);
			return $this;
		}

		/**
		 * @param $offset
		 * @param $item
		 * @return $this
		 */
		public function insert($offset, $item){
			$this->splice($offset, 0, [ $item ]);
			return $this;
		}

		/**
		 * @param $offset
		 * @param $item
		 * @return $this
		 */
		public function replace($offset, $item){
			$this->items[$offset] = $item;
			return $this;
		}

		/**
		 * @param $offset
		 * @return mixed
		 */
		public function getByOffset($offset){
			return $this->items[$offset];
		}

		/**
		 * @param $offset
		 * @return mixed
		 */
		public function whip($offset){
			$items = $this->splice($offset, 1);
			return $items[0];
		}

		/**
		 * @return mixed
		 */
		public function shift(){
			return array_shift($this->items);
		}

		/**
		 * @return mixed
		 */
		public function pop(){
			return array_pop($this->items);
		}


	}
}

