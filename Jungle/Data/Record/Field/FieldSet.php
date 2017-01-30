<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.11.2016
 * Time: 22:34
 */
namespace Jungle\Data\Record\Field {

	/**
	 * Class FieldSet
	 * @package Jungle\Data\Record\Field
	 */
	class FieldSet extends Field{

		protected $field_type = 'set';

		/** @var array  */
		public $expected = [];

		/**
		 * @param $value
		 * @return mixed
		 */
		public function decode($value){
			return $this->expected[$value];
		}

		/**
		 * @param $value
		 * @return mixed
		 */
		public function encode($value){
			return array_search($value, $this->expected, true);
		}


		public function validate($value){
			return in_array($value, $this->expected, true);
		}


	}
}

