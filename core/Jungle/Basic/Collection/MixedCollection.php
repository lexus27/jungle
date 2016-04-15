<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 2:32
 */
namespace Jungle\Basic\Collection {

	use Jungle\Basic\Collection\Listing\IOrdered;

	/**
	 * Class MixedCollection
	 * @package Jungle\Basic\Collection
	 *
	 * Что то вроде MixedCollection в ExtJs
	 */
	class MixedCollection implements ICollection, IOrdered{

		protected $count = 0;
		protected $keys  = [];
		protected $items = [];

		/**
		 * @return $this
		 */
		public function clear(){
			$this->keys  = [];
			$this->items = [];
			$this->count = 0;
			return $this;
		}

		/**
		 * @return array
		 */
		public function toArray(){
			return array_combine($this->keys,$this->items);
		}

		/**
		 * @param callable $collector
		 * @param bool|true $asArray
		 * @param bool $cut
		 * @return array
		 * @internal param bool $autoFilter
		 */
		public function collectBy(callable $collector, $asArray = true, $cut = false){
			$keys = $this->keys;
			$items = $this->items;
			$a = [];
			foreach($keys as $i => $key){
				if(call_user_func($collector,$key,$items[$i])){
					$a[$keys[$i]] = $items[$i];
					if($cut){
						unset($this->keys[$i],$this->items[$i]);
						$this->count--;
					}
				}
			}
			if($cut && $a){
				ksort($this->keys);
				ksort($this->items);
			}
			return $asArray?$a: new MixedCollection($a);
		}

		/**
		 * @param callable $filter
		 * @param bool $asArray
		 * @return array
		 */
		public function filterBy(callable $filter, $asArray = true){
			$keys = $this->keys;
			$items = $this->items;
			$a = [];
			foreach($keys as $i => $key){
				if(call_user_func($filter,$key,$items[$i])){
					$a[$keys[$i]] = $items[$i];
					unset($this->keys[$i],$this->items[$i]);
					$this->count--;
				}
			}

			if($a){
				ksort($this->keys);
				ksort($this->items);
				$this->count = count($this->items);
			}
			return ($asArray?$a: new MixedCollection($a));
		}

		/**
		 * @param callable $mapper
		 * @param bool $executeReturnSignals
		 * @param bool $collectedAsArray
		 * @return $this
		 */
		public function each(callable $mapper,$executeReturnSignals = false,$collectedAsArray = false){
			$keys = $this->keys;
			$items = $this->items;
			$a = [];
			foreach($keys as $i => $key){
				$mapperReturn = call_user_func($mapper,$key,$items[$i]);
				if($executeReturnSignals){
					switch($mapperReturn){
						case true: $a[$keys[$i]] = $items[$i];break;
						case false: unset($this->keys[$i],$this->items[$i]); $this->count--; break;
					}
				}
			}

			if($a){
				ksort($this->keys);
				ksort($this->items);
				$this->count = count($this->items);
			}

			return $collectedAsArray?$a: new MixedCollection($a);
		}


		/**
		 * @param $item
		 * @return $this
		 */
		public function append($item){
			$key = $this->count++;
			$this->keys[] = $key;
			$this->items[] = $item;
			return $this;
		}

		/**
		 * @param $item
		 * @return $this
		 */
		public function prepend($item){
			$key = $this->count++;
			array_splice($this->keys,0,0,[$key]);
			array_splice($this->items,0,0,[$item]);
			return $this;
		}

		/**
		 * @param $offset
		 * @param $item
		 * @param bool $add
		 * @return $this
		 */
		public function insert($offset, $item, $add = true){
			$key = $this->count++;
			if(!$add && isset($this->items[$offset])){
				$this->count--;
			}
			array_splice($this->keys,$offset,$add?0:1,[$key]);
			array_splice($this->items,$offset,$add?0:1,[$item]);
			return $this;
		}

		/**
		 * Получить первый элемент
		 * @return mixed
		 */
		public function first(){
			return $this->items[0];
		}

		/**
		 * Получить последний элемент
		 * @return mixed
		 */
		public function last(){
			return $this->items[$this->count-1];
		}

		/**
		 * Высекает начало из коллекции
		 * @param bool $returnPair
		 * @return mixed item
		 */
		public function shift($returnPair = false){
			if(!$this->count) return null;
			array_splice($this->keys,0,1);
			$item = array_splice($this->items,0,1);
			if($item){
				$this->count--;
				return $item[0];
			}
			return null;
		}

		/**
		 * Высекает конец из коллекции
		 * @param bool $returnPair
		 * @return array|mixed item
		 */
		public function pop($returnPair = false){
			if(!$this->count) return null;
			$keys = array_splice($this->keys,$this->count-1,1);
			$items = array_splice($this->items,$this->count-1,1);
			if($items){
				$this->count--;
				if($returnPair){
					return array_combine($keys,$items);
				}else{
					return $items[0];
				}
			}
			if($returnPair){return [0,null];}else{return null;}
		}

		/**
		 * @param $offset
		 * @param $count
		 * @param array|null $replacement
		 * @return mixed
		 */
		public function splice($offset, $count, array $replacement = null){
			$keys = array_splice($this->keys,$offset,1,array_keys($replacement));
			$items = array_splice($this->items,$offset,1,array_values($replacement));
			$this->count = count($this->keys);
			return array_combine($keys,$items);
		}
	}
}

