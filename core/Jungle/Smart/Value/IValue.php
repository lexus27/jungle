<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 12.05.2015
 * Time: 11:30
 */

namespace Jungle\Smart\Value {

	/**
	 * Interface IValue
	 * @package Jungle\Smart\Value
	 */
	interface IValue{

		/**
		 * @return string
		 */
		public function getValue();

		/**
		 * @param IValue|mixed $value
		 * @return bool
		 */
		public function equal($value);


	}
}