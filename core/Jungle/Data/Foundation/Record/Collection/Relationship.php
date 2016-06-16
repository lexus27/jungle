<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.06.2016
 * Time: 20:54
 */
namespace Jungle\Data\Foundation\Record\Collection {

	use Jungle\Data\Foundation\Condition\Condition;
	use Jungle\Data\Foundation\Condition\ConditionComplex;
	use Jungle\Data\Foundation\Condition\ConditionInterface;
	use Jungle\Data\Foundation\Record;
	use Jungle\Data\Foundation\Record\Collection;
	use Jungle\Data\Foundation\Record\Head\Field\Relation;
	use Jungle\Data\Foundation\Record\Head\Schema;

	/**
	 * Class Relationship
	 * @package Jungle\Data\Foundation\Record\Collection
	 */
	class Relationship extends Collection implements \ArrayAccess{

		/** @var  Record */
		protected $holder;

		/** @var  Relation */
		protected $holder_field;

		/** @var array  */
		protected $intermediate_registry = [];

		/** TROUGH беруться из HolderField */
		
		/** @var  Schema|string|null  */
		protected $through_intermediate_schema;

		/** @var  array|ConditionInterface */
		protected $through_intermediate_condition;

		/** @var  array  */
		protected $through_intermediate_collation = [];

		/** @var   */
		protected $through_extended_condition;

		/** @var   */
		protected $through_opposite_relationships = null;

		/** @var   */
		protected $auto_deploy = true;

		/** @var  bool */
		protected $dirty_capturing = true;

		/** @var bool  */
		protected $pending_operations_capture = true;

		/** @var array  */
		protected $pending_operations = [];

		/** @var array  */
		protected $dirty_update_commands = [];

		/** @var  bool  */
		protected $checkpoint = true;

		/**
		 * @throws Exception\Synchronize
		 */
		public function synchronize(){
			$this->dirty_capturing = false;
			parent::synchronize();
			$this->dirty_capturing = true;
		}





		/**
		 * @param Record $holder
		 * @param Relation $field
		 * @return $this
		 */
		public function setHolder(Record $holder, Relation $field){
			if($this->holder !== $holder || $this->holder_field !== $field){
				$this->holder = $holder;
				$this->holder_field = $field;
				if($field->isThrough()){
					$this->through_intermediate_schema = $this->schema->getSchemaManager()->getSchema($field->getIntermediateSchema());
					$this->through_intermediate_collation = array_combine($field->getIntermediateReferencedFields(),$field->getReferencedFields());
				}
				$this->_applyConditions();
			}
			return $this;
		}

		/**
		 * @return Record
		 */
		public function getHolder(){
			return $this->holder;
		}


		/**
		 * @param array|null $changed
		 */
		public function beforeHolderSave(array $changed = null){

		}

		/**
		 * @param array|null $changed
		 */
		public function afterHolderSave(array $changed = null){
			$fields = $this->holder_field->getFields();
			if(!$changed || ($changed && array_intersect_key($changed,$fields))){
				$this->_applyConditions();
			}
		}


		/**
		 *
		 */
		protected function _applyConditions(){
			$fields = $this->holder_field->getFields();
			if($this->holder_field->isThrough()){
				if($this->holder->getOperationMade() !== Record::OP_CREATE){
					$condition = [];
					foreach($this->holder_field->getIntermediateFields() as $i => $name){
						$condition[] = [ $name, '=', $this->holder->getProperty($fields[$i]) ];
					}
					$this->setThroughCondition($condition);
				}
				$this->setContainCondition($this->holder_field->getReferencedCondition());
			}else{
				if($this->holder->getOperationMade() !== Record::OP_CREATE){
					$condition = [];
					foreach($this->holder_field->getReferencedFields() as $i => $name){
						$condition[] = [ $name, '=', $this->holder->getProperty($fields[$i]) ];
					}
					$condition = array_merge($condition, (array) $this->holder_field->getReferencedCondition());
				}else{
					$condition = array_merge((array) $this->holder_field->getReferencedCondition());
				}
				$this->setContainCondition($condition);
			}

		}

		/**
		 * @param $condition
		 * @return $this
		 */
		public function setThroughCondition($condition){
			$condition = Condition::build($condition);
			if($this->through_intermediate_condition !== $condition){
				$this->through_intermediate_condition = Condition::build($condition);
				$this->through_extended_condition = null;
			}
			return $this;
		}

