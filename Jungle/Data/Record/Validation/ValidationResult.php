<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.11.2016
 * Time: 11:58
 */
namespace Jungle\Data\Record\Validation {
	
	use Jungle\Data\Record;

	/**
	 * Class ValidationResult
	 * @package Jungle\Data\Record\Validation
	 */
	class ValidationResult extends ValidationCollector{

		const CONSTRAINT_UNKNOWN    = 'unknown';
		const CONSTRAINT_DUPLICATE  = 'duplicate';
		const CONSTRAINT_RELATION   = 'relation';

		/** @var  Record */
		protected $record;

		/** @var  string[] */
		protected $constraint_errors = [];

		/** @var array  */
		protected $field_errors = [];

		/** @var ValidationResult[]  */
		protected $related_error_validations = [];

		/**
		 * ValidationResult constructor.
		 * @param Record $record
		 */
		public function __construct(Record $record){
			$this->record = $record;
		}

		/**
		 * @param $relation_key
		 * @param ValidationResult $result
		 */
		public function addRelatedValidation($relation_key, ValidationResult $result){
			if(!isset($this->related_error_validations[$relation_key])){
				$this->related_error_validations[$relation_key] = [];
			}
			$this->related_error_validations[$relation_key][] = $result;
		}

		public function hasObjectErrors(){
			return !empty($this->field_errors);
		}

		/**
		 * @return bool
		 */
		public function hasErrors(){
			return !empty($this->field_errors)
			       || !empty($this->related_error_validations)
			       || !empty($this->constraint_errors);
		}

		/**
		 * @param null $relation_key
		 * @return bool
		 */
		public function hasRelatedErrors($relation_key = null){
			if($relation_key===null){
				return !empty($this->related_error_validations);
			}
			return isset($this->related_error_validations[$relation_key]);
		}

		/**
		 * @param $relation_key
		 * @return ValidationResult|null
		 */
		public function getOneRelatedValidation($relation_key){
			return isset($this->related_error_validations[$relation_key])?$this->related_error_validations[$relation_key][0]:null;
		}

		/**
		 * @param $relation_key
		 * @return ValidationResult[]
		 */
		public function getRelatedValidation($relation_key){
			return isset($this->related_error_validations[$relation_key])?$this->related_error_validations[$relation_key]:[];
		}

		/**
		 * @param $error_type
		 */
		public function addConstraintError($error_type){
			$this->constraint_errors[] = $error_type;
		}

		/**
		 * @return bool
		 */
		public function hasUnknownConstraint(){
			return in_array(self::CONSTRAINT_UNKNOWN, $this->constraint_errors, true);
		}

	}
}

