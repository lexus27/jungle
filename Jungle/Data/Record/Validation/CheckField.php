<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 21.11.2016
 * Time: 14:51
 */
namespace Jungle\Data\Record\Validation {
	
	use Jungle\Data\Record;

	/**
	 * Class CheckField
	 * @package Jungle\Data\Record\Validation
	 */
	class CheckField extends Validation{

		/** @var string  */
		public $type = 'CheckField';

		final public function __construct(){}

		/**
		 * @param Record $record
		 * @param ValidationCollector $collector
		 * @return array
		 */
		function validate(Record $record, ValidationCollector $collector){
			// Проверка типа значения по полю происходит только если значение не NULL
			$schema = $record->getSchema();
			foreach($schema->fields as $name => $field){
				$value = $record->{$name};
				if(isset($value)){
					if(!$field->validate($value)){
						$collector->error($name, $this);
					}
				}
			}
		}
	}
}

