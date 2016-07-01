<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 20:54
 */
namespace Jungle\Data {

	use Jungle\Data\Record\Collection\Relationship;
	use Jungle\Data\Record\Exception;
	use Jungle\Data\Record\ExportableInterface;
	use Jungle\Data\Record\Head\Field;
	use Jungle\Data\Record\Head\Field\Relation;
	use Jungle\Data\Record\Head\Field\Virtual;
	use Jungle\Data\Record\Head\Schema;
	use Jungle\Data\Record\Properties\PropertyRegistryInterface;
	use Jungle\Data\Record\Properties\PropertyRegistryTransientInterface;
	use Jungle\Util\Data\Foundation\Schema\OuterInteraction\SchemaAwareInterface;
	use Jungle\Util\Data\Foundation\Storage;
	
	/**
	 * Class Record
	 * @package modelX
	 */
	abstract class Record
		implements PropertyRegistryInterface,
		PropertyRegistryTransientInterface,
		ExportableInterface,
		\Iterator,
		\ArrayAccess,
		\Serializable,
		\JsonSerializable,
		SchemaAwareInterface{

		const OP_CREATE = 1;

		const OP_UPDATE = 2;

		const OP_DELETE = 3;
		
		/** @var   */
		protected static $instantiatedRecordsCount = 0;
		
		
		/** @var  int */
		private $_property_iterator_index = 0;
		
		/** @var  int */
		private $_property_iterator_count = 0;
		
		
		
		/** @var bool  */
		protected $_set_property_dirty_applied = false;
		
		/** @var bool  */
		protected $_set_property_relation_applied_in_new = false;
		
		/** @var bool  */
		protected $_set_property_relation_applied_in_old = false;
		
		/** @var bool  */
		protected static $properties_changes_restrict = true;

		

		

		/** @var  int */
		protected $_internalIdentifier;

		/** @var bool */
		protected $_initialized = false;

		

		
		
		
		/** @var  \Jungle\Data\Record\Head\Schema */
		protected $_schema;
		
		/** @var  int */
		protected $_operation_made = self::OP_CREATE;
		
		/** @var bool */
		protected $_operation_processing = false;
		
		/** @var  mixed */
		protected $_original;

		/** @var  array */
		protected $_processed = [];


		/**
		 * Record constructor.
		 */
		public function __construct(){
			$this->_internalIdentifier = ++self::$instantiatedRecordsCount;
		}



		/**
		 * @param $operationMade
		 * @return $this
		 */
		public function setOperationMade($operationMade){
			if($this->_initialized){
				throw new \LogicException('Record is already initialized!');
			}
			$this->_operation_made = $operationMade;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getOperationMade(){
			return $this->_operation_made;
		}

		/**
		 * @param $original
		 * @return $this
		 */
		public function setOriginalData($original){
			if($this->_initialized){
				throw new \LogicException('Record is already initialized!');
			}
			if($this->_original !== $original){
				$this->_original = $original;
				$this->_afterOriginalDataChanged();
			}
			return $this;
		}

		/**
		 *
		 */
		protected function _afterOriginalDataChanged(){
			$this->_resetAll();
		}



		/**
		 * @return $this
		 */
		public function markRecordInitialized(){
			if(!$this->_initialized){
				$this->_initialized = true;
				$this->_schema->markInitialized($this);
				if($this->_operation_made === self::OP_UPDATE){
					$this->afterFetch();
				}
				$this->onRecordReady();
			}
			return $this;
		}

		/**
		 * @return $this
		 */
		public function toFlyweight(){
			$this->_initialized = false;
			return $this;
		}


		/**
		 * @return mixed
		 */
		public function getOriginalData(){
			return $this->_original;
		}



		/**
		 * @return int
		 */
		public static function getStatusInstantiatedRecordsCount(){
			return self::$instantiatedRecordsCount;
		}

		/**
		 * @return mixed
		 */
		public function getIdentifierValue(){
			return $this->_getFrontProperty($this->getSchema()->getPrimaryFieldName());
		}


		/**
		 * @param Schema $schema
		 * @return $this
		 */
		public function setSchema(Schema $schema){
			$this->_schema = $schema;
			return $this;
		}

		/**
		 * @return Schema
		 */
		public function getSchema(){
			return $this->_schema;
		}

		/**
		 * @Complex-Triggered
		 * @param array $data
		 * @param null|string[]|string|int[]|int $whiteList
		 * @param null|string[]|string|int[]|int $blackList
		 * @return $this
		 */
		public function assign(array $data, $whiteList = null, $blackList = null){
			$attributes = $this->_schema->getFieldNames();
			if($whiteList !== null){
				if(!is_array($whiteList)){
					if(!is_numeric($whiteList) || !is_string($whiteList)){
						throw new \InvalidArgumentException('White list allow value types: array or string or numeric');
					}
					$whiteList = [ $whiteList ];
				}
				$attributes = array_intersect($attributes, $whiteList);
			}
			if($blackList !== null){
				if(!is_array($blackList)){
					if(!is_numeric($blackList) || !is_string($blackList)){
						throw new \InvalidArgumentException('White list allow value types: array or string or numeric');
					}
					$blackList = [ $blackList ];
				}
				$attributes = array_diff($attributes, $blackList);
			}

			if(self::$properties_changes_restrict){
				if($readOnly = $this->_schema->getReadonlyNames()){
					$attributes = array_diff($attributes, $readOnly);
				}
				if($privates = $this->_schema->getPrivateNames()){
					$attributes = array_diff($attributes, $privates);
				}
			}

			foreach($attributes as $key){
				if(array_key_exists($key, $data)){
					$this->setProperty($key,$data[$key]);
				}
			}
			return $this;
		}

		/**
		 * @param $key
		 * @param $value
		 * @return $this
		 * @throws \Exception
		 */
		public function setProperty($key, $value){
			try{
				$field = $this->_schema->getField($key);
				if($field){
					if(self::$properties_changes_restrict){
						if($field->isReadonly()){
							throw new Exception('Could not set readonly "'.$key.'" property');
						}
						if($field->isPrivate()){
							throw new Exception('Could not set private "'.$key.'" property');
						}
					}
					if($field instanceof Relation){
						if(!$field->isMany()){
							if($this->isInitializedProperty($key)){
								$old = $this->_getFrontProperty($key);
								if($value !== $old){
									$this->_setFrontProperty($key, $value);
									/** @var Relation[] $opposites */
									if((!$this->_set_property_relation_applied_in_old && $old) || (!$this->_set_property_relation_applied_in_new && $value)){
										$opposites = $field->getOppositeRelations();
									}
									if(!$this->_set_property_relation_applied_in_old && $old instanceof Record){
										foreach($opposites as $f){
											if($f->isMany()){
												$relationship = $old->getProperty($f->getName());
												$relationship->removeItem($this);
											}else{
												$old->_set_property_relation_applied_in_new = true;
												$old->_set_property_relation_applied_in_old = true;
												$old->setProperty($f->getName(), null);
											}
										}
									}

									if(!$this->_set_property_relation_applied_in_new && $value instanceof Record){
										foreach($opposites as $f){
											if($f->isMany()){
												$relationship = $value->getProperty($f->getName());
												$relationship->add($this);
											}else{
												$value->_set_property_relation_applied_in_new = true;
												$value->_set_property_relation_applied_in_old = true;
												$value->setProperty($f->getName(), $this);
											}
										}
									}

								}
							}else{
								$this->_setFrontProperty($key, $value);
							}


							if(!$value && $field->isBelongs()){

								foreach($field->getFields() as $f){
									$this->setProperty($f,null);
								}

							}

						}else{
							if($value === null || (is_array($value) && empty($value))){
								$relationship = $this->_getFrontProperty($key);
								$relationship->remove();
							}else{
								throw new Exception('Set property "'.$key.'" positive value is forbidden, but you can detach all by pass null or empty array');
							}
						}
					}else{
						$this->_setFrontProperty($key, $value);
					}

					if($this->_set_property_dirty_applied){
						$this->_processed[$key] = $value;
						$this->_original = $this->_schema->valueAccessSet($this->_original,$key, $value);
						$this->_set_property_dirty_applied = false;
					}
				}else{
					throw new \LogicException('Set Field "'.$key.'" not exists in data map schema["'.$this->_schema->getName().'"]');
				}
				return $this;
			}finally{
				$this->_set_property_relation_applied_in_new = false;
				$this->_set_property_relation_applied_in_old = false;
			}
		}

		/**
		 * @param $key
		 * @return bool
		 */
		public function hasProperty($key){
			return !!$this->_schema->getField($key);
		}

		/**
		 * @param $key
		 * @return Record|Record[]|Relationship|mixed
		 * @throws Exception
		 */
		public function getProperty($key){
			$field = $this->_schema->getField($key);
			if($field){
				if(self::$properties_changes_restrict && $field->isPrivate()){
					throw new Exception('Could not get private property');
				}
				return $this->_getFrontProperty($key);
			}else{
				throw new \LogicException('Get Field "'.$key.'" not exists in data map schema["'.$this->_schema->getName().'"]');
			}
		}

		/**
		 * @return string
		 */
		public function getSource(){
			return $this->_schema->getSource();
		}

		/**
		 * @return string
		 */
		public function getWriteSource(){
			return $this->getSource();
		}

		/**
		 * @return Storage|string
		 */
		public function getStorage(){
			return $this->_schema->getStorage();
		}

		/**
		 * @return Storage|string
		 */
		public function getWriteStorage(){
			return $this->_schema->getWriteStorage();
		}

		/**
		 * @param $name
		 * @return mixed|Relationship|Record|null
		 */
		public function __get($name){
			return $this->getProperty($name);
		}

		/**
		 * @param $name
		 * @param $value
		 */
		public function __set($name, $value){
			$this->setProperty($name, $value);
		}

		/**
		 * @param $name
		 * @return bool
		 */
		public function __isset($name){
			return $this->hasProperty($name);
		}

		/**
		 * @param $name
		 */
		public function __unset($name){
			$this->resetPropertyDefault($name);
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function resetPropertyDefault($name){
			$field = $this->_schema->getField($name);
			if($field){
				$default = $field->getDefault();
				if(($default !== null) || ($default === null && $field->isNullable())){
					return $this->setProperty($name, $default);
				}else{
					throw new \LogicException('Property "' . $name . '" no have default value');
				}
			}
			return $this;
		}

		/**
		 * @param $name
		 * @return bool
		 */
		abstract public function isInitializedProperty($name);

		/**
		 * @inheritDoc
		 */
		public function offsetExists($offset){
			return $this->hasProperty($offset);
		}

		/**
		 * @inheritDoc
		 */
		public function offsetGet($offset){
			return $this->getProperty($offset);
		}

		/**
		 * @inheritDoc
		 */
		public function offsetSet($offset, $value){
			$this->setProperty($offset, $value);
		}

		/**
		 * @inheritDoc
		 */
		public function offsetUnset($offset){
			$this->resetPropertyDefault($offset);
		}


		/**
		 * @return mixed|null|Relationship|Record
		 */
		public function current(){
			$names = $this->_schema->getEnumerableNames();
			return $this->getProperty($names[$this->_property_iterator_index]);
		}

		/**
		 * @inheritDoc
		 */
		public function next(){
			$this->_property_iterator_index++;
		}

		/**
		 * @return string
		 */
		public function key(){
			$names = $this->_schema->getEnumerableNames();
			return $names[$this->_property_iterator_index];
		}

		/**
		 * @return bool
		 */
		public function valid(){
			return $this->_property_iterator_index < $this->_property_iterator_count;
		}

		/**
		 * @inheritDoc
		 */
		public function rewind(){
			$this->_property_iterator_index = 0;
			$this->_property_iterator_count = count($this->_schema->getEnumerableNames());
		}

		/**
		 * @return array
		 */
		public function export(){
			$values = [ ];
			foreach($this->_schema->getFieldNames() as $name){
				$values[$name] = $this->getProperty($name);
			}
			return $values;
		}

		/**
		 * Актуализация данных
		 */
		public function refresh(){
			$item = $this->_schema->storageLoadById($this->getIdentifierValue());
			if($item !== $this->_original){
				$this->_original = $item;
				$this->_afterOriginalDataChanged();
				$this->onRecordReady();
			}
			return $this;
		}

		/**
		 * @param null $fieldName
		 * @return mixed
		 */
		abstract public function reset($fieldName = null);


		/**
		 * @param null $fieldName
		 */
		abstract protected function _resetAll($fieldName = null);


		/**
		 * @return string
		 */
		public function serialize(){
			return serialize($this->export());
		}

		/**
		 * @param string $serialized
		 */
		public function unserialize($serialized){
			$serialized = unserialize($serialized);
			foreach($serialized as $key => $value){
				$this->setProperty($key,$value);
			}
		}

		/**
		 * @return array
		 */
		public function jsonSerialize(){
			return $this->export();
		}


		/**
		 * @param null $field
		 * @return bool
		 */
		public function hasChangesProperty($field = null){
			if($field === null){
				foreach($this->_schema->getFields() as $field){
					$name = $field->getName();
					if($this->isInitializedProperty($name)){
						if($field instanceof Field\Relation && $field->isMany()){
							/** @var Relationship $c */
							$c = $this->_getFrontProperty($name);
							if($c->isDirty()){
								return true;
							}
						}elseif($this->_getProcessed($name) !== $this->_getFrontProperty($name)){
							return true;
						}

					}
				}
			}
			$name = $field;
			if($this->isInitializedProperty($name)){
				$field = $this->_schema->getField($name);
				if($field instanceof Field\Relation && $field->isMany()){
					/** @var Relationship $c */
					$c = $this->_getFrontProperty($name);
					if($c->isDirty()){
						return true;
					}
				}elseif($this->_getProcessed($name) !== $this->_getFrontProperty($name)){
					return true;
				}
			}
			return false;
		}

		/**
		 * @return string[]
		 */
		public function getChangedProperties(){
			$changed = [];
			foreach($this->_schema->getFields() as $field){
				$name = $field->getName();
				if($this->isInitializedProperty($name)){
					if($field instanceof Field\Relation && $field->isMany()){
						/** @var Relationship $c */
						$c = $this->_getFrontProperty($name);
						if($c->isDirty()){
							$changed[] = $name; // Нужно узнавать у коллекции, были ли изменения
						}
					}elseif($this->_getProcessed($name) !== $this->_getFrontProperty($name)){
						$changed[] = $name;
					}
				}
			}
			return $changed;
		}

		/**
		 * @return bool
		 */
		public function save(){
			if($this->_operation_processing){
				if($this->_operation_made === self::OP_DELETE){
					throw new \LogicException('Current operation execute is not allow saving record!');
				}
				return true;
			}
			switch($this->_operation_made){
				case self::OP_CREATE:
					if($this->beforeSave() !== false && $this->beforeCreate() !== false){
						$this->_operation_processing = true;
						if($this->_doCreate()){
							$this->_operation_made = self::OP_UPDATE;
							$this->_operation_processing = false;
							$this->onCreate();
							$this->onSave();
							return true;
						}
					}
					break;

				case self::OP_UPDATE:
					$changed = $this->getChangedProperties();
					if(!$changed){
						return true;
					}

					if($this->beforeSave() !== false && $this->beforeUpdate() !== false){
						$this->_operation_processing = true;
						if($this->_doUpdate($changed)){
							$this->_operation_made = self::OP_UPDATE;
							$this->_operation_processing = false;
							$this->onUpdate();
							$this->onSave();
							return true;
						}
					}
					break;
				case self::OP_DELETE:
					return true;
					break;
			}
			return false;
		}

		/**
		 * @return bool
		 */
		public function remove(){
			if($this->_operation_processing){
				if($this->_operation_made !== self::OP_DELETE){
					throw new \LogicException('Current operation execute is not allow remove!');
				}
				return true;
			}
			if($this->_operation_made === self::OP_UPDATE && ($this->beforeRemove() !== false)){
				$this->_operation_processing = true;
				$this->_operation_made = self::OP_DELETE;
				if(!$this->_doRemove()){
					return false;
				}else{
					$this->_schema->getCollection()->removeItem($this);
					$this->_operation_processing = false;
				}
			}

			return true;
		}



		/**
		 * @param $name
		 * @param $value
		 */
		abstract protected function _setFrontProperty($name, $value);

		/**
		 * @param $name
		 * @return mixed
		 */
		abstract protected function _getFrontProperty($name);

		/**
		 * @param $key
		 * @return mixed
		 */
		protected function _getProcessed($key){
			if(!array_key_exists($key, $this->_processed)){
				return $this->_processed[$key] = $this->_schema->valueAccessGet($this, $key);
			}
			return $this->_processed[$key];
		}

		/**
		 * @return bool
		 * @throws \Exception
		 */
		protected function _doCreate(){

			$store = $this->getWriteStorage();
			$pkField = null;
			/** @var Relation[] $relationFields */
			$relationFields = [ ];
			/** @var Virtual[] $virtualFields */
			$virtualFields = [ ];
			/** @var Field[] $originalityFields */
			$originalityFields = [ ];
			foreach($this->_schema->getFields() as $field){
				if($field instanceof Relation){
					$relationFields[] = $field;
				}elseif($field instanceof Virtual){
					$virtualFields[] = $field;
				}else{
					if($field->isPrimary()){
						$pkField = $field;
					}
					$originalityFields[] = $field;
				}
			}

			try{
				self::$properties_changes_restrict = false;
				foreach($virtualFields as $field){

				}
				if($relationFields){
					$store->begin();
					foreach($relationFields as $field){
						$field->beforeRecordSave($this,$this->_processed,null);
					}
				}
				$data = null;
				foreach($originalityFields as $field){
					$name = $field->getName();
					$value = $this->isInitializedProperty($name) ? $this->_getFrontProperty($name) : null;
					$data = $this->_schema->valueAccessSet($data, $name, $value);
				}

				if(!$this->_schema->storageCreate($data, $this->getSource())){
					if($relationFields){
						$store->rollback();
					}
					return false;
				}
				$this->_afterStorageCreate($data, $pkField->getName(), $store->lastCreatedIdentifier(),$pkField);
				if($relationFields){
					foreach($relationFields as $field){
						$field->afterRecordSave($this,$this->_processed,null);
					}
					$store->commit();
				}
				$this->_onCreateCommit();
				self::$properties_changes_restrict = true;
				return true;
			}catch (\Exception $e){
				self::$properties_changes_restrict = true;
				if($relationFields){
					$store->rollback();
				}
				throw $e;
			}
		}

		/**
		 * @param $changed
		 * @return bool
		 * @throws \Exception
		 */
		protected function _doUpdate($changed){


			$pkField = null;
			/** @var Relation[] $relationFields */
			$relationFields = [ ];
			/** @var Virtual[] $virtualFields */
			$virtualFields = [ ];
			/** @var Field[] $originalityFields */
			$originalityFields = [ ];
			foreach($this->_schema->getFields() as $field){
				if($field instanceof Relation){
					$relationFields[] = $field;
				}elseif($field instanceof Virtual){
					$virtualFields[] = $field;
				}else{
					if($field->isPrimary()){
						$pkField = $field;
					}
					$originalityFields[] = $field;
				}
			}

			$store = $this->getWriteStorage();
			try{
				foreach($virtualFields as $field){

				}
				if($relationFields){
					$store->begin();

					$belongsChanges = false;
					foreach($relationFields as $field){
						if($field->getType() === Relation::TYPE_BELONGS){
							$belongsChanges = true;
						}
						$field->beforeRecordSave($this,$this->_processed,$changed);
					}
					if($belongsChanges){
						$changed = $this->getChangedProperties();
					}

				}
				$dynamicUpdate = $this->_schema->isDynamicUpdate();
				$idValue = $this->getIdentifierValue();
				$pkName = $pkField->getName();
				if($dynamicUpdate){
					$data = null;
					$original = $this->_original;
					foreach($originalityFields as $field){
						$name = $field->getName();
						if(in_array($name, $changed, true)){
							$value = $this->_getFrontProperty($name);
							$data = $this->_schema->valueAccessSet($data, $name, $value);
							$original = $this->_schema->valueAccessSet($original, $name, $value);
						}
					}
					if($data){
						if(!$this->_schema->storageUpdateById($data, $idValue)){
							if($relationFields){
								$store->rollback();
							}
							return false;
						}
						$this->_afterStorageUpdate($original, $pkName,$idValue, $changed);
					}
				}else{
					$data = $this->_original;
					foreach($originalityFields as $field){
						$name = $field->getName();
						$data = $this->_schema->valueAccessSet($data, $name, $this->_getFrontProperty($name));
					}
					if(!$this->_schema->storageUpdateById($data, $idValue)){
						if($relationFields){
							$store->rollback();
						}
						return false;
					}
					$this->_afterStorageUpdate($data, $pkName,$idValue, $changed);
				}

				if($relationFields){
					foreach($relationFields as $field){
						$field->afterRecordSave($this,$this->_processed,$changed);
					}
					$store->commit();
				}

				$this->_onUpdateCommit($idValue, $changed);

				return true;
			}catch (\Exception $e){
				if($relationFields){
					$store->rollback();
				}
				throw $e;
			}
			// TODO Запретить выставлять NULL к Relationship полям! или додумать алгоритм обработки при сохранении или при выставлении

		}

		/**
		 * @return bool
		 * @throws \Exception
		 */
		protected function _doRemove(){
			/**
			 * @var Field[] $fields
			 * @var Relation[] $relationFields
			 */
			$fields = [];
			$relationFields = [];
			foreach($this->_schema->getFields() as $field){
				$name = $field->getName();
				$fields[$name] = $field;
				if($field instanceof Relation){
					$relationFields[$name] = $field;
				}
			}
			$store = $this->getWriteStorage();
			try{
				if($relationFields){
					$store->begin();
					foreach($relationFields as $name => $field){
						$field->beforeRecordRemove($this);
					}
				}
				$pkField = $this->_schema->getPrimaryField();
				$pkName = $pkField->getName();
				$pkValue = $this->getProperty($pkName);
				if(!$this->_schema->storageRemove([[$pkName,'=',$pkValue]])){
					if($relationFields){
						$store->rollback();
					}
					return false;
				}
				$this->_afterStorageRemove();
				if($relationFields){
					foreach($relationFields as $name => $field){
						$field->afterRecordRemove($this);
					}
					$store->commit();
				}
				$this->_onRemoveCommit();
				return true;
			}catch(\Exception $e){
				if($relationFields){
					$store->rollback();
				}
				throw $e;
			}
		}

		protected function afterFetch(){ }

		protected function beforeSave(){ }

		protected function onSave(){ }

		protected function beforeCreate(){ }

		protected function onCreate(){ }

		protected function beforeUpdate(){ }

		protected function onUpdate(){ }

		protected function beforeRemove(){ }

		protected function onRecordReady(){ }

		protected function onConstruct(){}

		/**
		 * @param $data
		 * @param $pkName
		 * @param $identifier
		 * @param Field $pkField
		 */
		protected function _afterStorageCreate($data, $pkName, $identifier, Field $pkField){
			$this->_resetAll($pkName);
			$this->_original = $this->_schema->valueAccessSet($data, $pkName, $identifier);
		}

		/**
		 * @param $data
		 * @param $data
		 * @param $pkName
		 * @param $idValue
		 * @param array $changed
		 */
		protected function _afterStorageUpdate($data, $pkName,$idValue,array $changed){
			$this->_original = $data;
		}

		protected function _afterStorageRemove(){

		}


		/**
		 * @see Record::_doCreate
		 */
		protected function _onCreateCommit(){
			$collection = $this->_schema->getCollection();
			$collection->saturate(Record\Collection::SAT_LOAD,$collection);
			$collection->add($this);
			$collection->saturate(false);
		}

		/**
		 * @see Record::_doUpdate
		 * @param $id
		 * @param $changed
		 */
		protected function _onUpdateCommit($id,$changed){

		}

		/**
		 * @see Record::_doRemove
		 */
		protected function _onRemoveCommit(){
			$collection = $this->_schema->getCollection();
			$collection->setSyncLevel(Record\Collection::SYNC_FULL);
			$collection->removeItem($this);
			$collection->setSyncLevel();
		}


	}

}

