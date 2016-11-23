<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.11.2016
 * Time: 12:57
 */
namespace Jungle\Data\Record\Validation {

	use Jungle\Data\Record;

	/**
	 * Class CheckLengthBytes
	 * @package Jungle\Data\Record\Validation
	 */
	class CheckLengthBytes extends CheckLength{

		public $type = 'CheckLengthBytes';

		function validate(Record $record, ValidationCollector $collector){
			$data = $record->getProperties($this->fields);
			foreach($data as $key => $value){
				$len = strlen($value); // Число байт
				if(is_string($value) && $len > $this->max || $len < $this->min){
					$collector->error($key, $this);
				}
			}
		}
	}
}

