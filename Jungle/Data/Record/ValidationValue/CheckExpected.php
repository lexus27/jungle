<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.11.2016
 * Time: 21:14
 */
namespace Jungle\Data\Record\ValidationValue {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Validation\ValidationCollector;

	/**
	 * Class CheckExpected
	 * @package Jungle\Data\Record\Validator
	 */
	class CheckExpected extends Validator{

		/** @var array  */
		public $value_list = [];

		/** @var string  */
		public $type = 'CheckExpected';

		/**
		 * CheckExpected constructor.
		 * @param $value_list
		 */
		public function __construct($value_list){
			$this->value_list = $value_list;
		}

		/**
		 * @param $field_name
		 * @param $value
		 * @param ValidationCollector $collector
		 * @return mixed|void
		 */
		public function validate($field_name, $value, ValidationCollector $collector){
			if(!is_null($value) && !in_array($value, $this->value_list)){
				$collector->error($field_name, $this);
			}
		}


	}
}

