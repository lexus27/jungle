<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.06.2016
 * Time: 20:54
 */
namespace Jungle\Data\Record\Collection {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Collection;
	use Jungle\Data\Record\Collection\Exception\Synchronize;
	use Jungle\Data\Record\Head\Field\Relation;
	use Jungle\Data\Record\Head\Schema;
	use Jungle\Exception;
	use Jungle\Util\Data\Condition\Condition;
	use Jungle\Util\Data\Condition\ConditionComplex;
	use Jungle\Util\Data\Condition\ConditionInterface;

	/**
	 * Class Relationship
	 * @package Jungle\Data\Record\Collection
	 */
	class Relationship extends Collection implements \ArrayAccess{

		/** @var  \Jungle\Data\Record */
		protected $holder;

		/** @var  Relation */
		protected $holder_field;

		/** @var  Schema|string|null  */
		protected $intermediate_schema;

		/** @var  array */
		protected $intermediate_registry = [];

		/** @var  array */
		protected $intermediate_collation = [];

		/** @var  array|ConditionInterface */
		protected $intermediate_condition;

		/** @var  array|ConditionInterface */
		protected $intermediate_extended_condition;

		/** @var bool  */
		protected $auto_sort = true;

		/** @var  bool */
		protected $auto_deploy = true;

		/** @var  bool */
		protected $dirty_capturing = true;

		/** @var  bool */
		protected $pending_operations_capturing = true;

		/** @var  \Closure[] */
		protected $pending_operations = [];

		/** @var  bool  */
		protected $as_checkpoint = true;

		/**
		 * @throws Synchronize
		 */
		public function synchronize(){
			$this->dirty_capturing = false;
			parent::synchronize();
			$this->dirty_capturing = true;
		}


		public function __clone(){
			parent::__clone();
			$this->intermediate_condition = null;
			$this->intermediate_extended_condition = null;
		}


		/**
		 * @param \Jungle\Data\Record $holder
		 * @param Relation $field
		 * @return $this
		 */
		public function setHolder(Record $holder, Relation $field){
			if($this->holder !== $holder || $this->holder_field !== $field){
				$this->holder = $holder;
				$this->holder_field = $field;
				if($field->isThrough()){
					$this->intermediate_schema = $this->schema->getSchemaManager()->getSchema($field->getIntermediateSchema());
					$this->intermediate_collation = array_combine($field->getIntermediateReferencedFields(),$field->getReferencedFields());
				}
				$this->_applyConditions();
			}
			return $this;
		}

