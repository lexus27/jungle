<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 11.05.2015
 * Time: 0:18
 */

namespace Jungle\XPlate\Interfaces {

	use Jungle\Util\INamed;

	/**
	 * Interface IHtmlAttribute
	 * @package Jungle\XPlate\Interfaces
	 */
	interface IHtmlAttribute extends INamed{

		/**
		 * @return bool
		 */
		public function isDOMEventListener();

		/**
		 * @return string
		 */
		public function __toString();

	}
}