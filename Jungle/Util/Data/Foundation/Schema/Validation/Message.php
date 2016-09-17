<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.09.2016
 * Time: 12:15
 */
namespace Jungle\Util\Data\Foundation\Schema\Validation {

	/**
	 * Class Message
	 * @package Jungle\Util\Data\Foundation\Schema\Validation
	 */
	class Message implements MessageInterface{

		/** @var   */
		protected $type;

		/** @var  string  */
		protected $field_name;

		/** @var  string */
		protected $message;

		/** @var array  */
		protected $additionFields;

		/** @var array  */
		protected $rules;

		/**
		 * Message constructor.
		 * @param $fieldName
		 * @param $type
		 * @param $message
		 * @param array $additionFields
		 * @param array $rules
		 */
		public function __construct($fieldName, $type,$message, $additionFields = [ ], $rules = []){

			$this->name = $fieldName;
			$this->type = $type;

			$this->message = $message;
			$this->additionFields = $additionFields;
			$this->rules = $rules;
		}

		/**
		 * @return mixed
		 */
		public function getType(){
			return $this->type;
		}

		/**
		 * @return string
		 */
		public function getField(){
			return $this->field_name;
		}

		/**
		 * @return array
		 */
		public function getRules(){
			return $this->rules;
		}

		/**
		 * @return mixed
		 */
		public function getMessage(){
			return $this->message;
		}

		/**
		 * @return string[]
		 */
		public function getAdditionFields(){
			return $this->additionFields;
		}
	}
}

