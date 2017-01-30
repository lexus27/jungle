<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 16.12.2016
 * Time: 23:59
 */
namespace Jungle\Data\Record\ValidationValue {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Validation\ValidationCollector;

	/**
	 * Class ValidationGroup
	 * @package Jungle\Data\Record\ValidationValue
	 */
	class ValidationGroup{

		/** @var Validator  */
		public $validation;

		/** @var   */
		public $fields;

		/**
		 * ValidationGroup constructor.
		 * @param $fields
		 * @param Validator $validation
		 */
		public function __construct($fields, Validator $validation){
			$this->fields = $fields;
			$this->validation = $validation;
		}

		/**
		 * @param Record $record
		 * @param ValidationCollector $collector
		 */
		public function validate(Record $record, ValidationCollector $collector){
			$data = $record->getProperties($this->fields);
			foreach($data as $k => $v){
				$this->validation->validate($k,$v,$collector);
			}
		}


	}
}

