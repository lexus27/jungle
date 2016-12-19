<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.11.2016
 * Time: 21:51
 */
namespace Jungle\Data\Record\ValidationValue {
	
	use Jungle\Data\Record;
	use Jungle\Data\Record\Validation\ValidationCollector;

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


		public function __construct($min, $max){
			$this->min = $min;
			$this->max = $max;
		}

		/**
		 * @param $field_name
		 * @param $value
		 * @param ValidationCollector $collector
		 */
		public function validate($field_name, $value, ValidationCollector $collector){
			if(!is_null($value) && $value > $this->max || $value < $this->min){
				$collector->error($field_name, $this);
			}
		}
	}
}

