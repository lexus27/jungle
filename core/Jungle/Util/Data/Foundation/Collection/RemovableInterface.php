<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 06.06.2016
 * Time: 12:06
 */
namespace Jungle\Util\Data\Foundation\Collection {

	/**
	 * Interface RemovableInterface
	 * @package Jungle\Util\Data\Foundation\Collection
	 */
	interface RemovableInterface{

		/**
		 * @param $item
		 * @return mixed
		 */
		public function remove($item);

	}
}

