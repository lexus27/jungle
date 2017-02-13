<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.11.2016
 * Time: 22:15
 */
namespace Jungle\Data\Record\Field {

	/**
	 * Class FieldDateTime
	 * @package Jungle\Data\Record\Field
	 */
	class FieldDateTime extends Field{

		protected $field_type = 'date_time';

		/**
		 * @param $value
		 * @return int|null
		 */
		public function decode($value){
			return $this->nullable && $value===null?$value:strtotime($value);
		}

		/**
		 * @param $value
		 * @return bool|null|string
		 */
		public function encode($value){
			return $value!==null?date('Y-m-d H:i:s', $value):null;
		}

		/**
		 * @param $value
		 * @return int|null
		 */
		public function stabilize($value){
			if($this->nullable && empty($value)){
				return null;
			}
			return is_integer($value) ? $value : strtotime($value);
		}


		public function validate($value){
			return true;
		}
		
	}
}

