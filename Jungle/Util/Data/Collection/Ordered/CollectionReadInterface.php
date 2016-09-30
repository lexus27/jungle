<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:04
 */
namespace Jungle\Util\Data\Collection\Ordered {

	/**
	 * Interface CollectionReadInterface
	 * @package Jungle\Util\Data\Collection\Ordered
	 */
	interface CollectionReadInterface{

		/**
		 * @param $start
		 * @param $length
		 * @return array
		 */
		public function slice($start, $length);

		/**
		 * @return mixed
		 */
		public function first();

		/**
		 * @return mixed
		 */
		public function last();

	}
}

