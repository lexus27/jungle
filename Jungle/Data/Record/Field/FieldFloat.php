<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.11.2016
 * Time: 22:28
 */
namespace Jungle\Data\Record\Field {

	/**
	 * Class FieldFloat
	 * @package Jungle\Data\Record\Field
	 */
	class FieldFloat extends Field{

		protected $field_type = 'float';

		public $precision = null;

		public $precision_round = null;

		public function stabilize($value){
			// для Нумерик полей допустим мягкий режим, если значение пустое то спокойно
			// можно его привести к NULL если поле соответствует
			if($this->nullable && empty($value)){
				return null;
			}
			if(is_numeric($value)){
				$value = floatval($value);
			}
			if(isset($this->precision) && $this->precision_round && is_float($value)){
				$value = round($value, $this->precision, $this->precision_round);
			}
			return $value;
		}

		public function decode($value){
			return floatval($value);
		}

		public function validate($value){
			if(isset($this->precision) && $this->numberOfDecimals($value) > $this->precision){
				return false;
			}
			return is_numeric($value);
		}

		function numberOfDecimals($value){
			if(!is_numeric($value) || (int)$value == $value){
				return 0;
			}
			return strlen($value) - strrpos($value, '.') - 1;
		}

	}
}