		/**
		 *
		 */
		protected function _resetExtendedThroughCondition(){
			$this->through_extended_condition = null;
			foreach($this->descendants as $collection){
				if($collection instanceof self){
					$collection->_resetExtendedThroughCondition();
				}
			}
		}

		/**
		 * @return Relation
		 */
		public function getHolderField(){
			return $this->holder_field;
		}

		/**
		 * @return bool
		 */
		public function isThrough(){
			if(!$this->through_intermediate_schema){
				return $this->ancestor instanceof Relationship?$this->ancestor->isThrough():false;
			}else{
				return true;
			}
		}


		/**
		 * @Improve TROUGH Condition Это Intermediate
		 * 
		 * @param null $appendCondition
		 * @return ConditionComplex|null
		 */
		public function getExtendedThroughCondition($appendCondition = null){
			if($this->through_extended_condition === null){
				$conditions = [];
				if($appendCondition){
					$conditions[] = Condition::build($appendCondition);
				}
				$o = $this;
				do{
					if($o instanceof Relationship && $o->isThrough()){
						if($o->through_intermediate_schema && $o->through_intermediate_condition){
							$conditions[] = $o->through_intermediate_condition;
						}
					}else{
						break;
					}
				}while(($o = $o->ancestor));
				$extended = null;

				if(!$conditions){
					$extended = false;
				}else if(count($conditions) > 1){
					$extended = $this->_appendCondition(null,$conditions,true);
				}else{
					$extended = $conditions[0];
				}
				$this->through_extended_condition = $extended;
			}
			return $this->through_extended_condition;
		}


		/**
		 * @param Record $item
		 * @return Record|null
		 */
		public function getIntermediate(Record $item){
			$id = $item->getIdentifierValue();
			return isset($this->intermediate_registry[$id])? $this->intermediate_registry[$id]:null;
		}

		/**
		 * @param Record $item
		 * @param Record $intermediate
		 * @return $this
		 */
		public function setIntermediate(Record $item, Record $intermediate){
			$this->intermediate_registry[$item->getIdentifierValue()] = $intermediate;
			return $this;
		}




		/**
		 * @param Record $item
		 * @param Record $intermediateRecord
		 * @return bool|void
		 */
		public function add($item,Record $intermediateRecord = null){
//			if($this->auto_deploy && !$this->deployed){
//				$this->deployed = true;
//				$this->deploy();
//			}
			if(parent::add($item)){
				if($item->getOperationMade() === $item::OP_UPDATE && $intermediateRecord){
					$this->intermediate_registry[$item->getIdentifierValue()] = $intermediateRecord;
				}
				return true;
			}
			return false;
		}


		protected static $special_record_call_function = null;
		protected static $special_record_get_function = null;
		protected static $special_record_set_function = null;
		protected static $special_record_static_get_function = null;
		protected static $special_record_static_set_function = null;

		/**
		 * @return \Closure|null
		 */
		protected function _getSpecialRecordCallFunction(){
			if(self::$special_record_call_function===null){
				self::$special_record_call_function = \Closure::bind(function($record,$method,$arguments){
					return call_user_func_array([$record,$method],$arguments);
				},null,Record::class);
			}
			return self::$special_record_call_function;
		}

		/**
		 * @return \Closure|null
		 */
		protected function _getSpecialRecordStaticGetFunction(){
			if(self::$special_record_static_get_function===null){
				self::$special_record_static_get_function = \Closure::bind(function($record,$property){
					return $record::${$property};
				},null,Record::class);
			}
			return self::$special_record_static_get_function;
		}

		/**
		 * @return \Closure|null
		 */
		protected function _getSpecialRecordStaticSetFunction(){
			if(self::$special_record_static_set_function===null){
				self::$special_record_static_set_function = \Closure::bind(function($record,$property,$value){
					$record::${$property} = $value;
				},null,Record::class);
			}
			return self::$special_record_static_set_function;
		}

		/**
		 * @return \Closure|null
		 */
		protected function _getSpecialRecordGetFunction(){
			if(self::$special_record_get_function===null){
				self::$special_record_get_function = \Closure::bind(function($record,$property){
					return $record->{$property};
				},null,Record::class);
			}
			return self::$special_record_get_function;
		}

		/**
		 * @return \Closure|null
		 */
		protected function _getSpecialRecordSetFunction(){
			if(self::$special_record_static_set_function===null){
				self::$special_record_static_set_function = \Closure::bind(function($record,$property,$value){
					$record->{$property} = $value;
				},null,Record::class);
			}
			return self::$special_record_static_set_function;
		}

