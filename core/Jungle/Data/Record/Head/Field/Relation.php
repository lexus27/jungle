<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.06.2016
 * Time: 16:36
 */
namespace Jungle\Data\Record\Head\Field {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Collection\Relationship;
	use Jungle\Data\Record\Head\Field;
	use Jungle\Util\Data\Foundation\Schema\OuterInteraction\SchemaAwareInterface;

	/**
	 * Class Relation
	 * @package Jungle\Data\Record\Head\Field
	 */
	class Relation extends Field{

		const TYPE_BELONGS      = 'belongs';
		const TYPE_ONE          = 'one'; // depends - unique referenced_fields(referenced schema foreign keys) group
		const TYPE_MANY         = 'many';
		const TYPE_MANY_THROUGH = 'many_through';

		const ACTION_RESTRICT = 1;
		const ACTION_CASCADE = 2;
		const ACTION_SETNULL = 3;

		/** @var  mixed|null  */
		protected $type;

		/** @var  string[]*/
		protected $fields = [];


		/** @var  string */
		protected $intermediate_schema;
		/** @var  string[]  */
		protected $intermediate_fields = [];
		/** @var  string[]  */
		protected $intermediate_referenced_fields = [];

		/** @var  array|null */
		protected $intermediate_condition;


		/** @var  string */
		protected $referenced_schema;
		/** @var  string[]  */
		protected $referenced_fields = [];

		/** @var  array|null */
		protected $referenced_condition;

		/** @var int  */
		protected $action_update = self::ACTION_RESTRICT;

		/** @var  bool  */
		protected $virtual_update = false;



		/** @var int  */
		protected $action_delete = self::ACTION_RESTRICT;

		/** @var  bool  */
		protected $virtual_delete = false;

		public function __construct($name){
			$this->name = $name;
			$this->type = null;
		}

		/**
		 * @return int
		 */
		public function getActionUpdate(){
			return $this->action_update;
		}

		/**
		 * @param int $action_update
		 * @param null $virtual
		 * @return $this
		 */
		public function setActionUpdate($action_update, $virtual = null){
			$this->action_update = $action_update;
			if($virtual!==null){
				$this->virtual_update = $virtual;
			}
			return $this;
		}

