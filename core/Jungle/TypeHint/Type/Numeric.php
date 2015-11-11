<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 28.10.2015
 * Time: 0:40
 */
namespace Jungle\TypeHint\Type {

	use Jungle\TypeHint\Type;

	class Numeric extends Type{

		/** @var string  */
		protected $name = 'numeric';

		/**
		 * @param $value
		 * @return bool
		 */
		public function check($value){
			return is_numeric($value);
		}

		/**
		 * @param $t
		 * @return bool
		 */
		public function parse($t){
			return in_array(strtolower($t),['num','numeric','number'],true);
		}

	}
}

