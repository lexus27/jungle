<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 21.11.2016
 * Time: 15:11
 */
namespace Jungle\Data\Record\Validation {

	use Jungle\Data\Record;

	/**
	 * Class ValidationCollector
	 * @package Jungle\Data\Record\Validation
	 */
	class ValidationCollector extends \Exception{

		/** @var array  */
		protected $field_errors = [];

		/**
		 * ValidationCollector constructor.
		 * @param null $message
		 */
		public function __construct($message = null){
			parent::__construct($message);
		}



		/**
		 * @param $field_name
		 * @param $rule
		 */
		public function error($field_name, ValidationRule $rule){
			$this->field_errors[$field_name][] = $rule;
		}

		/**
		 * @param $field_name
		 * @return null
		 */
		public function getErrors($field_name){
			return isset($this->field_errors[$field_name])?$this->field_errors[$field_name]:null;
		}

		/**
		 * @return bool
		 */
		public function hasErrors(){
			return empty($this->field_errors);
		}

	}
}

