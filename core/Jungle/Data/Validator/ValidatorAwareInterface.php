<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 19:12
 */
namespace Jungle\Data\Validator {

	/**
	 * Interface ValidatorAwareInterface
	 * @package Jungle\Data
	 */
	interface ValidatorAwareInterface{

		/**
		 * @param callable|ValidatorInterface|null $validator
		 * @return mixed
		 */
		public function setValidator(callable $validator = null);

		/**
		 * @return callable|ValidatorInterface|null
		 */
		public function getValidator();

		/**
		 * @param $value
		 * @return bool|array
		 */
		public function validate($value);

	}
}