		/**
		 * @return \Jungle\Data\Record
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

			if($this->holder_field->isDynamic()){
				if($this->holder->getOperationMade() !== Record::OP_CREATE){
					$condition = [];
					foreach($this->holder_field->getReferencedFields() as $i => $name){
						$condition[] = [ $name, '=', $this->holder->getProperty($fields[$i]) ];
					}
					$condition = array_merge($condition, (array) $this->holder_field->getReferencedCondition());
				}else{
					$condition = (array) $this->holder_field->getReferencedCondition();
				}
				$condition[] = [
					$this->holder_field->getDynamicReferencedSchemafield(), '=', $this->holder->getSchema()->getName()
				];
				$this->setContainCondition($condition);
				return;
			}


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
					$condition = $this->holder_field->getReferencedCondition();
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
			if($this->intermediate_condition !== $condition){
				$this->intermediate_condition = Condition::build($condition);
				$this->intermediate_extended_condition = null;
			}
			return $this;
		}

		/**
		 *
		 */
		protected function _resetExtendedThroughCondition(){
			$this->intermediate_extended_condition = null;
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
			if(!$this->intermediate_schema){
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
			if($this->intermediate_extended_condition === null){
				$conditions = [];
				if($appendCondition){
					$conditions[] = Condition::build($appendCondition);
				}
				$o = $this;
				do{
					if($o instanceof Relationship && $o->isThrough()){
						if($o->intermediate_schema && $o->intermediate_condition){
							$conditions[] = $o->intermediate_condition;
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
				$this->intermediate_extended_condition = $extended;
			}
			return $this->intermediate_extended_condition;
		}


		/**
		 * @param \Jungle\Data\Record $item
		 * @return \Jungle\Data\Record|null
		 */
		public function getIntermediate(Record $item){
			$id = $item->getIdentifierValue();
			return isset($this->intermediate_registry[$id])? $this->intermediate_registry[$id]:null;
		}

		/**
		 * @param \Jungle\Data\Record $item
		 * @param \Jungle\Data\Record $intermediate
		 * @return $this
		 */
		public function setIntermediate(Record $item, Record $intermediate){
			$this->intermediate_registry[$item->getIdentifierValue()] = $intermediate;
			return $this;
		}




		/**
		 * @param \Jungle\Data\Record $item
		 * @param \Jungle\Data\Record $intermediateRecord
		 * @return bool|void
		 */
		public function add($item,Record $intermediateRecord = null){
			if(parent::add($item)){
				if($item->getOperationMade() === $item::OP_UPDATE && $intermediateRecord){
					$this->intermediate_registry[$item->getIdentifierValue()] = $intermediateRecord;
				}
				return true;
			}
			return false;
		}


		/**
		 * @param \Jungle\Data\Record $record
		 */
		protected function _afterItemAdd($record){
			if(!$this->holder_field->isThrough()){
				foreach($this->holder_field->getOppositeRelations($record) as $field){
					$record->setProperty($field->getName(),$this->holder,false,true); // Выставлен OPPOSITE
				}
			}else{
				/**
				 *
				 * Текущая схема является схемой REFERENCED
				 * Схема Holder Является СХЕМОЙ ИМЕЮЩЕЙ Many Связь с текущей
				 *
				 */
				/** @var Relation $field */
				foreach($this->holder_field->getOppositeRelations($record) as $field){
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
					$iSchema = $this->schema->getSchemaManager()->getSchema($this->intermediate_schema);
					$affected = $iSchema->storageRemoveThrough($iCondition->toStorageCondition(), $this->schema,$extended->toStorageCondition(),array_flip($this->intermediate_collation));
					if($affected){
						$this->getCheckpoint()->_remove($extended);
						return $affected;
					}
				}else{
					$this->addPendingOperation(function() use($condition){
						$extended = $this->getExtendedContainCondition($condition);
						$iCondition = $this->getExtendedThroughCondition();
						$iSchema = $this->schema->getSchemaManager()->getSchema($this->intermediate_schema);
						$affected = $iSchema->storageRemoveThrough($iCondition?$iCondition->toStorageCondition():null, $this->schema,$extended?$extended->toStorageCondition():null,array_flip($this->intermediate_collation));
						return $affected;
					});
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
					$this->addPendingOperation(function() use($condition){
						$extended = $this->getExtendedContainCondition($condition);
						$affected = $this->schema->storageRemove($extended->toStorageCondition());
						return $affected;
					});
					$this->getCheckpoint()->_remove($condition);
				}
			}
			return $this;
		}

		/**
		 * @param callable $operation
		 * @return bool
		 */
		public function addPendingOperation(callable $operation){
			if($this->pending_operations_capturing){
				$this->pending_operations[] = $operation;
				return true;
			}
			return false;
		}

		/**
		 * @param callable $operation
		 * @return $this
		 */
		public function removePendingOperation($operation){
			$i = array_search($operation,$this->pending_operations, true);
			if($i !== false){
				array_splice($this->pending_operations,$i,1);
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

		/**
		 * @return bool
		 */
		public function isDirty(){
			return parent::isDirty() || $this->pending_operations;
		}

		/**
		 * Применяется после обработки Dirty стеков, обычно во время сохранения объекта в классе поля Relation
		 */
		public function resetDirty(){
			/** @var \Jungle\Data\Record $record */
			$added = $this->dirty_added;
			$removed = $this->dirty_removed;
			parent::resetDirty();
			if($this->isThrough() && ($added || $removed)){
				$opposites = $this->holder_field->getOppositeRelations($record);
				foreach($added as $record){
					foreach($opposites as $field){
						$name = $field->getName();
						if($record->isInitializedProperty($name)){
							$relationship = $record->getProperty($name);
							if(($i = array_search($this->holder,$relationship->dirty_added))!==false){
								array_splice($relationship->dirty_added,$i,1);
							}
						}
					}
				}
				foreach($removed as $record){
					foreach($opposites as $field){
						$name = $field->getName();
						if($record->isInitializedProperty($name)){
							$relationship = $record->getProperty($name);
							if(($i = array_search($this->holder, $relationship->dirty_removed)) !== false){
								array_splice($relationship->dirty_removed, $i, 1);
							}
						}
					}
				}
			}
			$this->pending_operations = [];
		}

		/**
		 * @param \Jungle\Data\Record $record
		 * @throws Record\Exception
		 */
		protected function _afterItemRemove($record){
			if(!$this->holder_field->isThrough()){
				foreach($this->holder_field->getOppositeRelations($record) as $field){
					$record->setProperty($field->getName(),null); // Выставлен OPPOSITE
				}
			}else{
				if($record->getOperationMade() !== Record::OP_CREATE){
					unset($this->intermediate_registry[$record->getIdentifierValue()]);
				}
				foreach($this->holder_field->getOppositeRelations($record) as $field){
					$fieldName = $field->getName();
					if($record->isInitializedProperty($fieldName)){
						$relationship = $record->getProperty($fieldName);
						$relationship->_removeItem($this->holder);
					}
				}
			}
			parent::_afterItemRemove($record);
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
				$iSchema     = $this->schema->getSchemaManager()->getSchema($this->intermediate_schema);
				$iCondition  = $this->intermediate_condition?$this->intermediate_condition->toStorageCondition():null;
				$iCollation  = $this->intermediate_collation;
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
						// FIXME already loaded intermediate record check
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
			return $this->getSchema()->getName() . "[RELATIONSHIP] \r\nOwned in: \r\n\t" . $this->holder->getSchema()->getName() . '[' . ($this->holder->getIdentifierValue()?:'NEW') . "] by \"" . $this->holder_field->getName() . '"' . ($this->intermediate_schema? "\r\ntrough: \r\n\t" . ($this->intermediate_schema->getName()):'');
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
		 * @return \Jungle\Data\Record
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
			}else{
				throw new Exception('\ArrayAccess::offsetSet to Relationship, support only append(example: $relationship[] = $item;) without pass index');
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

