<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 20:47
 */
namespace Jungle\Data\Foundation\Collection {

	/**
	 * Interface CollectionInterface
	 * @package Jungle\Data\Foundation\Collection
	 */
	interface CollectionInterface{

		public function setItems(array $items);

		public function getItems();

	}
}

