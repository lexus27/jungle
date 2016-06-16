<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 20:58
 */
namespace Jungle\Data\Foundation\Collection\Sortable {

	/**
	 * Interface SorterAwareInterface
	 * @package Jungle\Data\Foundation\Collection
	 */
	interface SorterAwareInterface{

		/**
		 * @param $sorter
		 * @return mixed
		 */
		public function setSorter(SorterInterface $sorter = null);

		/**
		 * @return mixed
		 */
		public function getSorter();

	}
}

