<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 14.11.2016
 * Time: 15:53
 */
namespace Jungle\Data\Record\Relation {
	
	use Jungle\Data\Record;
	use Jungle\Data\Record\Schema\Schema;
	use Jungle\Data\Record\Snapshot;

	/**
	 * Class RelationForeign
	 * @package Jungle\Data\Record\Relation
	 */
	class RelationForeign extends RelationSchema{

		const ACTION_RESTRICT   = 'restrict';
		const ACTION_CASCADE    = 'cascade';
		const ACTION_SETNULL    = 'setnull';


		public $on_delete = self::ACTION_RESTRICT;

		public $on_update = self::ACTION_RESTRICT;

		public $virtual = false;

		public $nullable = true;

		public function __construct(
			$name,
			$fields,
			$referenced_fields,
			$referenced_schema,
			$on_update = null,
			$on_delete = null,
			$virtual = null
		){


			$this->name = $name;
			$this->fields = !is_array($fields)?[$fields]:$fields;
			$this->referenced_fields = !is_array($referenced_fields)?[$referenced_fields]:$referenced_fields;
			$this->referenced_schema = $referenced_schema;

			if($on_update)$this->on_update = $on_update;
			if($on_delete)$this->on_delete = $on_delete;
			if(isset($virtual))$this->virtual = $virtual;
		}

		/**
		 * @param Record $record
		 * @return Record|null
		 */
		public function load(Record $record){
			// Загрузка при отсутствии значений полей внешнего ключа
			// Проверяем есть ли возможность загрузить связанный объект
			$data = $record->getProperties($this->fields);

			// (У нас не работает в хранилище авто подмена на оператор IS NULL при left = null)
			// если хотябы 1 значение NULL
			if(in_array(null, $data, true)){
				return null;
			}
			// альтернатива : если хотябы 1 значение NULL
			//if(array_intersect($data,[null])){
			//	return null;
			//}
			// в таком случае будет: если все NULL
			//if(!array_diff($data,[null])){
			//
			//}

			$schema = $this->getSchemaGlobal($this->referenced_schema);

			$condition = $this->createConditionFrom($record);
			return $schema->loadFirst($condition);
		}

		public function inspectContextEventsBefore(Record $record, array $changes){
			if($changes['change']){
				$this->schema->onRelatedSingleChange($record,$this->name,$changes['change']['-'],$changes['change']['+']);
			}
		}

		/**
		 * @param Record $record
		 * @param Snapshot $snapshot
		 * @return mixed|void
		 * @throws Record\ORMException
		 */
		public function beforeRecordSave(Record $record, Snapshot $snapshot = null){
			if(($related = $record->getRelatedLoaded($this->name))!==false){
				/** @var Record $related */
				if($related){
					$related->save();

					$data = $this->dataTo($related);
					$record->assign($data);

					$relations = $this->getOwnerships($related->getSchema());
					foreach($relations as $name => $relation){
						if($relation instanceof RelationSchema){
							$relation->changeBackward($related, $record);
						}
					}

				}else{
					$record->assign($this->dataEmpty());
				}
			}
		}


		public function changeBackward(Record $record, Record $related = null){
			$record->setRelated($this->name, $related);
		}


		/**
		 * Может потребоваться для обратного выставления свойств
		 * @param Schema $schema
		 * @return array
		 */
		public function getOwnerships(Schema $schema){
			if($this->isAllowedSchema($schema)){
				$ownerships = [];
				foreach($schema->getRelations() as $name => $relation){
					if(
						$relation instanceof RelationSchemaHost &&
						$relation->getReferencedRelation() === $this
					){
						$ownerships[$name] = $relation;
					}
				}
				return $ownerships;
			}
		}

		/**
		 * @param Record $referenced
		 * @param array $append_conditions
		 * @return array
		 */
		public function createConditionTo(Record $referenced, array $append_conditions = null){
			$data = $referenced->getProperties($this->referenced_fields);
			$data = array_combine($this->fields, $data);
			$condition = [];
			foreach($data as $k => $v){
				$condition[] = [$k , '=', $v];
			}
			if($append_conditions){
				foreach($append_conditions as $k => $v){
					$condition[] = is_array($v)?$v:[$k,'=',$v];
				}
			}
			return $condition;
		}


		/**
		 * @param Record $record
		 * @param array $append_conditions
		 * @return array Условие которое подойдет для выборки связанных записей которые ссылаются на $record
		 *
		 * Условие которое подойдет для выборки связанных записей которые ссылаются на $record
		 */
		public function createConditionFrom(Record $record, array $append_conditions = null){
			$data = $record->getProperties($this->fields);
			$data = array_combine($this->referenced_fields, $data);
			$condition = [];
			foreach($data as $k => $v){
				$condition[] = [$k , '=', $v];
			}
			if($append_conditions){
				foreach($append_conditions as $k => $v){
					$condition[] = is_array($v)?$v:[$k,'=',$v];
				}
			}
			return $condition;
		}


		public function dataTo(Record $related, array $phantom = null){
			$data = $related->getProperties($this->referenced_fields);
			$data = array_combine($this->fields, $data);

			if($phantom){
				$data = array_replace($phantom, $data);
			}

			return $data;
		}

		public function dataEmpty(){
			return array_fill_keys($this->fields, null);
		}


		public function isAllowedSchema(Schema $schema){
			return $schema->isDerivativeFrom($this->referenced_schema);
		}

		/**
		 * @return RelationForeign
		 */
		public function getReferencedRelation(){
			foreach($this->getSchemaGlobal($this->referenced_schema)->getRelations() as $name => $relation){
				if(
					$relation instanceof RelationSchemaHost &&
					$this->referenced_fields === $relation->fields &&
					$this->fields === $relation->referenced_fields
				){
					return $relation;
				}
			}
			return null;
		}

		public function initialize(Schema $schema){
			foreach($this->fields as $name){
				if(!$this->schema->fields[$name]->nullable){
					$this->nullable = false;
					break;
				}
			}
		}
	}
}

