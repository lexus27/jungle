<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 19:01
 */
namespace Jungle\Data\Validator {

	use Jungle\Data\Validator;

	/**
	 * Class ValidatorAggregate
	 * @package Jungle\Data
	 */
	class ValidatorAggregate implements ValidatorInterface{

		/**
		 * @var Validator[]|callable[]
		 */
		protected $validators = [];

		/**
		 * @param array $validators
		 */
		public function __construct(array $validators){
			$this->validators = $validators;
		}

		/**
		 * @param $value
		 * @param null $property
		 * @return array|bool|string
		 */
		public function __invoke($value,$property=null){
			return $this->check($value,$property);
		}

		/**
		 * @param $value
		 * @param null $property
		 * @return bool|array
		 */
		public function check($value, $property = null){
			$errors = [];
			foreach($this->validators as $validator){
				$result = call_user_func($validator,$value,$property);
				if($result!==true){
					$errors = array_merge($errors,$this->decompositeErrors($result));
				}
			}
			if($errors){
				return $errors;
			}else{
				return true;
			}
		}


		/**
		 * @param $errors
		 * @return array|bool
		 */
		public static function decompositeErrors($errors){
			if(is_array($errors) && isset($errors['message']) || is_string($errors)){
				return [$errors];
			}
			return $errors;
		}

	}
}

