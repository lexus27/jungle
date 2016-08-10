<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 21:01
 */
namespace Jungle\Util\Data\Foundation\Collection\Enumeration\Unique {

	use Jungle\Util\Data\Foundation\Collection\Enumeration\Collection as EnumerationCollection;


	/**
	 * Class Collection
	 * @package Jungle\Util\Data\Foundation\Collection\Enumeration
	 */
	abstract class Collection extends EnumerationCollection
		implements CollectionInterface{

		/**
		 * @param $item
		 * @param bool|true $checkUnique
		 * @return $this
		 */
		public function add($item, $checkUnique = true){
			if($checkUnique){
				$index = array_search($item, $this->items, true);
				if($index !== false){
					return $this;
				}
			}
			$this->items[] = $item;
			return $this;
		}

		/**
		 * @param $item
		 * @return $this
		 */
		public function remove($item){
			$index = array_search($item, $this->items, true);
			if($index !== false){
				array_splice($this->items, $index, 1);
			}
			return $this;
		}


	}
}

