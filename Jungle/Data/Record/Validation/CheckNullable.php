<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.11.2016
 * Time: 20:19
 */
namespace Jungle\Data\Record\Validation {

	use Jungle\Data\Record;

	/**
	 * Class CheckNullable
	 * @package Jungle\Data\Record
	 */
	class CheckNullable extends Validation{

		public $type = 'PresenceOf';

		final public function __construct(){}

		/**
		 * @param Record $record
		 * @param ValidationCollector $collector
		 * @return array
		 */
		public function validate(Record $record, ValidationCollector $collector){
			// Отличие CheckNullable от CheckPresenceOf в том что первый проверяет соответствие
			// Свойства поля nullable тому что значение не является NULL,
			// а CheckPresenceOf проверяет Присутствие значение конструкцией !empty().

			// Проверка полей которые участвуют в отложенном присвоении (зависящие от ForeignRelation)
			// Должна производиться только согласованно с полем-отношения
			// Валидация должна проводиться по наличию объекта-значения в поле отношения
			// А это означает что в результате валидации могут быть сообщения относящееся к связанному полю
			// То есть было бы выгоднее видеть ошибку типа "Связанная запись 'company' не должна отсутствовать"
			// Вместо: "Поле company_id не должно быть пустым"
			//
			$schema = $record->getSchema();
			$foreign_dependency = $schema->foreign_dependency;
			$pk = $schema->pk;
			$autoGen = $schema->pk_auto_generation;
			foreach($schema->fields as $name => $field){
				// Фикс для согласования авто-генерируемого первичника
				if($pk === $name && $autoGen){continue;}
				$value = $record->{$name};

				if(!$field->nullable && is_null($value)){
					// Фикс для согласования Foreign Зависимых полей
					if(isset($foreign_dependency[$name])){
						$value = $record->getRelatedLoaded($foreign_dependency[$name]);
						if($value === false){
							continue;
						}
						if(is_null($value)){
							$collector->error($name,$this);
						}
					}else{
						$collector->error($name,$this);
					}
				}
			}
		}

	}
}

