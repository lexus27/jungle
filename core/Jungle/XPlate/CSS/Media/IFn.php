<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 11.05.2015
 * Time: 15:14
 */

namespace Jungle\XPlate\CSS\Media {

	use Jungle\Util\INamed;
	use Jungle\Util\Smart\Value\Measure\IUnit;

	/**
	 * Interface ICssMediaFn
	 * @package Jungle\XPlate\Interfaces
	 *
	 * CSS Медиа-функция @media all and mediaFn and mediaFn
	 *
	 */
	interface IFn extends INamed{

		/**
		 * @param $string
		 * @return bool
		 */
		public function isStringAllowed($string);

		/**
		 * @param bool $allowed
		 * @return $this
		 */
		public function setStringAllowed($allowed = true);

		/**
		 * @return bool
		 */
		public function isBooleanAllowed();

		/**
		 * @param bool $allowed
		 * @return $this
		 */
		public function setBooleanAllowed($allowed = true);

		/**
		 * @return bool
		 */
		public function isMeasureAllowed();

		/**
		 * @param bool $allowed
		 * @return $this
		 */
		public function setMeasureAllowed($allowed = true);

		/**
		 * @return bool
		 */
		public function isRangeAllowed();

		/**
		 * @param bool $allowed
		 * @return $this
		 */
		public function setRangeAllowed($allowed = true);

		/**
		 * @param IUnit $unit
		 * @return bool
		 */
		public function isMeasureUnitAllowed(IUnit $unit);

		/**
		 * @param IUnit $unit
		 * @return $this
		 */
		public function addAllowedMeasureUnit(IUnit $unit);

		/**
		 * @param IUnit $unit
		 * @return bool|mixed
		 */
		public function searchAllowedMeasureUnit(IUnit $unit);

		/**
		 * @param IUnit $unit
		 * @return $this
		 */
		public function removeAllowedMeasureUnit(IUnit $unit);


		/**
		 * @param $value
		 * @return bool
		 */
		public function isPassedValueAllowed($value);

		/**
		 * @return string
		 */
		public function __toString();

	}
}