<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 15:56
 */
namespace Jungle\Util\Data\Validation\Rule {
	
	use Jungle\Util\Data\Validation\Rule;

	/**
	 * Class Length
	 * @package Jungle\Util\Data\Validation\Rule
	 */
	class Length extends Rule{

		/** @var string  */
		protected $type = 'Length';

		/** @var int|null  */
		protected $minLength = null;

		/** @var int|null  */
		protected $maxLength = null;

		/** @var bool  */
		protected $multiByte = true;

		/**
		 * @param $value
		 * @return bool
		 */
		protected function _expertize($value){
			$len = $this->multiByte?mb_strlen($value):strlen($value);
			if($this->minLength !== null && $len < $this->minLength) return false;
			if($this->maxLength !== null && $len > $this->maxLength) return false;
			return true;
		}
	}
}

