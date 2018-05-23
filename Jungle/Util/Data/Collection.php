<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 21:00
 */
namespace Jungle\Util\Data {
	
	use Jungle\Util\Data\Collection\CollectionInterface;
	use Jungle\Util\Data\Collection\Ordered;
	
	/**
	 * Class Collection
	 * @package Jungle\Util\Data\Collection
	 */
	abstract class Collection implements
		\Countable,
		\Iterator,
		CollectionInterface,
		Ordered\CollectionReadInterface,
		\JsonSerializable{

		/** @var array */
		protected $items = [];

		/**
		 * @param array $items
		 * @return $this
		 */
		public function setItems(array $items){
			$this->items = $items;
			return $this;
		}

		/**
		 * @return array
		 */
		public function getItems(){
			return $this->items;
		}

		/**
		 * @param $start
		 * @param $length
		 * @return mixed
		 */
		public function slice($start, $length){
			return array_slice($this->items, $start, $length, false);
		}

		/**
		 * @return mixed
		 */
		public function first(){
			list($k, $v) = each($a = array_slice($this->items, 0, 1, false));
			return $v;
		}

		/**
		 * @return mixed
		 */
		public function last(){
			list($k, $v) = each($a = array_slice($this->items, -1, 1, false));
			return $v;
		}

		protected $auto_sync = false;

		/**
		 * @return bool
		 */
		public function isAutoSync(){
			return $this->auto_sync;
		}

		/**
		 * @param bool|false $autoSync
		 * @return $this
		 */
		public function setAutoSync($autoSync = false){
			$this->auto_sync = boolval($autoSync);
			return $this;
		}


		/**
		 * @return mixed
		 */
		public function current(){
			return current($this->items);
		}

		/**
		 * @return mixed
		 */
		public function next(){
			next($this->items);
		}

		/**
		 * @return mixed
		 */
		public function key(){
			return key($this->items);
		}

		/**
		 * @return mixed
		 */
		public function valid(){
			return isset($this->items[key($this->items)]);
		}

		/**
		 * @return mixed
		 */
		public function rewind(){
			reset($this->items);
		}

		public function removeCurrent(){
			array_splice($this->items,key($this->items),1);
		}

		/**
		 * @return mixed
		 */
		public function count(){
			return count($this->items);
		}


		/**
		 * @return mixed
		 */
		function jsonSerialize(){
			return $this->getItems();
		}


	}
}

