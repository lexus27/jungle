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
	 * @package Jungle\Data\Record\Validation
	 */
	class CheckLength extends Validation{

		public $type = 'CheckLength';

		public $min;

		public $max;

		function __construct($min, $max){
			$this->min = $min;
			$this->max = $max;
		}
		
		function validate($field_name, $value, ValidationCollector $collector){
			$len = mb_strlen($value); // число символов UNICODE
			if(is_string($value) && $len > $this->max || $len < $this->min){
				$collector->error($field_name, $this);
			}
		}
	}
}

