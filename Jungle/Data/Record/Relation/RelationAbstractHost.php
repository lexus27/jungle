<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 16.11.2016
 * Time: 5:13
 */
namespace Jungle\Data\Record\Relation {

	use Jungle\Data\Record;

	/**
	 * Class RelationAbstractHost
	 * @package Jungle\Data\Record\Relation
	 */
	abstract class RelationAbstractHost extends Relation{

		/**
		 * Связь которая находится на противоположной схеме
		 * это может быть полезно, чтобы не указывать для 2х противоположных связей FOREIGN <> MANY | ONE
		 * одинаковый набор метаданных только наоборот
		 *
		 * @var RelationForeign
		 */
		public $referenced_relation;

		public $condition;

		public $phantom;

		public $master;


		/**
		 * RelationMany constructor.
		 * @param $name
		 * @param $referenced_relation
		 * @param null $referenced_schema_name
		 */
		public function __construct($name, $referenced_relation, $referenced_schema_name = null){
			$this->name = $name;
			$this->referenced_relation = ($referenced_schema_name? $referenced_schema_name . '.' :'') . $referenced_relation;
		}

		public function _check(){

			$initialized = (!$this->referenced_schema || !$this->referenced_fields || !$this->fields);


			if(is_string($this->referenced_relation)){
				list($schema_name, $relation_name) = array_replace([null,null],explode('.',$this->referenced_relation));

				if($schema_name && !$relation_name){
					$relation_name = $schema_name;
					$schema_name = null;
				}

				if($schema_name){
					$this->referenced_schema = $this->getSchemaGlobal($schema_name);
					$this->referenced_relation = $this->referenced_schema->getRelation($relation_name);
				}else{
					if($this->referenced_schema){
						$this->referenced_schema = $this->getSchemaGlobal($this->referenced_schema);
					}
					$this->referenced_relation = $this->referenced_schema->getRelation($relation_name);
				}

				$this->fields = $this->referenced_relation->referenced_fields;
				$this->referenced_fields = $this->referenced_relation->fields;

				$initialized = true;
			}


			if(!$initialized && $this->referenced_relation instanceof RelationForeign){
				$this->fields = $this->referenced_relation->referenced_fields;
				$this->referenced_fields = $this->referenced_relation->fields;
			}
		}

		public function initialize(){}


		/**
		 * @return RelationForeign
		 */
		public function getReferencedRelation(){
			if($this->referenced_relation instanceof RelationForeign){
				return $this->referenced_relation;
			}else{
				$this->_check();
				return $this->referenced_relation;
			}
		}

		public function referencedCondition(Record $record){
			return $this->getReferencedRelation()->createConditionTo($record, $this->condition);
		}

		public function referencedData(Record $record){
			return $this->getReferencedRelation()->dataTo($record, $this->phantom);
		}

		public function getAttachSignature(){
			return crc32(implode(array_merge($this->fields,$this->referenced_fields)));
		}


	}
}

