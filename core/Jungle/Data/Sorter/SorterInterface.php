<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.03.2016
 * Time: 1:56
 */
namespace Jungle\Data\Sorter {

	/**
	 * Interface SorterInterface
	 * @package Jungle\Data
	 */
	interface SorterInterface{

		public function getCmp();

		public function setCmp(callable $cmp);

		public function sort(& $array);

	}
}

