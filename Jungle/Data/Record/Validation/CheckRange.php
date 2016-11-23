<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.11.2016
 * Time: 21:51
 */
namespace Jungle\Data\Record\Validation {
	
	use Jungle\Data\Record;

	/**
	 * Class CheckRange
	 * @package Jungle\Data\Record\Validation
	 */
	class CheckRange extends Validation{

		/** @var int */
		public $min;

		/** @var int */
		public $max;

		/** @var string  */
		public $type = 'CheckRange';


		public function __construct($min, $max, $fields){
			parent::__construct($fields);
			$this->min = $min;
			$this->max = $max;
		}

		/**
		 * @param Record $record
		 * @param ValidationCollector $collector
		 * @return array
		 */
		public function validate(Record $record, ValidationCollector $collector){
			$data = $record->getProperties($this->fields);
			foreach($data as $key => $value){
				if(!is_null($value) && $value > $this->max || $value < $this->min){
					$collector->error($key, $this);
				}
			}
		}
	}
}