		/**
		 * @param Record $record
		 */
		protected function _afterItemAdd($record){
			if(!$this->holder_field->isThrough()){
				$fn = $this->_getSpecialRecordSetFunction();
				foreach($this->holder_field->getOppositeRelations() as $field){
					$fn($record,'_set_property_relation_applied_in_new', true);
					$record->setProperty($field->getName(),$this->holder); // Выставлен OPPOSITE
				}
			}else{
				/**
				 *
				 * Текущая схема является схемой REFERENCED
				 * Схема Holder Является СХЕМОЙ ИМЕЮЩЕЙ Many Связь с текущей
				 *
				 */
				/** @var Relation $field */
				foreach($this->holder_field->getOppositeRelations() as $field){
					$relationship = $record->getProperty($field->getName());
					if(!in_array($this->holder,$relationship->items,true)){
						$relationship->items[] = $this->holder;
						$relationship->_afterItemAdd($this->holder);
					}
				}

			}
			parent::_afterItemAdd($record);
		}

		/**
		 * @param null $condition
		 * @return $this|int
		 */
		public function devastate($condition = null){
			return parent::remove($condition);
		}

		/**
		 * @param null $condition
		 * @return $this|int
		 */
		public function remove($condition = null){
			if($this->isThrough()){
				if(!is_array($condition) && $condition!==null){
					$condition = [$this->schema->getPrimaryFieldName() => $condition];
				}
				$level = $this->getSyncLevel();
				if($level === self::SYNC_STORE){
					$extended = $this->getExtendedContainCondition($condition);
					$iCondition = $this->getExtendedThroughCondition();
					$iSchema = $this->schema->getSchemaManager()->getSchema($this->through_intermediate_schema);
					$affected = $iSchema->storageRemoveThrough($iCondition->toStorageCondition(), $this->schema,$extended->toStorageCondition(),array_flip($this->through_intermediate_collation));
					if($affected){
						$this->getCheckpoint()->_remove($extended);
						return $affected;
					}
				}else{
					if($this->pending_operations_capture){
						$this->pending_operations[] = function() use($condition){
							$extended = $this->getExtendedContainCondition($condition);
							$iCondition = $this->getExtendedThroughCondition();
							$iSchema = $this->schema->getSchemaManager()->getSchema($this->through_intermediate_schema);
							$affected = $iSchema->storageRemoveThrough($iCondition?$iCondition->toStorageCondition():null, $this->schema,$extended?$extended->toStorageCondition():null,array_flip($this->through_intermediate_collation));
							return $affected;
						};
					}
					$this->getCheckpoint()->_remove($condition);
				}

			}else{
				if(!is_array($condition) && $condition!==null){
					$condition = [$this->schema->getPrimaryFieldName() => $condition];
				}
				$level = $this->getSyncLevel();
				if($level === self::SYNC_STORE){
					$extended = $this->getExtendedContainCondition($condition);
					$affected = $this->schema->storageRemove($extended->toStorageCondition());
					if($affected){
						$this->getRoot()->_remove($extended);
						return $affected;
					}
				}elseif($level === self::SYNC_FULL){
					$this->getRoot()->_remove($this->getExtendedContainCondition($condition));
				}else{
					if($this->pending_operations_capture){
						$this->pending_operations[] = function() use($condition){
							$extended = $this->getExtendedContainCondition($condition);
							$affected = $this->schema->storageRemove($extended->toStorageCondition());
							return $affected;
						};
					}
					$this->getCheckpoint()->_remove($condition);
				}
			}
			return $this;
		}

		/**
		 *
		 */
		public function applyPendingOperations(){
			foreach($this->pending_operations as $command){
				$command();
			}
		}

		public function isDirty(){
			return parent::isDirty() || $this->pending_operations;
		}

		/**
		 *
		 */
		public function resetDirty(){
			/** @var Record $record */
			$added = $this->dirty_added;
			$removed = $this->dirty_removed;
			parent::resetDirty();
			if($this->isThrough() && ($added || $removed)){
				$opposites = $this->holder_field->getOppositeRelations();
				foreach($added as $record){
					foreach($opposites as $field){
						$relationship = $record->getProperty($field->getName());
						$relationship->dirty_added = [];
					}
				}
				foreach($removed as $record){
					foreach($opposites as $field){
						$relationship = $record->getProperty($field->getName());
						$relationship->dirty_removed = [];
					}
				}
			}
			$this->pending_operations = [];
		}

