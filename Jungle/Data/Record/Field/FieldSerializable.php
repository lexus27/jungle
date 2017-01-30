<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.11.2016
 * Time: 21:58
 */
namespace Jungle\Data\Record\Field {

	/**
	 * Class FieldSerializable
	 * @package Jungle\Data\Record\Field
	 */
	class FieldSerializable extends Field{

		protected $field_type = 'data';

		public function decode($value){
			return unserialize($value);
		}

		public function encode($value){
			return serialize($value);
		}


		public function validate($value){
			return true;
		}


	}
}

