<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.09.2016
 * Time: 11:58
 */
namespace Jungle\Util\Data\Validation\Rule {
	
	use Jungle\Util\Data\Validation\Rule;

	/**
	 * Class Precision
	 * @package Jungle\Util\Data\Validation\Rule
	 */
	class Precision extends Rule{

		protected $type = 'Precision';

		/** @var  null|int */
		protected $precision;

		/** @var  null|int */
		protected $minPrecision;

		/**
		 * @param $value
		 * @return bool
		 */
		protected function _expertize($value){
			$minPrecision = $this->minPrecision===null?null:abs($this->minPrecision);
			$precision = $this->precision===null?null:abs($this->precision);
			$valid = true;
			if($minPrecision === null){
				if($precision > 0){
					$valid = preg_match('[\.,]\d{'.$precision.'}$', $value);
				}
			}else{
				if($minPrecision > 0){
					$valid = preg_match('[\.,]\d{'.$minPrecision.','.$precision.'}$', $value);
				}else{
					if($precision > 0){
						$valid = preg_match('([\.,]\d{0,'.$precision.'})?$', $value);
					}
				}
			}
			return boolval($valid);
		}
	}
}

