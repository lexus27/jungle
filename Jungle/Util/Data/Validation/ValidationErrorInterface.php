<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 15:41
 */
namespace Jungle\Util\Data\Validation {

	use Jungle\Util\Data\Validation\Message\ValidatorMessage;

	/**
	 * Interface ValidationErrorInterface
	 * @package Jungle\Util\Data\Validation
	 */
	interface ValidationErrorInterface{

		/**
		 * @return string[]
		 */
		public function getAffectedFields();

		/**
		 * @param $field_name
		 * @return mixed
		 */
		public function getMessagesFor($field_name);

		/**
		 * @param ValidatorMessage $message
		 * @return mixed
		 */
		public function addValidatorMessage(ValidatorMessage $message);

	}
}

