<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 14.05.2015
 * Time: 23:08
 */

namespace Jungle\Util\Smart\Value\Measure {

	use Jungle\Util\Named\NamedInterface;

	/**
	 * Тип единицы измерения или же то что измеряет единица измерения в данном типе (Скорость,Растояние,Объем,Деньги, и т.д)
	 * Interface IUnitType
	 * @package Jungle\Util\Smart\Value\Measure
	 */
	interface IUnitType extends NamedInterface{

		/**
		 * @param $name
		 * @return IUnit
		 */
		public function getUnit($name);

		/**
		 * Добавить еденицу измерения к данному типу
		 * @param IUnit $unit
		 * @param bool $setType
		 * @return $this
		 */
		public function addUnit(IUnit $unit,$setType = true);

		/**
		 * Поиск единицы измерения в данном типе
		 * @param IUnit $unit
		 * @return int|bool
		 */
		public function searchUnit(IUnit $unit);

		/**
		 * Удаление единицы измерения из данного типа
		 * @param IUnit $unit
		 * @param bool $setTypeNull
		 * @return $this
		 */
		public function removeUnit(IUnit $unit,$setTypeNull = true);

		/**
		 * @param double $number
		 * @param IUnit $from
		 * @param IUnit $to
		 * @return double
		 */
		public static function convert($number, IUnit $from, IUnit $to);

	}
}