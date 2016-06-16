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

	/**
	 * Interface SorterInterface
	 * @package Jungle\Data\Foundation\Collection\Collection
	 */
	interface SorterInterface{

		/**
		 * @return callable
		 */
		public function getCmp();

		/**
		 * @param callable $cmp
		 * @return mixed
		 */
		public function setCmp(callable $cmp);

		/**
		 * @param array $array
		 * @return $this
		 */
		public function sort(array & $array);

	}
}

