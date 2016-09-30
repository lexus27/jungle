<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 17:40
 */
namespace Jungle\Util\Data\Validation\Validator {

	use Jungle\Util\Data\Validation\Validator;

	/**
	 * Class Confirmation
	 * @package Jungle\Util\Data\Validation\Validator
	 */
	class Confirmation extends Validator{

		/** @var  string */
		protected $type = 'Confirmation';

		/** @var  string */
		protected $field_name;

		/** @var  string|array|null  */
		protected $reference_field;

		/**
		 * @param $object
		 * @return bool
		 */
		protected function _expertize($object){

			$fieldName = null;
			$referenceFields = is_array($this->reference_field)?$this->reference_field:[$this->reference_field];

			if($this->field_name!==null){
				$fieldName = $this->field_name;
			}elseif(count($referenceFields)>1){
				$fieldName = array_shift($referenceFields);
			}

			if($fieldName){

				$keys = array_keys($referenceFields,$fieldName, true);
				foreach($keys as $key){
					array_splice($referenceFields,$key,1);
				}

				$value = $object->{$fieldName};
				foreach($referenceFields as $fName){
					if($value !== $object->{$fName}){
						return false;
					}
				}

			}

			return true;
		}
	}
}

