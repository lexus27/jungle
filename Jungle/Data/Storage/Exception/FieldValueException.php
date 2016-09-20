<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 17:28
 */
namespace Jungle\Data\Storage\Exception {

	/**
	 * Class Field
	 * @package Jungle\Data\Storage\Exception
	 */
	class FieldValueException extends Operation{

		/** @var  string */
		protected $field_name;

		/** @var   */
		protected $value;

		/**
		 * FieldValueException constructor.
		 * @param string $fieldKey
		 * @param mixed $value
		 * @param string $message
		 * @param int $code
		 * @param \Exception|null $previous
		 */
		public function __construct($fieldKey, $value, $message, $code, $previous = null){
			$this->field_name = $fieldKey;
			$this->value = $value;
			parent::__construct($message, $code, $previous);
		}


		/**
		 * @param $fieldName
		 * @return $this
		 */
		public function setFieldName($fieldName){
			$this->field_name = $fieldName;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getFieldName(){
			return $this->field_name;
		}

		public function getValue(){
			return $this->value;
		}

	}
}

