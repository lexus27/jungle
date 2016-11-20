<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.11.2016
 * Time: 22:30
 */
namespace Jungle\Data\Record\Field {

	/**
	 * Class Enum
	 * @package Jungle\Data\Record\Field
	 */
	class Enum extends Field{

		protected $field_type = 'enum';

		public function decode($value){
			return explode(',',$value);
		}

		public function encode($value){
			if(is_array($value)){
				return implode(',', $value);
			}
			return $value;
		}


		public function validate($value){
			return true;
		}

	}
}

