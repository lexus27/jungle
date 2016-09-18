<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 15:33
 */
namespace Jungle\Util\Data\Foundation\Validation\Rule {
	
	use Jungle\Util\Data\Foundation\Validation\Rule;

	/**
	 * Class RegExp
	 * @package Jungle\Util\Data\Foundation\Validation\Rule
	 */
	class RegExp extends Rule{

		/** @var string  */
		protected $type = 'RegExp';

		/** @var  string|null */
		protected $pattern;

		/**
		 * @param $value
		 * @return bool
		 */
		protected function _check($value){
			return $this->pattern?boolval(preg_match($this->pattern,$value)):true;
		}
	}
}

