<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.03.2016
 * Time: 16:38
 */
namespace Jungle\Data\Collection {

	use Jungle\Data\Collection;
	use Jungle\Util\Value\Massive;

	/**
	 * Class Buffer
	 * @package Jungle\Data\Collection
	 */
	class Buffer{

		/** @var  Collection */
		protected $collection;

		/** @var int  */
		protected $limit = 10;

		/** @var array  */
		protected $items = [];

		/** @var int[]  */
		protected $usage = [];

		/**
		 * @param Collection $collection
		 * @param $limit
		 */
		public function __construct(Collection $collection, $limit){
			$this->collection   = $collection;
			$this->limit        = $limit;
		}

		/**
		 * @param $key
		 * @return null
		 */
		public function get($key){
			if(isset($this->items[$key])){
				$this->usage[$key]++;
			}else{
				$value = $this->collection->get($key);
				if($value !== null){
					if(count($this->items) > $this->limit){
						$this->_shift();
					}
					$this->items[$key] = $value;
					$this->usage[$key] = 1;
				}else{
					return null;
				}
			}
			return $this->items[$key];
		}

		/**
		 * @return mixed
		 */
		protected function _shift(){
			Massive::keySortColumn($this->items,function($key){
				return $this->usage[$key];
			},true);

			$keys = array_keys($this->items);
			$key = $keys[0];
			unset($this->usage[$key]);
			sort($this->usage);


			return array_shift($this->items);

		}
	}
}

