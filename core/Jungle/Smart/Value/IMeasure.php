<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 16.05.2015
 * Time: 8:00
 */

namespace Jungle\Smart\Value {

	use Jungle\Smart\Value\Measure\IUnit;
	use Jungle\Smart\Value\Measure\IUnitType;

	/**
	 * Значение измеряющее определенную величину в составе единицы измерения (1km/h, 28px)
	 * Interface IMeasure
	 * @package Jungle\Smart\Value\Measure
	 */
	interface IMeasure extends IValue{

		/**
		 * Установить единицу измерения для значения
		 * @param IUnit $unit
		 * @return $this
		 */
		public function setUnit(IUnit $unit);

		/**
		 * Конвертация значения к другой единице измерения
		 * @param IUnit $unit
		 * @return $this
		 */
		public function changeUnit(IUnit $unit);

		/**
		 * Получить единицу измерения
		 * @return IUnit|null
		 */
		public function getUnit();


		/**
		 * @param $value
		 * @param IUnitType $measureUnitType
		 * @param IUnitType $measureUnitTypeSecond
		 * @return mixed
		 */
		public function setValue($value,IUnitType $measureUnitType=null,IUnitType $measureUnitTypeSecond=null);

		/**
		 * @return string
		 */
		public function getValue();

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