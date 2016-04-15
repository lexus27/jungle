<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 28.10.2015
 * Time: 0:40
 */
namespace Jungle\TypeHint\TypeChecker {

	use Jungle\TypeHint\TypeChecker;

	/**
	 * Class Numeric
	 * @package Jungle\TypeHint\TypeChecker
	 */
	class Numeric extends TypeChecker{

		/** @var string  */
		protected $name = 'numeric';

        /** @var array  */
        protected $aliases = ['num','numeric','number'];


		/**
		 * @param $value
		 * @param array $parameters
		 * @return bool
		 */
		protected function validateByParameters($value, array $parameters = []){
			if(parent::validateByParameters($value,$parameters)===false){
				return false;
			}
			if(!is_numeric($value)){
				return false;
			}
			$p = $this->normalizeParameters($parameters,[
				'min'       => null,
				'max'       => null
			]);

			if(is_numeric($p['min']) && $value < $p['min']){
				$this->last_internal_error_message = 'String is less than "'.$p['min'].'"';
				return false;
			}

			if(is_numeric($p['max']) && $value > $p['max']){
				$this->last_internal_error_message = 'String is long than "'.$p['max'].'"';
				return false;
			}

			return true;

		}

		/**
		 * @param $number
		 * @param null $min
		 * @param null $max
		 * @return int|null|string
		 */
		public static function numConstrain($number, $min = null, $max = null){
			if(is_numeric($max) && $number < $min){
				$number = $min;
			}
			if(is_numeric($max) && $number > $max){
				$number = $max;
			}
			return $number;
		}

	}

}

