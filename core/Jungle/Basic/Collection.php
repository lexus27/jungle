<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.11.2015
 * Time: 12:57
 */
namespace Jungle\Basic {

	/**
	 * Class Collection
	 * @package Jungle\Basic
	 */
	class Collection{

		/** @var array  */
		protected $items = [];

		/**
		 * @param array $collection
		 */
		public function __construct(array $collection = null){
			if($collection!==null){
				if(is_array($collection)){
					$this->items = $collection;
				}
			}
		}

		/**
		 * @return $this
		 */
		public function clear(){
			$this->items = [];
			return $this;
		}

		/**
		 * @return array
		 */
		public function toArray(){
			return $this->items;
		}


		/**
		 * @param callable $collector
		 * @param bool|true $asArray
		 * @param bool|false $autoFilter
		 * @return array|Collection
		 */
		public function collectBy(callable $collector, $asArray = true, $autoFilter = false){
			$collected = [];
			foreach($this->items as $key => $item){
				if(call_user_func($collector,$key,$item)){
					$collected[$key] = $item;
					if($autoFilter){// auto filter item
						$this->unsetItem($key);
					}
				}
			}
			return !$asArray?$collected:new Collection($collected);
		}


		/**
		 * @param callable $filter
		 * @param bool|true $asArray
		 * @return array|Collection
		 */
		public function filterBy(callable $filter, $asArray = true){
			$collected = [];
			foreach($this->items as $key => & $item){
				if(call_user_func($filter,$key,$item)){
					$collected[$key] = $item;
					$this->unsetItem($key); // filter item
				}
			}
			return !$asArray?$collected:new Collection($collected);
		}


		/**
		 * @param callable $mapper
		 * @return $this
		 */
		public function each(callable $mapper){
			foreach($this->items as $key => & $item){
				call_user_func($mapper,$key,$item,$this->items);
			}
			return $this;
		}





		/**
		 * Набор полей, уникален только ключ
		 * @see hasItem
		 * @see getItem
		 * @see setItem
		 * @see unsetItem
		 */

		/**
		 * @param $key
		 * @return bool
		 */
		public function hasItem($key){
			return isset($this->items[$key]);
		}

		/**
		 * @ItemAccess
		 *
		 * @param $key
		 * @return mixed|null
		 */
		public function getItem($key){
			return isset($this->items[$key])?$this->items[$key]:null;
		}

		/**
		 * @param $key
		 * @param $value
		 * @return $this
		 */
		public function setItem($key,$value){
			$this->items[$key] = $value;
			return $this;
		}

		/**
		 * @param $key
		 * @return $this
		 */
		public function unsetItem($key){
			unset($this->items[$key]);
			return $this;
		}


		/**
		 * Unique, Value identification
		 *
		 * Пополняющийся набор, полюбому уникальные значения
		 *
		 * @see add (value) добавит если такого элемента не существует
		 * @see search (value) вернет индекс элемента, поэтому элемент в коллекции должен быть единственным уникальным
		 * @see remove (value) находит по searchItem при возвращаемом индексе
		 *
		 */


		/**
		 * @param $item
		 * @return $this
		 */
		public function add($item){
			if($this->search($item)===false){
				$this->items[] = $item;
			}
			return $this;
		}

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
				array_splice($this->items,$i,1);
			}
			return $this;
		}

		/**
		 * @ItemAccess
		 *
		 * @param string $INamedKey
		 * @return null
		 *
		 * Здесь ключ можно сравнивать как в регистронезависимом  так и в регистрозависимом режиме
		 *
		 * Так-же благодаря этому возможен контроль уникальности дополнительно по имени объекта
		 * немного модифицировав метод add
		 * @see add
		 */
		public function get($INamedKey){
			foreach($this->items as $item){
				if($item instanceof INamedRead && strcmp($item->getName(),$INamedKey)===0){
					return $item;
				}
			}
			return null;
		}


		/**
		 * Ordered, Duplicates, Index identification
		 * Пополняемый упорядоченый набор элементов
		 * может иметь контроль уникальности итемов
		 *
		 * @see append(value) - добавит перед всеми элементами
		 * @see prepend(value) - добавит после всех элементов
		 * @see insert(offset,value,addOnly) - вставит между какими-то элементами, если offset 0 то перед 0 (все индексы потом сместятся)
		 * @see replace(fromValue,value)
		 */


		/**
		 * @param $item
		 * @return $this
		 */
		public function append($item){
			array_splice($this->items,count($this->items),0,[$item]);
			return $this;
		}

		/**
		 * @param $item
		 * @return $this
		 */
		public function prepend($item){
			array_splice($this->items,0,0,[$item]);
			return $this;
		}

		/**
		 * @param $offset
		 * @param $item
		 * @param bool $add
		 * @return $this
		 */
		public function insert($offset,$item,$add = true){
			array_splice($this->items,$offset,($add?0:1),[$item]);
			return $this;
		}

		/**
		 * @UNIQUE
		 * Только если имеется контроль уникальности итемов
		 * @param mixed $replacement Элемент который нужно поставить на место $itemPlace
		 * @param mixed $itemPlace
		 * @param bool $swap Означает что если $replacement выставлен на место $itemPlace
		 * то удаленный в тот момент $itemPlace выставится
		 * @return $this
		 */
		public function replace($replacement,$itemPlace, $swap = true){
			$e = array_search($itemPlace,$this->items,true);
			if($e!==false){

				// unique control
				$i = array_search($replacement,$this->items,true);
				if($i!==false){
					array_splice($this->items,$i,1, $swap?[$itemPlace]:null);
				}
				// control end

				array_splice($this->items,$e,1,[$replacement]);
			}

			return $this;
		}



	}
}

