<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 17:39
 */
namespace Jungle\Util\Data\Validation {

	use Jungle\Util\Data\Validation\Message\ValidatorMessage;

	/**
	 * Class Validator
	 * @package Jungle\Util\Data\Validation
	 */
	abstract class Validator extends ExpertizeAbstract implements ValidatorInterface{

		/** @var  string|null */
		protected $field_name;

		/**
		 * @param $object
		 * @param array $parameters
		 * @return mixed
		 */
		public function validate($object,array $parameters = []){
			return $this->expertize($object,$parameters);
		}

		/**
		 * @param $result
		 * @return ValidatorMessage|\Jungle\Util\Data\Validation\MessageInterface
		 */
		protected function _prepareMessage($result){
			$params = $this->getParams();unset($params['field_name']);
			return new ValidatorMessage($this->type, $this->field_name, $params);
		}


	}

}

