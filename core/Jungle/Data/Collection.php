<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 17:27
 */
namespace Jungle\Data {

	use Jungle\Data\Sorter\SorterInterface;

	/**
	 * Class Collection
	 * @package Jungle\Util
	 */
	class Collection implements \Countable, \SeekableIterator{

		/** @var int  */
		protected $i        = 0;

		/** @var  array  */
		protected $items    = [];

		/** @var  SorterInterface */
		protected $sorter;

		/** @var  array  */
		protected $insert_hooks = [];

		/** @var  array  */
		protected $remove_hooks = [];

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
		 * @param $item
		 * @return $this
		 */
		public function add($item){
			if($this->beforeAdd($item)!==false){
				$this->items[] = $item;
				$this->onAdd($item);
			}
			return $this;
		}

		/**
		 * @param $item
		 * @return bool
		 */
		protected function beforeAdd($item){
			foreach($this->insert_hooks as $hook){
				if(call_user_func($hook, $item, $this)===false){
					return false;
				}
			}
			return true;
		}

		/**
		 * @param $item
		 */
		protected function onAdd($item){}

		/**
		 * @param $item
		 * @return mixed
		 */
		public function search($item){
			return array_search($item,$this->items,true);
		}

		/**
		 * @param $item
		 * @return $this
		 */
		public function remove($item){
			if(($i = $this->search($item))!==false){
				if($this->beforeRemove($item)!==false){
					array_splice($this->items,$i,1);
					$this->onRemove($item);
				}
			}
			return $this;
		}

		protected function beforeRemove($item){
			foreach($this->insert_hooks as $hook){
				if(call_user_func($hook, $item, $this)===false){
					return false;
				}
			}
			return true;
		}

		protected function onRemove($item){}

		/**
		 * Sort item collection
		 */
		public function sort(){
			if($this->sorter){
				$this->sorter->sort($this->items);
			}
		}

		public function current(){
			return $this->items[$this->i];
		}

		public function next(){
			$this->i++;
		}

		public function key(){
			return $this->i;
		}

		public function valid(){
			return isset($this->items[$this->i]);
		}

		public function rewind(){
			$this->i = 0;
		}

		public function count(){
			return count($this->items);
		}

		public function seek($position){
			$this->i = $position;
		}




	}
}

