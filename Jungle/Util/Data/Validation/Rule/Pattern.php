<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 15:34
 */
namespace Jungle\Util\Data\Validation\Rule {

	use Jungle\Util\Data\Validation\Rule;

	/**
	 * Class Pattern
	 * @package Jungle\Util\Data\Validation\Rule
	 */
	class Pattern extends Rule{

		/** @var string  */
		protected $type = 'Pattern';

		/** @var  string|null */
		protected $pattern;

		/** @var  int|null */
		protected $flags;

		/**
		 * @param $value
		 * @return bool
		 */
		protected function _expertize($value){
			return $this->pattern?boolval(fnmatch($this->pattern,$value,$this->flags)):true;
		}
	}
}