		public function removeItem(Record $item){
			parent::removeItem($item);
		}

		/**
		 * @param Record $record
		 * @throws Record\Exception
		 */
		protected function _afterItemRemove($record){
			if(!$this->holder_field->isThrough()){
				foreach($this->holder_field->getOppositeRelations() as $field){
					$record->setProperty($field->getName(),null); // Выставлен OPPOSITE
				}
			}else{
				if($record->getOperationMade() !== Record::OP_CREATE){
					unset($this->intermediate_registry[$record->getIdentifierValue()]);
				}
				/**
				 *
				 * Текущая схема является схемой REFERENCED
				 * Схема Holder Является СХЕМОЙ ИМЕЮЩЕЙ Many Связь с текущей
				 *
				 */

				foreach($this->holder_field->getOppositeRelations() as $field){
					$fieldName = $field->getName();
					if($record->isInitializedProperty($fieldName)){
						$relationship = $record->getProperty($fieldName);
						$relationship->_removeItem($this->holder);
					}
				}

			}
			parent::_afterItemRemove($record); // TODO: Change the autogenerated stub
		}


		/**
		 * @param null $condition
		 * @param null $limit
		 * @param null $offset
		 * @param null $orderBy
		 * @return $this
		 */
		protected function _deploy($condition = null, $limit = null, $offset = null, $orderBy = null){
			if($this->isThrough()){
				/**
				 * @var Schema $iSchema
				 * @var array $iCondition
				 * @var array $iCollation
				 */
				$iSchema     = $this->schema->getSchemaManager()->getSchema($this->through_intermediate_schema);
				$iCondition  = $this->through_intermediate_condition?$this->through_intermediate_condition->toStorageCondition():null;
				$iCollation  = $this->through_intermediate_collation;
				$prefix = uniqid('i').'_';
				$prefix_length = strlen($prefix);
				$shipment = $this->schema->storageLoadThrough($condition,
					$iSchema,$iCondition,$iCollation,
					$limit?:$this->getLimit(),$this->getOffset() + $offset,$orderBy,true,$prefix
				);
				if($shipment->count()){
					$iCollection = $iSchema->getCollection();
					$iCollection->saturate(self::SAT_LOAD,$iCollection);
					while(($item = $shipment->asAssoc()->fetch())!==false){
						$iItem = [];
						foreach($item as $key => $value){
							if(substr($key,0,$prefix_length)===$prefix){
								$iItem[substr($key,$prefix_length)] = $value;
								unset($item[$key]);
							}
						}
						$iRecord = $iSchema->initializeRecord($iItem);
						$iCollection->add($iRecord);

						$record = $this->schema->initializeRecord($item);
						$this->add($record,$iRecord);
					}
				}
			}else{
				parent::_deploy($condition, $limit, $offset, $orderBy);
			}
			return $this;
		}

		/**
		 * @return string
		 */
		public function getInformationIdentifier(){
			return $this->getSchema()->getName() . "[RELATIONSHIP] \r\nOwned in: \r\n\t" . $this->holder->getSchema()->getName() . '[' . ($this->holder->getIdentifierValue()?:'NEW') . "] by \"" . $this->holder_field->getName() . '"' . ($this->through_intermediate_schema? "\r\ntrough: \r\n\t" . ($this->through_intermediate_schema->getName()):'');
		}

		/**
		 * @param mixed $offset
		 * @return bool
		 */
		public function offsetExists($offset){
			return $this->has($offset);
		}

		/**
		 * @param mixed $offset
		 * @return Record
		 */
		public function offsetGet($offset){
			return $this->get($offset);
		}

		/**
		 * @param mixed $offset
		 * @param mixed $value
		 * @throws Exception
		 */
		public function offsetSet($offset, $value){
			if($offset === null){
				if(!$value instanceof Record){
					throw new Exception('Wrong collection item type add');
				}
				$this->add($value);
			}
		}

		/**
		 * @param mixed $offset
		 */
		public function offsetUnset($offset){
			$faceProperty = $this->face_access_property;
			if(!$faceProperty){
				foreach($this->items as $item){
					if($item->getIdentifierValue() === $offset){
						$this->removeItem($item);
					}
				}
			}else{
				foreach($this->items as $item){
					if($item->getProperty($faceProperty) === $offset){
						$this->removeItem($item);
					}
				}
			}


		}
	}
}

