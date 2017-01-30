<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.11.2016
 * Time: 12:49
 */
namespace Jungle\Data\Record\Validation {
	
	use Jungle\Data\Record;

	/**
	 * Class CheckLength
	 * @package Jungle\Data\Record\Validation
	 */
	class CheckLength extends Validation{

		/** @var string  */
		public $type = 'CheckLength';

		/** @var  int */
		public $min;

		/** @var  int */
		public $max;

		function __construct($min, $max, $fields){
			parent::__construct($fields);
			$this->min = $min;
			$this->max = $max;
		}
		
		function validate(Record $record, ValidationCollector $collector){
			$data = $record->getProperties($this->fields);
			foreach($data as $key => $value){
				$len = mb_strlen($value); // число символов UNICODE
				if(is_string($value) && $len > $this->max || $len < $this->min){
					$collector->error($key, $this);
				}
			}
		}
	}
}

