<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.11.2016
 * Time: 13:13
 */
namespace Jungle\Data\Record\Validation {

	use Jungle\Data\Record;

	/**
	 * Class Validation
	 * @package Jungle\Data\Record\Validation
	 */
	abstract class Validation extends ValidationRule{

		/** @var   */
		public $type;

		/** @var array  */
		protected $fields = [];

		/**
		 * Validation constructor.
		 * @param $fields
		 */
		public function __construct($fields){
			$this->fields = is_array($fields)?$fields:[$fields];
		}

		/**
		 * @return array
		 */
		public function fields(){
			return $this->fields;
		}

		/**
		 * @param Record $record
		 * @param ValidationCollector $collector
		 * @return mixed
		 */
		abstract function validate(Record $record, ValidationCollector $collector);

		/**
		 * @return mixed
		 */
		public function getValidationType(){
			return $this->type;
		}

	}
}

