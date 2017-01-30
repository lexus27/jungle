<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.11.2016
 * Time: 12:49
 */
namespace Jungle\Data\Record\ValidationValue {
	
	use Jungle\Data\Record;
	use Jungle\Data\Record\Validation\ValidationCollector;

	/**
	 * Class CheckLength
	 * @package Jungle\Data\Record\Validator
	 */
	class CheckLength extends Validator{

		public $type = 'CheckLength';

		/** @var  int */
		public $min;

		/** @var  int */
		public $max;

		/**
		 * CheckLength constructor.
		 * @param $min
		 * @param $max
		 */
		function __construct($min, $max){
			$this->min = $min;
			$this->max = $max;
		}

		/**
		 * @param $field_name
		 * @param $value
		 * @param ValidationCollector $collector
		 */
		function validate($field_name, $value, ValidationCollector $collector){
			$len = mb_strlen($value); // число символов UNICODE
			if(is_string($value) && $len > $this->max || $len < $this->min){
				$collector->error($field_name, $this);
			}
		}
	}
}

