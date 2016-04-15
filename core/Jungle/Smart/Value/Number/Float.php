<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 11.11.2015
 * Time: 17:49
 */
namespace Jungle\Smart\Value\Number {

	use Jungle\Smart\Value\Number;

	/**
	 * Class Float
	 * @package Jungle\Smart\Value\Number
	 */
	class Float extends Number{


		/**
		 * @var float
		 */
		protected static $default_value = 0.0;

		/**
		 * @param $pass
		 */
		protected function beforeValueSet(& $pass){
			$pass = floatval($pass);
		}
	}
}

