<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 15:45
 */
namespace Jungle\Util\Data\Validation\Message {
	
	use Jungle\Util\Data\Validation\Message;
	use Jungle\Util\Data\Validation\ValidationErrorInterface;

	/**
	 * Class ValidationError
	 * @package Jungle\Util\Data\Validation\Message
	 */
	class ValidationError extends ValidatorMessage implements ValidationErrorInterface{

		/**
		 * @return string[]
		 */
		public function getAffectedFields(){
			// TODO: Implement getAffectedFields() method.
		}

		/**
		 * @param $field_name
		 * @return mixed
		 */
		public function getMessagesFor($field_name){
			// TODO: Implement getMessagesFor() method.
		}

		/**
		 * @param ValidatorMessage $message
		 * @return mixed
		 */
		public function addValidatorMessage(ValidatorMessage $message){
			// TODO: Implement addValidatorMessage() method.
		}
	}
}

