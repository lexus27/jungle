<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.06.2016
 * Time: 3:08
 */
namespace Jungle\Util\Data\Foundation {

	/**
	 * Interface ShipmentInterface
	 * @package Jungle\Data\Record\Collection
	 *
	 * Вариант отгрузки из источников
	 *
	 * Проблема решение которой предполагается
	 *      - это невыносимое системой заполнение памяти при копировании больших массивов с результатами
	 *      - Iterator очень долгая штука изза методов, хоть и экономичная по памяти
	 *
	 *
	 */
	interface ShipmentInterface{

		/**
		 * @return int
		 */
		public function count();

		/**
		 * @return mixed|bool
		 */
		public function fetch();

		/**
		 * @return ShipmentInterface
		 */
		public function asIndexed();

		/**
		 * @return ShipmentInterface
		 */
		public function asAssoc();

	}
}

