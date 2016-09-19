<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.09.2016
 * Time: 11:57
 */
namespace Jungle\Util\Data\Foundation\Validation\Rule {
	
	use Jungle\Util\Data\Foundation\Validation\Rule;

	/**
	 * Class Vartype
	 * @package Jungle\Util\Data\Foundation\Validation\Rule
	 */
	class Vartype extends Rule{

		/** @var string  */
		protected $type = 'Vartype';

		/** @var array  */
		protected $vartypes = [];

		/**
		 * @param $value
		 * @return bool
		 */
		protected function _check($value){
			return in_array(gettype($value),$this->vartypes, true);
		}
	}
}

