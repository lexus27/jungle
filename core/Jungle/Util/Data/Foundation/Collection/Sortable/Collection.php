<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:06
 */
namespace Jungle\Util\Data\Foundation\Collection\Sortable {

	/**
	 * Class Collection
	 * @package Jungle\Util\Data\Foundation\Collection
	 */
	abstract class Collection extends \Jungle\Util\Data\Foundation\Collection implements
		CollectionInterface,
		SorterAwareInterface{


		/** @var  SorterInterface */
		protected $sorter;

		/**
		 * @return mixed
		 */
		public function sort(){
			if($this->sorter){
				if(!$this->sorter->sort($this->items)){

				}
			}
			return $this;
		}

		/**
		 * @param array|SorterInterface|null $sorter
		 * @return mixed
		 */
		public function setSorter($sorter = null){
			$this->sorter = $sorter;
			return $this;
		}

		/**
		 * @return SorterInterface
		 */
		public function getSorter(){
			return $this->sorter;
		}

	}
}

