<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 12.05.2015
 * Time: 11:31
 */

namespace Jungle\Util\Smart\Value {


	/**
	 * Interface IValueSettable
	 * @package Jungle\Util\Smart\Value
	 */
	interface IValueSettable{

		/**
		 * @param $value
		 * @return $this
		 */
		public function setValue($value);

	}
}