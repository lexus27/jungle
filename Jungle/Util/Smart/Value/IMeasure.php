<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 16.05.2015
 * Time: 8:00
 */

namespace Jungle\Util\Smart\Value {

	use Jungle\Util\Smart\Value\Measure\IUnit;
	use Jungle\Util\Smart\Value\Measure\IUnitType;

	/**
	 * Значение измеряющее определенную величину в составе единицы измерения (1km/h, 28px)
	 * Interface IMeasure
	 * @package Jungle\Util\Smart\Value\Measure
	 */
	interface IMeasure extends IValue{

		/**
		 * Установить единицу измерения для значения
		 * @param IUnit $unit
		 * @return $this
		 */
		public function setPrimaryUnit(IUnit $unit);

		/**
		 * Конвертация значения к другой единице измерения
		 * @param IUnit|string $to
		 * @return $this
		 */
		public function primary($to);

		/**
		 * Получить единицу измерения
		 * @return IUnit|null
		 */
		public function getPrimaryUnit();


		/**
		 * Установить единицу измерения для значения
		 * @param IUnit $unit
		 * @return $this
		 */
		public function setSecondaryUnit(IUnit $unit);

		/**
		 * Конвертация значения к другой единице измерения
		 * @param IUnit|string $to
		 * @return $this
		 */
		public function secondary($to);

		/**
		 * Получить единицу измерения
		 * @return IUnit|null
		 */
		public function getSecondaryUnit();

		/**
		 * @param $value
		 * @param IUnitType $primaryUnitType
		 * @param IUnitType $secondaryUnitType
		 * @return mixed
		 */
		public function setValue($value,IUnitType $primaryUnitType=null,IUnitType $secondaryUnitType=null);

		/**
		 * @return string
		 */
		public function getValue();

		/**
		 * @param int $round
		 * @return mixed
		 */
		public function setRoundPrecision($round = null);

		/**
		 * @param int $round
		 * @return mixed
		 */
		public function setRoundMode($round = PHP_ROUND_HALF_UP);

		/**
		 * @return int
		 */
		public function getNumber();

		/**
		 * @return string
		 */
		public function __toString();


	}
}