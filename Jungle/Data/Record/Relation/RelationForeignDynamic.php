<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 14.11.2016
 * Time: 22:24
 */
namespace Jungle\Data\Record\Relation {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Schema\Schema;
	use Jungle\Data\Record\Snapshot;

	/**
	 * Class RelationForeignDynamic
	 * @package Jungle\Data\Record\Relation
	 *
	 * В данной реализации,
	 * в формировании динамической связи учавствует всего 1 объект связи в отличие от 3х динамик типов связей
	 *
	 * В текущей реализации объект-связь RelationMany автоматически работает и с Foreign и с ForeignDynamic
	 * без установленной логики для их опознания и логики обработки спецефичной-каждому типу
	 * на стороне RelationAbstractHost(RelationMany & RelationOne) не происходит никакой логики для обеспечения работы конкретно динамической связи)
	 *
	 *
	 */
	class RelationForeignDynamic extends RelationForeign{

		/**
		 * В данном свойстве указывается поле которое отвечает за хранение названия схемы
		 * @var  string
		 */
		public $referenced_schema;

		/** @var array  */
		public $referenced_schema_allowed = [];


		public $virtual = true;


		public function __construct(
			$name,
			$fields,
			$referenced_fields,
			$referenced_schema_specifier,
			$on_update = null,
			$on_delete = null,
			$virtual = null
		){


			$this->name = $name;
			$this->fields = !is_array($fields)?[$fields]:$fields;
			$this->referenced_fields = !is_array($referenced_fields)?[$referenced_fields]:$referenced_fields;
			$this->referenced_schema = $referenced_schema_specifier;

			if($on_update)$this->on_update = $on_update;
			if($on_delete)$this->on_delete = $on_delete;
			if(isset($virtual))$this->virtual = $virtual;
		}

		/**
		 * @param Schema $schema
		 * @return bool
		 */
		public function isAllowedSchema(Schema $schema){
			if(!$this->referenced_schema_allowed){
				return true;
			}
			foreach($this->referenced_schema_allowed as $id){
				if($schema->isDerivativeFrom($id)){
					return true;
				}
			}
			return false;
		}

		/**
		 * @param Record $record
		 * @param Snapshot $snapshot
		 * @return mixed|void
		 * @throws Record\Exception
		 * @throws \Exception
		 */
		public function beforeRecordSave(Record $record, Snapshot $snapshot = null){
			if(($related = $record->getRelatedLoaded($this->name))!==false){
				/** @var Record $related */
				if($related){
					$schema = $related->getSchema();
					if(!$this->isAllowedSchema($schema)){
						throw new \Exception(
							'Trying to assign the related object in "' . $this->schema->getName() . '.' . $this->name .
							'" that does not fit the scheme'
						);
					}

					$related->save();

					$record->assign($this->dataTo($related));

				}else{
					// В случае если связь была сброшена в нулл
					$record->assign($this->dataEmpty());
				}
			}
		}

		public function getLocalFields(){
			$fields = array_merge($this->fields,[$this->referenced_schema]);
			return $fields;
		}
		/**
		 * @param Record $record
		 * @return Record|null
		 */
		public function load(Record $record){
			// Загрузка при отсутствии значений полей внешнего ключа
			// Проверяем есть ли возможность загрузить связанный объект
			$data = $record->getProperties($this->getLocalFields());
			if(in_array(null, $data, true)){
				return null;
			}

			// Получаем указатель на схему и получаем её
			$referenced_schema = $record->getProperty($this->referenced_schema);
			$schema = $this->getSchemaGlobal($referenced_schema);

			// создаем условие для загрузки записи и загружаем по полученой схеме 1 запись
			$condition = $this->createConditionFrom($record);
			return $schema->loadFirst($condition);
		}

		public function createConditionTo(Record $referenced, array $append_conditions = NULL){
			$condition = parent::createConditionTo($referenced);
			$condition[] = [$this->referenced_schema, '=', $referenced->getSchema()->getIdentity()];
			if($append_conditions){
				foreach($append_conditions as $k => $v){
					$condition[] = is_array($v)?$v:[$k,'=',$v];
				}
			}
			return $condition;
		}

		public function dataTo(Record $related, array $phantom = NULL){
			$data = parent::dataTo($related);
			$data[$this->referenced_schema] = $related->getSchema()->getIdentity();

			if($phantom){
				$data = array_replace($phantom, $data);
			}

			return $data;
		}

		public function dataEmpty(){
			return array_fill_keys($this->getLocalFields(), null);
		}


	}
}

