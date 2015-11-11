<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 11.05.2015
 * Time: 0:21
 */

namespace Jungle\XPlate\CSS\Definition {

	use Jungle\Basic\INamedBase;

	/**
	 * Interface ICssProperty
	 * @package Jungle\XPlate\Interfaces
	 *
	 * CSS свойство
	 *
	 */
	interface IProperty extends INamedBase{

		/**
		 * @return bool
		 */
		public function isShorthand();

		/**
		 * @param IProperty $property
		 * @param bool $setGeneral
		 * @return $this
		 */
		public function addContain(IProperty $property,$setGeneral = true);

		/**
		 * @param IProperty $property
		 * @return mixed
		 */
		public function searchContain(IProperty $property);

		/**
		 * @param IProperty $property
		 * @param bool $removeGeneral
		 * @return $this
		 */
		public function removeContain(IProperty $property,$removeGeneral = true);

		/**
		 * @param IProperty $property
		 * @param bool $addContain
		 * @param bool $removeOld
		 * @return $this
		 */
		public function setGeneral(IProperty $property = null, $addContain = true,$removeOld = true);

		/**
		 * @return IProperty
		 */
		public function getGeneral();


		/**
		 * @param bool $required
		 * @return $this
		 */
		public function setVendorRequired($required = true);

		/**
		 * @return bool
		 */
		public function isVendorRequired();

		/**
		 * @param $value
		 * @return string
		 */
		public function processEval($value);

		/**
		 * @param string $raw_property_name with prefixes and them
		 * @return string normalized
		 */
		public static function normalizePropertyName($raw_property_name);

	}
}