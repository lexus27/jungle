<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.11.2016
 * Time: 21:09
 */
namespace Jungle\Data\Record\Validation {

	use Jungle\Data\Record;

	/**
	 * Class CheckPresenceOf
	 * @package Jungle\Data\Record\Validation
	 */
	class CheckPresenceOf extends Validation{

		/** @var string  */
		public $type = 'PresenceOf';

		/**
		 * @param Record $record
		 * @param ValidationCollector $collector
		 * @return array
		 */
		public function validate(Record $record, ValidationCollector $collector){
			$data = $record->getProperties($this->fields);
			$schema = $record->getSchema();
			$pk = $schema->pk;
			$autoGen = $schema->pk_auto_generation;
			$foreign_dependency = $schema->foreign_dependency;
			foreach($data as $name => $value){
				if(empty($value)){
					// Фикс для согласования авто-генерируемого первичника
					if($pk === $name && $autoGen){continue;}

					// Фикс для согласования Foreign Зависимых полей
					if(isset($foreign_dependency[$name])){
						$value = $record->getRelatedLoaded($foreign_dependency[$name]);
						if($value === false){
							continue;
						}
						if(empty($value)){
							$collector->error($name, $this);
						}
					}else{
						$collector->error($name,$this);
					}
				}
			}
		}
	}
}

