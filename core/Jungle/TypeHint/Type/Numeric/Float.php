<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 28.10.2015
 * Time: 0:40
 */
namespace Jungle\TypeHint\Type\Numeric {
	use Jungle\TypeHint\Type;

	class Float extends Type\Numeric{

		/** @var string  */
		protected $name = 'float';

		/**
		 * @param $value
		 * @return bool
		 */
		public function check($value){
			return is_float($value);
		}

		/**
		 * @param $t
		 * @return bool
		 */
		public function parse($t){
			return in_array(strtolower($t),['double','float'],true);
		}

	}
}

