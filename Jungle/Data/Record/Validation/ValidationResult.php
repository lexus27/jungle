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

		/** @var int  */
		protected static $_level = 0;

		/**
		 * ValidationResult constructor.
		 * @param Record $record
		 */
		public function __construct(Record $record){
			$this->record = $record;
			parent::__construct('ValidationResult');
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

		/**
		 * @return bool
		 */
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
			return isset($this->related_error_validations[$relation_key][0])?$this->related_error_validations[$relation_key][0]:null;
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

		/**
		 * @return string
		 */
		function __toString(){
			self::$_level++;
			$schema = $this->record->getSchema();

			$fields = [];
			foreach($this->field_errors as $fieldKey => $validations){
				$field = "\t".'"'.$fieldKey.'"('.$schema->fields[$fieldKey]->getFieldType().') value: ' .
				         var_export($this->record->{$fieldKey},true) . PHP_EOL;
				$field.= "\t".'ValidationsFailed: ['. PHP_EOL ;
				foreach($validations as $validation){
					$field.= "\t\t".json_encode($validation) . PHP_EOL;
				}
				$field.= "\t".']';
				$fields[] = $field;
			}

			$relations = [];
			foreach($this->related_error_validations as $relationKey => $results){
				$relation = "\t".'"'.$relationKey.'": [';
				$res = [];
				foreach($results as $result){
					$str = "\r\n$result";
					$str = strtr($str, [
						PHP_EOL => PHP_EOL . str_pad("",self::$_level+1,"\t")
					]);
					$res[]= '{'.$str."\r\n".str_pad("",self::$_level,"\t").'}';
				}
				$relation.=implode(',',$res);
				$relation.= ']';
				$relations[] = $relation;
			}



			$string = [];
			if(self::$_level === 1){
				$string[]= 'ValidationError: "'.get_class($this).'"  ';
			}
			$string[]='Schema: "'. $schema->getName().'"';
			if($this->constraint_errors){
				$string[]='Constraints: '. implode(', ',$this->constraint_errors);
			}

			if($fields){
				$string[]= "Fields: ". PHP_EOL.implode(PHP_EOL, $fields);
			}
			if($relations){
				$string[]= "Relations: {". PHP_EOL.implode(PHP_EOL, $relations).PHP_EOL.'}';
			}

			self::$_level--;
			return implode(PHP_EOL, $string);

		}
		
		/**
		 * For other readers export for json and etc...
		 * @return array
		 */
		public function export(){
			$schema = $this->record->getSchema();
			
			$fields = [];
			foreach($this->field_errors as $fieldKey => $validations){
				/** @var Validation $validation */
				foreach($validations as $validation){
					$fields[$fieldKey][] = $validation->type;
				}
			}
			$relations = [];
			foreach($this->related_error_validations as $relationKey => $results){
				/** @var ValidationResult $result */
				foreach($results as $result){
					$relations[$relationKey][] = $result->export();
				}
			}
			
			return [
				'name'          => $schema->getName(),
				'fields'        => $fields,
				'relations'     => $relations,
				'constrains'    => $this->constraint_errors
			];
		}

	}
}

