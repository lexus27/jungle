<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 28.10.2015
 * Time: 0:41
 */
namespace Jungle\TypeHint\Type {

	use Jungle\TypeHint\Type;

	class ObjectDerivative extends Type{

		/**
		 * @param $value
		 * @param $passedTypeString
		 * @return bool
		 */
		public function check($value, $passedTypeString){
			return is_a($value,$passedTypeString);
		}

	}
}

