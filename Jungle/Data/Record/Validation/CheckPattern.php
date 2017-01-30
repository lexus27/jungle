<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.11.2016
 * Time: 12:31
 */
namespace Jungle\Data\Record\Validation {

	use Jungle\Data\Record;

	/**
	 * Class CheckPattern
	 * @package Jungle\Data\Record\Validation
	 */
	class CheckPattern extends Validation{

		/** @var string  */
		public $type = 'CheckPattern';

		/** @var  string */
		public $pattern;

		/**
		 * CheckPattern constructor.
		 * @param $pattern
		 * @param $fields
		 */
		public function __construct($pattern, $fields){
			$this->pattern = $pattern;
			parent::__construct($fields);
		}

		/**
		 * @param Record $record
		 * @param ValidationCollector $collector
		 * @return array
		 */
		public function validate(Record $record, ValidationCollector $collector){
			$values = $record->getProperties($this->fields);
			$pattern = $this->pattern;
			foreach($values as $key => $value){
				if(!is_null($value) && !preg_match($pattern, $value)){
					$collector->error($key,$this);
				}
			}
		}

	}
}

