<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 14.11.2016
 * Time: 15:59
 */
namespace Jungle\Data\Record\Relation {
	
	use Jungle\Data\Record;
	use Jungle\Data\Record\Snapshot;

	/**
	 * Class RelationOne
	 * @package Jungle\Data\Record\Relation
	 */
	class RelationOne extends RelationSchemaHost{

		/**
		 * RelationMany constructor.
		 * @param $name
		 * @param $referenced_relation
		 * @param null $referenced_schema_name
		 */
		public function __construct($name, $referenced_relation, $referenced_schema_name = null){
			parent::__construct($name, $referenced_relation, $referenced_schema_name);
		}

		/**
		 * @param Record $record
		 * @return mixed
		 */
		public function load(Record $record){
			$relation = $this->getReferencedRelation();
			return $relation->schema->loadFirst( $this->referencedCondition($record) );
		}

		public function getFields(){
			$this->_check();
			return $this->fields;
		}

		public function getReferencedFields(){
			$this->_check();
			return $this->referenced_fields;
		}

		/**
		 * @param Record $record
		 * @param array $changes
		 */
		public function inspectContextEventsAfter(Record $record, array $changes){
			if($changes['modify']){
				$this->schema->onRelatedSingleModify($record, $this->name, $changes['modify']);
			}
			if($changes['change']){
				$this->schema->onRelatedSingleChange($record, $this->name, $changes['change']['-'],$changes['change']['+']);
			}
		}

		/**
		 * @param Record $record
		 * @param Snapshot $snapshot
		 * @return mixed|void
		 * @throws Record\ORMException
		 * @throws \Exception
		 */
		public function afterRecordSave(Record $record, Snapshot $snapshot = null){
			$this->_check();
			/** @var Record $related */
			$related = $record->getRelated($this->name);
			$this->referenced_relation->changeBackward($related, $record);
			$related->save();
		}

		/**
		 * @param Record $record
		 * @param Record|null $related
		 * @return mixed|void
		 * @throws \Exception
		 */
		public function changeBackward(Record $record, Record $related = null){
			$record->setRelated($this->name, $related);
		}
	}
}

