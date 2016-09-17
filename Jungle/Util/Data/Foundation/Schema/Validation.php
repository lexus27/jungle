<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.09.2016
 * Time: 13:05
 */
namespace Jungle\Util\Data\Foundation\Schema {

	use Jungle\Util\Data\Foundation\Record\PropertyRegistryInterface;
	use Jungle\Util\Data\Foundation\Schema\Validation\MessageInterface;
	use Jungle\Util\Data\Foundation\Schema\Validation\ValidatorInterface;

	/**
	 * Class Validation
	 * @package Jungle\Util\Data\Foundation\Schema
	 */
	class Validation{

		/** @var  ValidatorInterface[] */
		protected $validators = [];

		/** @var  PropertyRegistryInterface */
		protected $last_validated_object;

		/** @var  MessageInterface[] */
		protected $last_validated_messages = [];

		/**
		 * @param ValidatorInterface $validator
		 * @return $this
		 */
		public function addValidator(ValidatorInterface $validator){
			$this->validators[] = $validator;
			return $this;
		}

		/**
		 * @param PropertyRegistryInterface $object
		 * @return bool
		 */
		public function validate(PropertyRegistryInterface $object){
			$this->last_validated_object = $object;
			$this->last_validated_messages = [];
			foreach($this->validators as $validator){
				$message = $validator->validate($object);
				if($message instanceof MessageInterface){
					$this->last_validated_messages[] = $message;
				}
			}
			return empty($this->last_validated_messages);
		}


		/**
		 * @return MessageInterface[]
		 */
		public function getMessages(){
			return $this->last_validated_messages;
		}




	}
}

