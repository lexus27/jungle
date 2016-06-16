<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.05.2016
 * Time: 23:24
 */
namespace Jungle\Data\Foundation\Collection\Sortable {

	use Jungle\Data\Foundation\Cmp;

	/**
	 * Class Sorter
	 * @package Jungle\Data\Foundation\Collection\Collection
	 */
	class Sorter implements SorterInterface{

		/** @var  callable|null */
		protected $cmp;

		/**
		 * @return callable|\Closure|null
		 */
		public function getCmp(){
			if(!$this->cmp){
				$this->cmp = Cmp::getDefaultCmp();
			}
			return $this->cmp;
		}

		/**
		 * @param callable|null $cmp
		 * @return $this
		 */
		public function setCmp(callable $cmp = null){
			$this->cmp = Cmp::checkoutCmp($cmp);
			return $this;
		}

		/**
		 * @param $array
		 * @return bool
		 */
		public function sort(array & $array){
			return usort($array, $this->cmp);
		}
	}
}