		/**
		 * @param $virtual
		 * @return $this
		 */
		public function setVirtualUpdate($virtual){
			$this->virtual_update = $virtual;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isVirtualUpdate(){
			return $this->virtual_update;
		}

		/**
		 * @return int
		 */
		public function getActionDelete(){
			return $this->action_delete;
		}

		/**
		 * @param int $action_delete
		 * @param null $virtual
		 * @return $this
		 */
		public function setActionDelete($action_delete, $virtual = null){
			$this->action_delete = $action_delete;
			if($virtual!==null){
				$this->virtual_delete = $virtual;
			}
			return $this;
		}

		/**
		 * @param $virtual
		 * @return $this
		 */
		public function setVirtualDelete($virtual){
			$this->virtual_delete = $virtual;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isVirtualDelete(){
			return $this->virtual_delete;
		}


		/**
		 * @param $referencedSchema
		 * @param $fields
		 * @param $referencedFields
		 * @param null $referencedCondition
		 * @return $this
		 */
		public function belongsTo($referencedSchema, $fields, $referencedFields, $referencedCondition = null, array $options = []){
			$this->setupRelation(
				self::TYPE_BELONGS,
				$fields,$referencedSchema,$referencedFields,
				null,null,null,
				null,$referencedCondition
			);

			$options = array_replace([
				'onUpdate' => self::ACTION_RESTRICT,
				'onDelete' => self::ACTION_RESTRICT,
				'onUpdateVirtual' => true,
				'onDeleteVirtual' => true
			],$options);

			$this->setActionUpdate($options['onUpdate'],$options['onUpdateVirtual']);
			$this->setActionDelete($options['onDelete'],$options['onDeleteVirtual']);

			return $this;
		}

		/**
		 * @param $referencedSchema
		 * @param $fields
		 * @param $referencedFields
		 * @param null $referencedCondition
		 * @return $this
		 */
		public function hasOne($referencedSchema, $fields, $referencedFields, $referencedCondition = null){
			return $this->setupRelation(
				self::TYPE_ONE,
				$fields,$referencedSchema,$referencedFields,
				null,null,null,
				null,$referencedCondition
			);
		}

		/**
		 * @param $referencedSchema
		 * @param $fields
		 * @param $referencedFields
		 * @param null $referencedCondition
		 * @return $this
		 */
		public function hasMany($referencedSchema, $fields, $referencedFields, $referencedCondition = null){
			return $this->setupRelation(
				self::TYPE_MANY,
				$fields,$referencedSchema,$referencedFields,
				null,null,null,
				null,$referencedCondition
			);
		}

		/**
		 * @param $intermediateSchema
		 * @param $referencedSchema
		 * @param $fields
		 * @param $intermediateFields
		 * @param $intermediateReferencedFields
		 * @param $referencedFields
		 * @param null $intermediateCondition
		 * @param null $referencedCondition
		 * @return Relation
		 */
		public function hasManyThrough(
			$intermediateSchema,$referencedSchema,
			$fields, $intermediateFields, $intermediateReferencedFields, $referencedFields,
			$intermediateCondition = null,$referencedCondition = null
		){
			return $this->setupRelation(
				self::TYPE_MANY_THROUGH,
				$fields,$referencedSchema,$referencedFields,
				$intermediateSchema,$intermediateFields,$intermediateReferencedFields,
				$intermediateCondition,$referencedCondition
			);
		}

		/**
		 * @param $type
		 * @param $fields
		 * @param $referencedSchema
		 * @param $referencedFields
		 * @param null $intermediateSchema
		 * @param array $intermediateFields
		 * @param array $intermediateReferencedFields
		 * @param null $intermediateCondition
		 * @param null $referencedCondition
		 * @return $this
		 */
		public function setupRelation(
			$type, $fields, $referencedSchema, $referencedFields,
			$intermediateSchema, $intermediateFields = null, $intermediateReferencedFields,
			$intermediateCondition = null, $referencedCondition = null
		){
			$fields = is_string($fields)?[$fields]:$fields;
			$intermediateFields = is_string($intermediateFields)?[$intermediateFields]:$intermediateFields;
			$intermediateReferencedFields = is_string($intermediateReferencedFields)?[$intermediateReferencedFields]:$intermediateReferencedFields;
			$referencedFields = is_string($referencedFields)?[$referencedFields]:$referencedFields;

			if(!in_array($type,[ self::TYPE_BELONGS, self::TYPE_ONE, self::TYPE_MANY, self::TYPE_MANY_THROUGH],true)){
				throw new \LogicException('Relation: not allowed relation type('.var_export($type,true).') passed');
			}

			if($type === self::TYPE_MANY_THROUGH){
				if(!is_array($fields) || !is_array($intermediateFields)){
					throw new \LogicException('Relation[THROUGH][SELF to INTERMEDIATE]: fields wrong or not supplied - ['.var_export($fields,true).' TO '.var_export($intermediateFields,true).']');
				}
				if(!is_array($intermediateReferencedFields) || !is_array($referencedFields)){
					throw new \LogicException('Relation[THROUGH][INTERMEDIATE to REFERENCED]: fields wrong or not supplied - ['.var_export($intermediateReferencedFields,true).' TO '.var_export($referencedFields,true).']');
				}

				if(count($fields) !== count($intermediateFields)){
					throw new \LogicException('Relation[THROUGH][SELF to INTERMEDIATE]: fields wrong counts - (['.implode(', ', $fields).'] TO ['.implode(', ', $intermediateFields).'])');
				}
				if(count($intermediateReferencedFields) !== count($referencedFields)){
					throw new \LogicException('Relation[THROUGH][INTERMEDIATE to REFERENCED]: fields wrong counts - (['.implode(', ', $intermediateReferencedFields).'] TO ['.implode(', ', $referencedFields).'])');
				}
			}else{
				if(!is_array($fields) || !is_array($referencedFields)){
					throw new \LogicException('Relation[DIRECT]: fields wrong or not supplied - ['.var_export($fields,true).' TO '.var_export($referencedFields,true).']');
				}

				if(count($fields) !== count($referencedFields)){
					throw new \LogicException('Relation[DIRECT]: fields wrong counts - (['.implode(', ', $fields).'] TO ['.implode(', ', $referencedFields).'])');
				}
			}

			$this->type                             = $type;
			$this->fields                           = $fields;
			$this->referenced_schema                = $referencedSchema;
			$this->referenced_fields                = $referencedFields;
			$this->referenced_condition             = $referencedCondition;

			$this->intermediate_schema              = $intermediateSchema;
			$this->intermediate_fields              = $intermediateFields;
			$this->intermediate_referenced_fields   = $intermediateReferencedFields;
			$this->intermediate_condition           = $intermediateCondition;
			return $this;
		}

		/**
		 * @param null $type
		 * @return bool
		 */
		public function isThrough($type = null){
			if($type === null){
				$type = $this->type;
			}
			return $type === self::TYPE_MANY_THROUGH;
		}

		/**
		 * @param null $type
		 * @return bool
		 */
		public function isMany($type = null){
			if($type === null){
				$type = $this->type;
			}
			return in_array($type,[self::TYPE_MANY,self::TYPE_MANY_THROUGH],true);
		}
		
		/**
		 * @return bool
		 */
		public function isBelongs(){
			return $this->type === self::TYPE_BELONGS;
		}


		/**
		 * @param Relation $fieldA
		 * @param Relation $fieldB
		 * @param bool|false $withoutThrough
		 * @return bool
		 */
		public static function isOppositeRelations(Relation $fieldA,Relation $fieldB, $withoutThrough = false){
			$schema_name = $fieldA->schema->getName();
			if(
				($fieldB->type === self::TYPE_BELONGS && $fieldA->type !== self::TYPE_BELONGS) ||
				($fieldB->type !== self::TYPE_BELONGS && $fieldA->type === self::TYPE_BELONGS)
			){
				if($schema_name !== $fieldB->referenced_schema){
					return false;
				}
				if($fieldA->fields !== $fieldB->referenced_fields){
					return false;
				}

				if($fieldA->referenced_fields !== $fieldB->fields){
					return false;
				}

				return true;
			}elseif(!$withoutThrough && $fieldB->isThrough() && $fieldA->isThrough()){
				if($schema_name !== $fieldB->referenced_schema){
					return false;
				}
				if($fieldA->intermediate_schema !== $fieldB->intermediate_schema){
					return false;
				}
				if($fieldA->fields !== $fieldB->referenced_fields){
					return false;
				}
				if($fieldA->referenced_fields !== $fieldB->fields){
					return false;
				}
				if($fieldA->intermediate_fields !== $fieldB->intermediate_referenced_fields){
					return false;
				}
				if($fieldA->intermediate_referenced_fields !== $fieldB->intermediate_fields){
					return false;
				}
				return true;
			}else{
				return false;
			}
		}

		public static function isIntermediateRelations(Relation $intermediate,Relation $container){
			if($intermediate->type === self::TYPE_BELONGS && in_array($container->type,[ self::TYPE_MANY_THROUGH])){
				if($intermediate->fields !== $container->intermediate_fields){
					return false;
				}
				if($intermediate->referenced_fields !== $container->fields){
					return false;
				}
				if($intermediate->schema->getName() !== $container->intermediate_schema){
					return false;
				}
				return true;
			}
			return false;
		}

		/**
		 * @param $belongs
		 * @param $notBelongs
		 * @return bool
		 */
		public function checkOppositeType($belongs, $notBelongs){
			return $belongs === self::TYPE_BELONGS && $notBelongs !== self::TYPE_BELONGS;
		}

		/**
		 * @return array
		 */
		public function getAllowedTypes(){
			return [
				self::TYPE_BELONGS,
				self::TYPE_ONE,
				self::TYPE_MANY,
				self::TYPE_MANY_THROUGH
			];
		}

		/**
		 * @param $type
		 * @return bool
		 */
		public function isAllowedType($type){
			return in_array($type, [ self::TYPE_BELONGS, self::TYPE_ONE, self::TYPE_MANY, self::TYPE_MANY_THROUGH ], true);
		}



		/**
		 * @return array
		 */
		public function getFields(){
			return $this->fields;
		}

		/**
		 * @param $condition
		 * @return $this
		 */
		public function setReferencedCondition($condition){
			$this->referenced_condition=$condition;
			return $this;
		}

		/**
		 * @return array|null
		 */
		public function getReferencedCondition(){
			return $this->referenced_condition;
		}

		/**
		 * @return array
		 */
		public function getReferencedFields(){
			return $this->referenced_fields;
		}

		/**
		 * @return string
		 */
		public function getReferencedSchema(){
			return $this->referenced_schema;
		}

		/**
		 * @return string
		 */
		public function getIntermediateSchema(){
			return $this->intermediate_schema;
		}

		/**
		 * @param null $condition
		 * @return $this
		 */
		public function setIntermediateCondition($condition=null){
			$this->intermediate_condition = $condition;
			return $this;
		}

		/**
		 * @return array|null
		 */
		public function getIntermediateCondition(){
			return $this->intermediate_condition;
		}

		/**
		 * @return array
		 */
		public function getIntermediateFields(){
			return $this->intermediate_fields;
		}

		/**
		 * @return array
		 */
		public function getIntermediateReferencedFields(){
			return $this->intermediate_referenced_fields;
		}

		/**
		 * @param Record $record
		 * @return Record\Collection\Relationship
		 */
		protected function createRelationship($record){
			$schemaManager = $this->schema->getSchemaManager();
			$relationship = new Record\Collection\Relationship();
			$relationship->setAncestor($schemaManager->getCollection($this->referenced_schema));
			$relationship->setHolder($record, $this);
			return $relationship;
		}

		/**
		 * @param \Jungle\Data\Record $record
		 * @return array
		 */
		protected function prepareCollectionContainCondition(Record $record){
			$condition = [];
			foreach($this->referenced_fields as $i => $name){
				$value = $record->getProperty($this->fields[$i]);
				$condition[] = [$name,'=',$value];
			}
			return array_merge($condition,(array)$this->referenced_condition);
		}


		/**
		 * @param \Jungle\Data\Record $data
		 * @param $key
		 * @return mixed|null
		 */
		public function valueAccessGet($data, $key){
			if(!$this->type){
				throw new \LogicException("Relation Field {$this->name} is not configured, please setup one of relation type[belongsTo,hasOne,hasMany,hasOneTrough,hasManyTrough], At the stage of factory!");
			}
			if(!$data instanceof Record){
				throw new \LogicException('Relation field valueAccessGet($data,$key) - $data must be Record instance');
			}
			if($data->getOperationMade() === Record::OP_CREATE){
				if($this->isMany()){
					return $this->createRelationship($data);
				}else{
					return null;
				}
			}
			$schemaManager = $this->schema->getSchemaManager();
			$referencedSchema = $schemaManager->getSchema($this->referenced_schema);
			if($this->type === self::TYPE_MANY_THROUGH){
				return $this->createRelationship($data);
			}else{
				if($this->type === self::TYPE_BELONGS || $this->type === self::TYPE_ONE){
					return $referencedSchema->loadFirst($this->prepareCollectionContainCondition($data));
				}else{
					return $this->createRelationship($data);
				}
			}
		}

		/**
		 * @param Record $data
		 * @param $key
		 * @param $value
		 * @return mixed
		 */
		public function valueAccessSet($data, $key, $value){
			if(!$this->type){
				throw new \LogicException("Relation Field {$this->name} is not configured, please setup one of relation type[belongsTo,hasOne,hasMany,hasOneTrough,hasManyTrough], At the stage of factory!");
			}
			return $data instanceof Record? $data->getOriginalData() : $data;
		}

		/**
		 * @param $original_value
		 * @return void
		 * @throws Record\Exception
		 */
		public function evaluate($original_value){
			throw new Record\Exception('Evaluate not support in relation field');
		}

		/**
		 * @param $native_value
		 * @return void
		 * @throws Record\Exception
		 */
		public function originate($native_value){
			throw new Record\Exception('Originate not support in relation field');
		}

		/**
		 * @param Record|Relationship|SchemaAwareInterface|null $native_value
		 * @return bool
		 */
		public function verify($native_value){
			if(!$this->type){
				throw new \LogicException("Relation Field {$this->name} is not configured, please setup one of relation type[belongsTo,hasOne,hasMany,hasOneTrough,hasManyTrough], At the stage of factory!");
			}
			if($native_value === null && $this->isDefaultNull()){
				return true;
			}
			if($native_value instanceof Record){
				if($this->isMany()){
					return false; // Значение является множественным, что противоречит типу связи [MULTIPLY].
				}
				$schema = $native_value->getSchema();
			}elseif($native_value instanceof Relationship){
				if(!$this->isMany()){
					return false; // Значение является одиночным, что противоречит типу связи {SINGLE}.
				}
				$schema = $native_value->getSchema();
			}else{
				return false; // Значение не может быть NULL
			}
			if($this->referenced_schema !== $schema->getName()){
				return false; // Значение не подходит по схеме.
			}
			return true;
		}

		/**
		 * @param Record $record
		 * @param array $processed
		 * @param array $changed
		 * @return bool|void
		 */
		public function beforeRecordSave(Record $record, array $processed, array $changed = null){
			parent::beforeRecordSave($record,$processed,$changed);
			if(($changed && in_array($this->name,$changed,true)) || (!$changed && $record->isInitializedProperty($this->name))){
				$related = $record->getProperty($this->name);
				switch($this->type){
					case self::TYPE_BELONGS:
						$this->_saveBelongsBefore($record, $related, isset($processed[$this->name])?$processed[$this->name]:null ,$processed,$changed);
						break;
					case self::TYPE_ONE:
						$this->_saveOneBefore($record, $related, isset($processed[$this->name])?$processed[$this->name]:null,$processed,$changed);
						break;
					case self::TYPE_MANY:
						$related->applyPendingOperations();
						$this->_saveManyBefore($record, $related,$processed,$changed);
						break;
					case self::TYPE_MANY_THROUGH:
						$related->applyPendingOperations();
						$this->_saveManyThroughBefore($record, $related,$processed,$changed);
						break;
				}
			}
			return true;
		}


		/**
		 * @param \Jungle\Data\Record $record
		 * @param array $processed
		 * @param array $changed
		 */
		public function afterRecordSave(Record $record, array $processed, array $changed = null){
			if(($changed && in_array($this->name,$changed,true)) || (!$changed && $record->isInitializedProperty($this->name))){
				$related = $record->getProperty($this->name);
				switch($this->type){
					case self::TYPE_BELONGS:
						$this->_saveBelongsAfter($record, $related, isset($processed[$this->name])?$processed[$this->name]:null ,$processed,$changed);
						break;
					case self::TYPE_ONE:
						$this->_saveOneAfter($record, $related, isset($processed[$this->name])?$processed[$this->name]:null,$processed,$changed);
						break;
					case self::TYPE_MANY:
						$this->_saveManyAfter($record, $related,$processed,$changed);
						break;
					case self::TYPE_MANY_THROUGH:
						$this->_saveManyThroughAfter($record, $related,$processed,$changed);
						break;
				}
			}
			parent::afterRecordSave($record,$processed,$changed);
		}



		protected function _handleActionDeleteForThrough(){

		}

		protected function _handleActionDeleteForCollection($action,$virtual,Record $record, Relationship $relationship){
			switch($action){
				case self::ACTION_RESTRICT:
					if($virtual){
						if($relationship->count()){
							throw new \LogicException('Record could not delete, because already use in related records');
						}
					}
					break;
				case self::ACTION_SETNULL:
					if($virtual){
						$relationship->setSyncLevel(Relationship::SYNC_STORE);
					}else{
						$relationship->setSyncLevel(Relationship::SYNC_FULL);
					}
					$relationship->update(
						array_fill_keys(
							$relationship
								->getHolderField()
								->getReferencedFields()
							,null
						)
					);
					$relationship->setSyncLevel();
					break;
				case self::ACTION_CASCADE:
					if($virtual){
						$relationship->setSyncLevel(Relationship::SYNC_STORE);
					}else{
						$relationship->setSyncLevel(Relationship::SYNC_FULL);
					}
					$relationship->remove();
					$relationship->setSyncLevel();
					break;

			}
		}

		/**
		 * @param $action
		 * @param $virtual
		 * @param Record $record
		 * @throws Record\Exception
		 */
		protected function _handleActionDeleteForSingle($action,$virtual, Record $record){
			switch($action){
				case self::ACTION_RESTRICT:
					if($virtual){
						$related = $record->getProperty($this->name);
						if($related){
							throw new \LogicException('Record could not delete, because already use in related records');
						}
					}
					break;
				case self::ACTION_SETNULL:
					$related = $record->getProperty($this->name);
					if($related){
						$collection = $related->getSchema()->getCollection();
						if($virtual){
							$collection->setSyncLevel(Relationship::SYNC_STORE);
						}else{
							$collection->setSyncLevel(Relationship::SYNC_FULL);
						}
						$collection->update(
							array_fill_keys($this->referenced_fields,null),
							[$related->getSchema()->getPrimaryFieldName() => $related->getIdentifierValue()],
							true
						);
						$collection->setSyncLevel();
					}
					break;
				case self::ACTION_CASCADE:
					$related = $record->getProperty($this->name);
					if($related){
						$collection = $related->getSchema()->getCollection();
						if($virtual){
							$collection->setSyncLevel(Relationship::SYNC_STORE);
						}else{
							$collection->setSyncLevel(Relationship::SYNC_FULL);
						}
						$collection->remove([$related->getSchema()->getPrimaryFieldName() => $related->getIdentifierValue()]);
						$collection->setSyncLevel();
					}
					break;

			}
		}


		/** @var null|Relation[]  */
		protected $opposite_relations = null;

		/** @var null|Relation[]  */
		protected $intermediate_relations = null;

		/**
		 * @return Relation[]|null
		 */
		public function getOppositeRelations(){
			if($this->opposite_relations === null){
				$this->opposite_relations = $this->getSchema()->getSchemaManager()->getSchema($this->referenced_schema)->getOppositeRelationsFor($this);
			}
			return $this->opposite_relations;
		}

		/**
		 * @return Relation[]|null
		 */
		public function getIntermediateRelations(){
			if($this->intermediate_relations === null){
				$this->intermediate_relations = $this->getSchema()->getSchemaManager()->getSchema($this->referenced_schema)->getIntermediateRelationsFor($this);
			}
			return $this->intermediate_relations;
		}

		/**
		 * @param \Jungle\Data\Record $record
		 */
		public function beforeRecordRemove(Record $record){

			switch($this->type){
				case self::TYPE_BELONGS:

					// если поле имеет опцию OWNED delete, значит что в случае удаления этого объекта удалиться и тот который связан
					// Дополнительно: Рекомендуется на $this->fields UNIQUE индекса в БД, т.к иначе выставленые в других объектах связи на идентичный объект, удалив его, у всех зависимых от него потребуется выставить поля в NULL, такая схема наврядли будет где-либо применима
					// Дополнительно: Отлично подходит к противоположной - ONE связи, по выше указаному дополнению

					break;
				case self::TYPE_ONE:
					foreach($this->getOppositeRelations() as $field){
						$action = $field->action_delete;
						$virtual = $field->virtual_delete;
						$this->_handleActionDeleteForSingle($action,$virtual,$record);
					}
					break;
				case self::TYPE_MANY:
					foreach($this->getOppositeRelations() as $field){
						$action = $field->action_delete;
						$virtual = $field->virtual_delete;
						$this->_handleActionDeleteForCollection($action,$virtual,$record,$record->getProperty($this->name));

					}
					break;
				case self::TYPE_MANY_THROUGH:
					foreach($this->getIntermediateRelations() as $field){
						$field->_beforeRecordOppositeRemove($record, $this, true);
					}
					// Здесь нужно обработать intermediate записи Удалив их, если FK этого не придусматривают
					// В памяти , требуется просто удалить все загруженые записи intermediate схемы которые находятся в intermediate_registry текущего relationship
					// по запросу в БД , мы можем использовать delete condition = [$this->intermediate_fields = $record->getProperty()]
					// Это же условие используется для содержания intermediate относящихся записей в relationship

					break;
			}

		}

		/**
		 * @param \Jungle\Data\Record $record
		 */
		public function afterRecordRemove(Record $record){

		}

		/**
		 * @param \Jungle\Data\Record $record
		 * @param \Jungle\Data\Record|null $related
		 * @param \Jungle\Data\Record|null $old
		 * @param array $processed
		 * @param array $changed
		 * @throws Record\Exception\Save
		 */
		protected function _saveBelongsBefore(Record $record, Record $related = null, Record $old = null, array $processed = [], array $changed = null){
			if($old){
				// Что же делать со старой записью, обычно её вообще не трогают,
				// Но у неё могут быть выставленны связаные поля на текущий $record
			}

			if($related){
				if(!$related->save()){
					throw new Record\Exception\Save('Related[BELONGS] record "'.$this->name.'" is not can save');
				}else{
					foreach($this->fields as $i => $name){
						$record->setProperty($name, $related->getProperty($this->referenced_fields[$i]));
					}
				}
			}else{
				if($old){
					if(!$old->save()){
						throw new Record\Exception\Save('Detach error Related[BELONGS] record "'.$this->name.'"');
					}
				}
				foreach($this->fields as $i => $name){
					$record->setProperty($name, null);
				}
				$old->save();
			}
		}

		/**
		 * @param Record $record
		 * @param Record|null $related
		 * @param Record|null $old
		 * @param array $processed
		 * @param array $changed
		 */
		protected function _saveBelongsAfter(Record $record, Record $related = null, Record $old = null, array $processed = [], array $changed = null){

		}

		/**
		 * @param Record $record
		 * @param \Jungle\Data\Record|null $related
		 * @param Record|null $old
		 * @param array $processed
		 * @param array $changed
		 */
		protected function _saveOneBefore(Record $record, Record $related = null, Record $old = null, array $processed = [], array $changed = null){

		}

		/**
		 * @param Record $record
		 * @param Record|null $related
		 * @param Record|null $old
		 * @param array $processed
		 * @param array $changed
		 * @throws Record\Exception\Save
		 */
		protected function _saveOneAfter(Record $record, Record $related = null, Record $old = null, array $processed = [], array $changed = null){
			if($old){
				if(!$old->save()){
					throw new Record\Exception\Save('Excluding Related[ONE] record "'.$this->name.'" is not can save');
				}
			}
			if($related){
				foreach($this->referenced_fields as $i => $name){
					$related->setProperty($name, $record->getProperty($this->fields[$i]));
				}
				if(!$related->save()){
					throw new Record\Exception\Save('Related[ONE] record "'.$this->name.'" is not can save');
				}
			}
		}

		/**
		 * @param Record $record
		 * @param Relationship $relationship
		 * @param array $processed
		 * @param array $changed
		 */
		protected function _saveManyBefore(Record $record, Relationship $relationship = null, array $processed = [], array $changed = null){
			$relationship->beforeHolderSave($changed);
		}

		/**
		 * @param Record $record
		 * @param Relationship $relationship
		 * @param array $processed
		 * @param array $changed
		 * @throws Record\Exception\Save
		 */
		protected function _saveManyAfter(Record $record, Relationship $relationship = null, array $processed = [], array $changed = null){
			$relationship->afterHolderSave($changed);
			if($relationship){
				if($items = $relationship->getDirtyAddedItems()){
					foreach($items as $item){
						foreach($this->referenced_fields as $i => $relationFieldName){
							$item->setProperty($relationFieldName, $record->getProperty($this->fields[$i]));
						}
						if(!$item->save()){
							throw new Record\Exception\Save('Related[MANY] records in "'.$this->name.'" is not can save');
						}
					}
				}
				if($items = $relationship->getDirtyRemovedItems()){
					$rSchema = $relationship->getSchema();
					$opposites = $rSchema->getOppositeRelationsFor($this);
					foreach($items as $item){

						if($item->getOperationMade() === Record::OP_CREATE){
							continue;
						}

						$toRemove = false;
						foreach($opposites as $i => $oppositeField){
							if($oppositeField->isNullable()){
								$item->setProperty($oppositeField->getName(), null);
							}else{
								$toRemove = true;
								break;
							}
						}
						if($toRemove){
							if(!$item->remove()){
								throw new Record\Exception\Save('Excluded related[MANY] records in "'.$this->name.'" is not can save');
							}
						}else{
							if(!$item->save()){
								throw new Record\Exception\Save('Excluded Related[MANY] records in "'.$this->name.'" is not can save');
							}
						}

					}
				}
				$relationship->resetDirty();
			}else{



			}


		}

		/**
		 * @param Record $record
		 * @param Relationship $relationship
		 * @param array $processed
		 * @param array $changed
		 * @throws Record\Exception\Save
		 */
		protected function _saveManyThroughBefore(Record $record, Relationship $relationship, array $processed = [], array $changed = null){
			$relationship->beforeHolderSave($changed);
			if($items = $relationship->getDirtyAddedItems()){
				foreach($items as $item){
					if($item->getOperationMade() === Record::OP_CREATE){
						if(!$item->save()){
							throw new Record\Exception\Save('Related[MANY-THROUGH] records in "'.$this->name.'" is not can save');
						}
					}
				}
			}
		}

		/**
		 * @param Record $record
		 * @param Relationship $relationship
		 * @param array $processed
		 * @param array $changed
		 * @throws Record\Exception\Save
		 */
		protected function _saveManyThroughAfter(Record $record, Relationship $relationship, array $processed = [], array $changed = null){
			$relationship->afterHolderSave($changed);
			if($items = $relationship->getDirtyAddedItems()){
				$iSchema = $this->schema->getSchemaManager()->getSchema($this->getIntermediateSchema());
				$iCollection = $iSchema->getCollection();
				/** @var \Jungle\Data\Record $item */
				foreach($items as $item){
					$iRecord = $iSchema->initializeRecord();
					foreach($this->referenced_fields as $i => $relationFieldName){
						$iRecord->setProperty($this->intermediate_referenced_fields[$i], $item->getProperty($relationFieldName));
					}
					foreach($this->fields as $i => $relationFieldName){
						$iRecord->setProperty($this->intermediate_fields[$i], $record->getProperty($relationFieldName));
					}
					if(!$iRecord->save()){
						throw new Record\Exception\Save('Intermediate Link for Related[MANY-THROUGH] record in "'.$this->name.'" is not can save');
					}
					$iRecord->markRecordInitialized();
					$relationship->setIntermediate($item, $iRecord);
					$iCollection->add($iRecord);
				}
			}
			$relationship->resetDirty();
		}


		/**
		 *
		 *
		 * TODO Обновлять Changed Массив, после предварительной обработки Related объектов, т.к значения могут быть измененными изза ForeignKeys
		 *
		 */
	}
}

