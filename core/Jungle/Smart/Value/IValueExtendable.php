<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 17.05.2015
 * Time: 0:28
 */

namespace Jungle\Smart\Value {

	/**
	 * Interface IValueSmart
	 * @package Jungle\Smart\Value
	 */
	interface IValueExtendable{

		/**
		 * @param callable $extender
		 * @return IValueDescendant
		 */
		public function extend(callable $extender = null);

		/**
		 * @param callable $configurator
		 * @return $this
		 */
		public function apply(callable $configurator = null);

		/**
		 * @param callable $extender
		 * @return mixed
		 */
		public function setExtender(callable $extender = null);


	}
}