<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 11.05.2015
 * Time: 0:18
 */

namespace Jungle\XPlate\Interfaces {

	use Jungle\Basic\INamedBase;

	/**
	 * Interface IHtmlTag
	 * @package Jungle\XPlate\Interfaces
	 */
	interface IHtmlTag extends INamedBase{

		/**
		 * @return array
		 */
		public function getAllowedAttributeNames();

		/**
		 * @param string $attributeName
		 * @return $this
		 */
		public function addAllowedAttributeName($attributeName);

		/**
		 * @param string $attributeName
		 * @return bool|int
		 */
		public function searchAllowedAttributeName($attributeName);

		/**
		 * @param string $attributeName
		 * @return $this
		 */
		public function removeAllowedAttributeName($attributeName);

		/**
		 * @param string $attributeName
		 * @return bool
		 */
		public function isAllowedAttributeName($attributeName);


		/**
		 * @return string
		 */
		public function __toString();

	}
}