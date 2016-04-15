<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.04.2016
 * Time: 13:47
 */
namespace Jungle\Util\Data {

	/**
	 * Interface SortableCollectionInterface
	 * @package Jungle\Data
	 */
	interface SortableCollectionInterface{

		/**
		 * @param $sorter
		 * @return mixed
		 */
		public function setSorter($sorter);

		/**
		 * @return mixed
		 */
		public function getSorter();



	}
}

