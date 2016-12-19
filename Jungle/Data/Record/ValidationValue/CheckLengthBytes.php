<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.11.2016
 * Time: 12:57
 */
namespace Jungle\Data\Record\ValidationValue {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Validation\ValidationCollector;

	/**
	 * Class CheckLengthBytes
	 * @package Jungle\Data\Record\Validation
	 */
	class CheckLengthBytes extends CheckLength{

		public $type = 'CheckLengthBytes';

		function validate($field_name, $value, ValidationCollector $collector){
			$len = strlen($value); // Число байт
			if(is_string($value) && $len > $this->max || $len < $this->min){
				$collector->error($field_name, $this);
			}
		}
	}
}

