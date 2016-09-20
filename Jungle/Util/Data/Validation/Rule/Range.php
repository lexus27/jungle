<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 15:18
 */
namespace Jungle\Util\Data\Validation\Rule {
	
	use Jungle\Util\Data\Validation\Rule;

	/**
	 * Class Range
	 * @package Jungle\Util\Data\Validation\Rule
	 */
	class Range extends Rule{

		protected $type = 'Range';

		/** @var  int|null */
		protected $min;

		/** @var  int|null */
		protected $max;

		/** @var bool  */
		protected $abs = false;

		/**
		 * @param $value
		 * @return bool
		 */
		protected function _expertize($value){
			if($this->abs){
				$value = abs($value);
			}
			if($this->min !== null && $value < $this->min){
				return false;
			}
			if($this->max !== null && $value > $this->max){
				return false;
			}
			return true;

		}
	}
}

