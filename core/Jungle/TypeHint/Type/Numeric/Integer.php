<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 28.10.2015
 * Time: 0:40
 */
namespace Jungle\TypeHint\Type\Numeric {

	use Jungle\TypeHint\Type;

	class Integer extends Type\Numeric{

		/** @var string  */
		protected $name = 'integer';

		/**
		 * @param $value
		 * @return bool
		 */
		public function check($value){
			return is_integer($value);
		}

		/**
		 * @param $t
		 * @return bool
		 */
		public function parse($t){
			return in_array(strtolower($t),['int','integer'],true);
		}

	}
}

