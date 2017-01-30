<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.11.2016
 * Time: 12:31
 */
namespace Jungle\Data\Record\ValidationValue {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Validation\ValidationCollector;

	/**
	 * Class CheckPattern
	 * @package Jungle\Data\Record\Validator
	 */
	class CheckPattern extends Validator{

		/** @var string  */
		public $type = 'CheckPattern';

		/** @var  string */
		public $pattern;

		public function __construct($pattern){
			$this->pattern = $pattern;
		}

		public function validate($field_name, $value, ValidationCollector $collector){
			$pattern = $this->pattern;
			if(!is_null($value) && !preg_match($pattern, $value)){
				$collector->error($field_name,$this);
			}
		}

	}
}

