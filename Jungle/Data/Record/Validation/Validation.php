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
	abstract class Validation{

		public $type;

		/** @var array  */
		protected $fields = [];


		public function fields(){
			return $this->fields;
		}

		public function __construct($fields){
			$this->fields = is_array($fields)?$fields:[$fields];
		}

		abstract function validate(Record $record, ValidationCollector $collector);

	}
}

