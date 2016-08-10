<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 24.06.2015
 * Time: 22:52
 */

namespace Jungle\XPlate\CSS\Media {


	use Jungle\Util\Smart\Value\IMeasure;


	/**
	 * Interface IFnDecorator
	 * @package Jungle\XPlate\CSS\Media
	 */
	interface IFnDecorator{

		/**
		 * @param IFn $fn
		 * @return $this
		 */
		public function setFn(IFn $fn);

		/**
		 * @return IFn
		 */
		public function getFn();

		/**
		 * @return $this
		 */
		public function begin();


		/**
		 * @param $v
		 * @return $this
		 */
		public function setValue($v);

		/**
		 * @return bool|int|string|IMeasure
		 */
		public function getValue();


		/**
		 * @return bool
		 * Выставлены ли единицы измерения минимума или максимума
		 */
		public function isRange();

		/**
		 * @return $this
		 * Сбросить min и max единицы измерения
		 */
		public function resetRange();


		/**
		 * @param IMeasure $value
		 * @return $this
		 */
		public function setMin(IMeasure $value);

		/**
		 * @param IMeasure $value
		 * @return $this
		 */
		public function setMax(IMeasure $value);

		/**
		 * @param IMeasure $min
		 * @param IMeasure $max
		 * @return $this
		 * Использование setMin и setMax за один метод
		 */
		public function setMinMax(IMeasure $min, IMeasure $max);

		/**
		 * @return IMeasure|null
		 */
		public function getMin();

		/**
		 * @return IMeasure|null
		 */
		public function getMax();


		/**
		 * @return bool|string
		 */
		public function toString();

		/**
		 * @return mixed
		 */
		public function __toString();
	}
}