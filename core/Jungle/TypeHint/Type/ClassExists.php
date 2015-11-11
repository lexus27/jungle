<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 28.10.2015
 * Time: 0:40
 */
namespace Jungle\TypeHint\Type {
	use Jungle\TypeHint\Type;
	class ClassExists extends Type{

		/**
		 * @param $value
		 * @return bool
		 */
		public function check($value){
			return class_exists();
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

