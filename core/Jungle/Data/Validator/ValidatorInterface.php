<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 19:01
 */
namespace Jungle\Data\Validator {

	/**
	 * Interface ValidatorInterface
	 * @package Jungle\Data
	 */
	interface ValidatorInterface{

		/**
		 * @param $value
		 * @param null $property
		 * @return bool|array
		 */
		public function check($value, $property = null);

		/**
		 * @param $value
		 * @param $property
		 * @return bool|array
		 */
		public function __invoke($value,$property = null);


	}
}

