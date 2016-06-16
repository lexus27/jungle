<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 20:59
 */
namespace Jungle\Data\Foundation\Collection {

	/**
	 * Interface IndexedUniqueCollectionInterface
	 * @package Jungle\Data\Foundation\Collection
	 */
	interface IndexedUniqueCollectionInterface{

		/**
		 * @param $item
		 * @return mixed
		 */
		public function indexOf($item);

	}
}

