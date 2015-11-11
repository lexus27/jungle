<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 12.05.2015
 * Time: 13:50
 */

namespace Jungle\Smart\Value\Measure {

	use Jungle\Basic\INamedBase;

	/**
	 * Единица измерения
	 * Interface IMeasureUnit
	 * @package Jungle\Smart\Value\Measure
	 */
	interface IUnit extends INamedBase{

		/**
		 * @param IUnit $unit
		 * @param double|int $number
		 * @return double|int
		 */
		public function convertTo(IUnit $unit,$number);

		/**
		 * Выставить коеффициент по отношению к ведущей единице измерения
		 * @param int $multiplier
		 * @return $this
		 */
		public function setCoefficient($multiplier = 1);

		/**
		 * Получить коеффициент ведущей единице измерения
		 * @return mixed
		 */
		public function getCoefficient();

		/**
		 * @param IUnit $unit
		 * @return bool
		 */
		public function equalType(IUnit $unit);

		/**
		 * Выставляет тип величины измерения
		 * @param IUnitType $type
		 * @param bool $addUnit
		 * @param bool $rmOld
		 * @return $this
		 */
		public function setType(IUnitType $type = null,$addUnit = true,$rmOld = true);

		/**
		 * Получить тип величины измерения
		 * @return IUnitType
		 */
		public function getType();

		/**
		 * Аналог INamedBase.getName() для строчного использования объекта
		 * @return string
		 */
		public function __toString();

	}
}