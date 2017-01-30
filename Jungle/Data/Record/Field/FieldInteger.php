<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.11.2016
 * Time: 22:27
 */
namespace Jungle\Data\Record\Field {

	/**
	 * Class FieldInteger
	 * @package Jungle\Data\Record\Field
	 */
	class FieldInteger extends Field{

		protected $field_type = 'integer';

		public function stabilize($value){
			// для Нумерик полей допустим мягкий режим, если значение пустое то спокойно
			// можно его привести к NULL если поле соответствует
			if($this->nullable && empty($value)){
				return null;
			}
			if(is_numeric($value)){
				$value = intval($value);
			}
			return $value;
		}

		public function decode($value){
			return intval($value);
		}


		public function validate($value){
			return is_numeric($value);
		}

	}
}

