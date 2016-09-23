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
	use Jungle\Data\Record\Exception\Field\AccessViolation;
	use Jungle\Data\Record\Exception\Field\ReadonlyViolation;
	use Jungle\Data\Record\Exception\Field\UnexpectedValue;
	use Jungle\Data\Record\ExportableInterface;
	use Jungle\Data\Record\Head\Field;
	use Jungle\Data\Record\Head\Field\Relation;
	use Jungle\Data\Record\Head\Field\Virtual;
	use Jungle\Data\Record\Head\Schema;
	use Jungle\Data\Record\TransientState;
	use Jungle\Data\Storage\Exception\DuplicateEntry;
	use Jungle\Data\Storage\Exception\Operation;
	use Jungle\Util\Data\Record\PropertyRegistryInterface;
	use Jungle\Util\Data\Record\PropertyRegistryTransientInterface;
	use Jungle\Util\Data\Schema\OuterInteraction\SchemaAwareInterface;
	use Jungle\Util\Data\Storage;
	use Jungle\Util\Data\Validation;
	use Jungle\Util\Data\Validation\Message\RuleMessage;
	use Jungle\Util\Data\Validation\Message\ValidationCollector;
	use Jungle\Util\Data\Validation\Message\ValidatorMessage;
	
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
		protected static $properties_changes_restrict_level = 0;



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

		/** @var  ValidationCollector|null */
		protected $validation_collector;

		/** @var  TransientState|null */
		protected $transient_state;


		/**
		 * Record constructor.
		 * @param null $validationCollector
		 */
		public function __construct($validationCollector = null){
			$this->_internalIdentifier = ++self::$instantiatedRecordsCount;
		}

		/**
		 * @param null $validationCollector
		 */
		protected function _initValidationCollector($validationCollector = null){
			if($validationCollector !== null){
				if($validationCollector instanceof ValidationCollector){
					$this->setValidationCollector($validationCollector);
				}elseif($validationCollector === true){
					$this->setValidationCollector();
				}
			}
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
		 * @param bool $appliedInOld
		 * @param bool $appliedInNew
		 * @return $this
		 * @throws AccessViolation
		 * @throws Exception
		 * @throws ReadonlyViolation
		 * @throws UnexpectedValue
		 */
		public function assign(array $data, $whiteList = null, $blackList = null, $appliedInOld = false, $appliedInNew = false, $dirtyApplied = false){
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

			if(!self::$properties_changes_restrict_level){
				if($readOnly = $this->_schema->getReadonlyNames()){
					$attributes = array_diff($attributes, $readOnly);
				}
				if($privates = $this->_schema->getPrivateNames()){
					$attributes = array_diff($attributes, $privates);
				}
			}

			foreach($attributes as $key){
				if(array_key_exists($key, $data)){
					$this->setProperty($key,$data[$key],$appliedInOld,$appliedInNew,$dirtyApplied);
				}
			}
			return $this;
		}

		/**
		 * @param $key
		 * @param $value
		 * @param bool $appliedInOld
		 * @param bool $appliedInNew
		 * @param bool $dirtyApplied
		 * @return $this
		 * @throws AccessViolation
		 * @throws Exception
		 * @throws Exception\Field
		 * @throws ReadonlyViolation
		 * @throws UnexpectedValue
		 */
		public function setProperty($key, $value, $appliedInOld = false, $appliedInNew = false, $dirtyApplied = false){
			$field = $this->_schema->getField($key);
			if($field){

				if(!self::$properties_changes_restrict_level){
					if($field->isReadonly()){
						throw new ReadonlyViolation('Could not set readonly "'.$key.'" property');
					}
					if($field->isPrivate()){
						throw new AccessViolation('Could not set private property "'.$key.'"');
					}
				}


				$value = $field->stabilize($value);
				if(!$this->validation_collector && !$dirtyApplied && $field->validate($value) === false){
					throw new UnexpectedValue('Verification aborted!');
				}

				if($field instanceof Relation){
					$this->_setRelationFieldValue($key, $value, $field, $appliedInOld, $appliedInNew);
				}else{
					$this->_setFrontProperty($key, $value);
				}

				if($dirtyApplied){
					$this->_processed[$key] = $value;
					$this->_original = $this->_schema->valueAccessSet($this->_original,$key, $value);
				}

			}else{
				throw new Exception('Set Field "'.$key.'" not exists in data map schema["'.$this->_schema->getName().'"]');
			}
			return $this;
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
		 * @throws Exception\Field
		 */
		public function getProperty($key){
			$field = $this->_schema->getField($key);
			if($field){
				if(!self::$properties_changes_restrict_level && $field->isPrivate()){
					throw new AccessViolation('Could not get private property "'.$key.'"');
				}
				return $this->_getFrontProperty($key);
			}else{
				throw new Exception\Field('Get Field "'.$key.'" not exists in data map schema["'.$this->_schema->getName().'"]');
			}
		}





		/**
		 *
		 */
		protected function _continueSetProperty(){

		}

		/**
		 * @param $key
		 * @param $value
		 * @param Relation $field
		 * @param bool $appliedInOld
		 * @param bool $appliedInNew
		 * @throws AccessViolation
		 * @throws Exception\Field
		 * @throws ReadonlyViolation
		 * @throws UnexpectedValue
		 */
		protected function _setRelationFieldValue($key, $value,Relation $field, $appliedInOld = false, $appliedInNew = false){
			if(!$field->isMany()){
				if($this->isInitializedProperty($key)){
					$old = $this->_getFrontProperty($key);
					if($value !== $old){
						$this->_setFrontProperty($key, $value);
						/** @var Relation[] $opposites */
						if((!$appliedInOld && $old) || (!$appliedInNew && $value)){
							$opposites = $field->getOppositeRelations($this);
						}
						if(!$appliedInOld && $old instanceof Record){
							foreach($opposites as $f){
								if($f->isMany()){
									$relationship = $old->getProperty($f->getName());
									$relationship->removeItem($this);
								}else{
									$old->setProperty($f->getName(), null, true ,true);
								}
							}
						}

						if(!$appliedInNew && $value instanceof Record){
							foreach($opposites as $f){
								if($f->isMany()){
									$relationship = $value->getProperty($f->getName());
									$relationship->add($this);
								}else{
									$value->setProperty($f->getName(), $this, true ,true);
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
					if($field->isDynamic()){
						$this->setProperty($field->getDynamicSchemafield(), null);
					}
				}
			}else{
				if($value === null || (is_array($value) && empty($value))){
					$relationship = $this->_getFrontProperty($key);
					$relationship->remove();
				}else{
					throw new Exception\Field('Set property "'.$key.'" positive value is forbidden, but you can detach all by pass null or empty array');
				}
			}
		}

		/**
		 * @param $key
		 * @param $value
		 * @param $field
		 */
		protected function _beforeSetProperty($key, $value, $field){

		}

		/**
		 * @param $key
		 * @param $value
		 * @param $field
		 */
		protected function _afterSetProperty($key, $value, $field){

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
		 * @throws AccessViolation
		 * @throws Exception\Field
		 * @throws ReadonlyViolation
		 * @throws UnexpectedValue
		 */
		public function resetPropertyDefault($name){
			$field = $this->_schema->getField($name);
			if($field){
				$default = $field->getDefault();
				if(($default !== null) || ($default === null && $field->isNullable())){
					return $this->setProperty($name, $default);
				}else{
					throw new Exception\Field('Property "' . $name . '" no have default value');
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
		 * @param bool $public
		 * @return array
		 * @throws Exception
		 */
		public function export( $public = true ){
			$values = [ ];
			foreach($this->_schema->getFields() as $field){
				if(!$field->isPrivate() && $field->isOriginality()){
					$name = $field->getName();
					$values[$name] = $this->getProperty($name);
				}
			}
			return $values;
		}

		/**
		 * Актуализация данных
		 */
		public function refresh(){
			if($this->_operation_made === self::OP_CREATE){
				$this->_original = null;
				$this->_afterOriginalDataChanged();
				$this->onRecordReady();
			}else{
				$item = $this->_schema->storageLoadById($this->getIdentifierValue());
				if($item !== $this->_original){
					$this->_original = $item;
					$this->_afterOriginalDataChanged();
					$this->onRecordReady();
				}
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
		 *
		 */
		protected function _afterReset(){

		}

		/**
		 *
		 */
		protected function _afterResetAll(){
			$this->transient_state = null;
		}

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
		 * @throws Exception
		 */
		public function save(){
			try{
				if($this->_operation_processing){
					if($this->_operation_made === self::OP_DELETE){
						throw new Exception('Current operation execute is not allow saving record!');
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
								$this->_schema->getCollection()->itemCreated($this);
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

						if($this->beforeSave() !== false && $this->beforeUpdate($changed) !== false){
							$this->_operation_processing = true;
							if($this->_doUpdate($this->getChangedProperties())){
								$this->_operation_made = self::OP_UPDATE;
								$this->_operation_processing = false;
								$this->_schema->getCollection()->itemUpdated($this);
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
			}finally{
				$this->validation_collector = null;
			}
		}

		/**
		 * @return bool
		 * @throws Exception
		 * @throws \Exception
		 */
		public function delete(){
			try{
				if($this->_operation_processing){
					if($this->_operation_made !== self::OP_DELETE){
						throw new Exception('Current operation execute is not allow delete!');
					}
					return true;
				}
				if($this->_operation_made === self::OP_UPDATE && ($this->beforeRemove() !== false)){
					$this->_operation_processing = true;
					$this->_operation_made = self::OP_DELETE;
					if(!$this->_doDelete()){
						return false;
					}else{
						$this->_schema->getCollection()->removeItem($this);
						$this->_operation_processing = false;
					}
				}

				return true;
			}finally{
				$this->validation_collector = null;
			}

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
		abstract protected function &_getFrontProperty($name);

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
		 *
		 */
		public static function startAllChangesLevel(){
			self::$properties_changes_restrict_level++;
		}

		/**
		 *
		 */
		public static function stopAllChangesLevel(){
			self::$properties_changes_restrict_level--;
		}

		/**
		 *
		 */
		public static function disableAllChangesMode(){
			self::$properties_changes_restrict_level = 0;
		}

		/**
		 * @return Validation|null
		 */
		public function getValidation(){
			return $this->_schema->getValidation();
		}


		/**
		 * @return TransientState
		 */
		public function getTransientState(){
			return $this->transient_state;
		}

		/**
		 * @param null $tag
		 * @return bool
		 */
		public function stateFix($tag = null){
			$properties = [];
			foreach($this->_schema->getFields() as $field){
				$name = $field->getName();
				if($this->isInitializedProperty($name)){
					$properties[$name] = $this->_getFrontProperty($name);
				}
			}
			if($properties){
				$old = $this->transient_state;
				$this->transient_state = TransientState::checkout($properties, $tag, $old);
				if($this->transient_state !== $old){
					if($this->transient_state){
						$this->transient_state->setFixed(true);
					}
					return true;
				}
			}
			if($this->transient_state){
				$this->transient_state->setFixed(true);
			}
			return false;
		}

		/**
		 * @param null $tag
		 * @return bool
		 */
		public function stateCapture($tag = null){
			$properties = [];
			foreach($this->_schema->getFields() as $field){
				$name = $field->getName();
				if($this->isInitializedProperty($name)){
					$properties[$name] = $this->_getFrontProperty($name);
				}
			}
			if($properties){
				$old = $this->transient_state;
				$this->transient_state = TransientState::checkout($properties, $tag, $old);
				if($this->transient_state !== $old){
					return true;
				}
			}
			return false;
		}


		/**
		 * @throws AccessViolation
		 * @throws Exception
		 * @throws ReadonlyViolation
		 * @throws UnexpectedValue
		 */
		public function stateRollback(){
			if($this->transient_state){
				$data = $this->transient_state->getForwardData($this->_processed);
				$previous = $this->transient_state->getPrevious();
				if($previous){
					$data = array_replace( $this->transient_state->getRollbackData(), $data );
				}
				if($data){
					try{
						self::$properties_changes_restrict_level++;
						foreach($data as $k=>$v){
							$this->setProperty($k,$v);
						}
					}finally{
						self::$properties_changes_restrict_level--;
					}
				}

				if($previous){
					$this->transient_state = $previous;
				}else{
					$this->transient_state = null;
				}
			}else{
				$this->reset();
			}
			return $this;
		}

		/**
		 * @return $this
		 * @throws AccessViolation
		 * @throws Exception
		 * @throws ReadonlyViolation
		 * @throws UnexpectedValue
		 */
		public function stateRecover(){
			if($this->transient_state){
				$data = $this->transient_state->getForwardData();
				if($data){
					try{
						self::$properties_changes_restrict_level++;
						foreach($data as $k=>$v){
							$this->setProperty($k,$v);
						}
					}finally{
						self::$properties_changes_restrict_level--;
					}
				}
			}else{
				$this->reset();
			}
			return $this;
		}

		/**
		 * @return $this
		 */
		public function stateShift(){
			if($this->transient_state){
				$this->transient_state = $this->transient_state->getPrevious();
			}
			return $this;
		}

		/**
		 * @return $this
		 */
		public function stateClean(){
			if($this->transient_state){
				if($this->transient_state->clean() === false){
					$this->transient_state = null;
				}
			}
			return $this;
		}



		/**
		 * @param ValidationCollector $collector
		 * @return ValidationCollector
		 */
		public function setValidationCollector(ValidationCollector $collector = null){
			if(!$collector){
				$collector = new ValidationCollector();
			}
			$this->stateCapture();
			$this->validation_collector = $collector;
			return $collector->setObject($this);
		}

		/**
		 * @return ValidationCollector
		 */
		public function getValidationCollector(){
			return $this->validation_collector;
		}


		/**
		 * @throws AccessViolation
		 * @throws Exception
		 * @throws ReadonlyViolation
		 * @throws UnexpectedValue
		 */
		protected function _preValidate(){
			if($this->validation_collector){
				if($this->stateCapture()){
					$data = $this->transient_state->getData();
					$this->stateShift();
					$messages = [];
					foreach($data as $k=>$v){
						try{
							$this->setProperty($k,$v);
						}catch(ValidatorMessage $message){
							$messages[] = $message;
						}
					}
					if($messages){
						$this->validation_collector->appendMessages($messages);
						return false;
					}
				}
			}
			return true;
		}

		/**
		 * TODO
		 * @return bool
		 * @throws ValidationCollector
		 */
		protected function _validate(){
			$validation = $this->_schema->getValidation();
			if($validation){
				$messages = $validation->validate($this);
				if($messages){
					if($this->validation_collector){
						$this->validation_collector->appendMessages($messages);
						return false;
					}else{
						throw new ValidationCollector($this, $messages);
					}
				}
			}
			return true;
		}


		/**
		 * @return true|ValidationCollector
		 * @throws ValidationCollector
		 */
		public function validate(){
			try{
				if($this->validation_collector){
					if($this->stateCapture()){
						$data = $this->transient_state->getData();
						$this->stateShift();
						$messages = [];
						foreach($data as $k=>$v){
							try{
								$this->setProperty($k,$v);
							}catch(ValidatorMessage $message){
								$messages[] = $message;
							}
						}
						if($messages){
							//return
							$this->validation_collector->appendMessages($messages);
							//return false;
						}
					}
				}

				$validation = $this->_schema->getValidation();
				$messages = $validation->validate($this);
				if($messages){
					if($this->validation_collector){
						return $this->validation_collector->appendMessages($messages);
						//return false;
					}else{
						throw new ValidationCollector($this, $messages);
					}
				}
				return true;
			}finally{
				$this->validation_collector = null;
			}
		}

		/**
		 * @param Operation $operation
		 * @param bool $delete
		 * @return bool
		 * @throws Operation
		 */
		protected function _handleStorageOperationException(Operation $operation, $delete = false){
			if($this->validation_collector){
				// TODO improve idea
				if($operation instanceof DuplicateEntry){
					$this->validation_collector->appendMessages([
						new ValidatorMessage(null, null, [], [ new RuleMessage('Unique',null) ],$operation->getMessage())
					]);
					return false;
				}
			}
			throw $operation;
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

			if($this->_preValidate()!==true){
				return false;
			}

			try{
				self::$properties_changes_restrict_level++;
				foreach($virtualFields as $field){}
				if($relationFields){
					$store->begin();
					foreach($relationFields as $field){
						$field->beforeRecordSave($this,$this->_processed,null);
					}
				}
				$data = null;
				foreach($originalityFields as $field){
					$name = $field->getName();
					$value = $this->_getFrontProperty($name);
					$data = $this->_schema->valueAccessSet($data, $name, $value);
				}

				if($this->_validate()!==true){
					return false;
				}

				try{
					if(!$this->_schema->storageCreate($data, $this->getSource())){
						if($relationFields){
							$store->rollback();
						}
						return false;
					}
				}catch(Operation $e){
					if($this->_handleStorageOperationException($e) === false){
						return false;
					}
				}

				$this->_afterStorageCreate($data, $pkField->getName(), $store->lastCreatedIdentifier(),$pkField);
				if($relationFields){
					foreach($relationFields as $field){
						$field->afterRecordSave($this,$this->_processed,null);
					}
					$store->commit();
				}
				$this->_onCreateCommit();
				return true;
			}catch(\Exception $e){
				if($relationFields){
					$store->rollback();
				}
				throw $e;
			}finally{
				self::$properties_changes_restrict_level--;
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

			$this->_preValidate();

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
						if($this->_validate()!==true){
							return false;
						}
						try{
							if(!$this->_schema->storageUpdateById($data, $idValue)){
								if($relationFields){
									$store->rollback();
								}
								return false;
							}
						}catch(Operation $e){
							if($this->_handleStorageOperationException($e) === false){
								return false;
							}
						}

						$this->_afterStorageUpdate($original, $pkName,$idValue, $changed);
					}
				}else{
					$data = $this->_original;
					foreach($originalityFields as $field){
						$name = $field->getName();
						$data = $this->_schema->valueAccessSet($data, $name, $this->_getFrontProperty($name));
					}
					if($data){
						if($this->_validate()!==true){
							return false;
						}
						try{
							if(!$this->_schema->storageUpdateById($data, $idValue)){
								if($relationFields){
									$store->rollback();
								}
								return false;
							}
						}catch(Operation $e){
							if($this->_handleStorageOperationException($e) === false){
								return false;
							}
						}

						$this->_afterStorageUpdate($data, $pkName,$idValue, $changed);
					}
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
		protected function _doDelete(){
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
						$field->beforeRecordDelete($this);
					}
				}
				$pkField = $this->_schema->getPrimaryField();
				$pkName = $pkField->getName();
				$pkValue = $this->getProperty($pkName);

				try{
					if(!$this->_schema->storageRemove([[$pkName,'=',$pkValue]])){
						if($relationFields){
							$store->rollback();
						}
						return false;
					}
				}catch(Operation $e){
					if($this->_handleStorageOperationException($e,true) === false){
						return false;
					}
				}

				$this->_afterStorageRemove();
				if($relationFields){
					foreach($relationFields as $name => $field){
						$field->afterRecordDelete($this);
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

		/**
		 *
		 */
		protected function onSave(){
			$this->stateClean();
			$this->stateFix();
		}

		protected function beforeCreate(){
			foreach($this->_schema->getFields() as $field){
				if($field->hasOption('on_create')){
					call_user_func($field->getOption('on_create'),$this,$field);
				}
			}
		}

		protected function onCreate(){}

		/**
		 * @param array $changed
		 * @return bool
		 */
		protected function beforeUpdate(array $changed){
			foreach($this->_schema->getFields($changed) as $field){
				if(!$field->getOption('changeable', true)){
					return false;
				}
			}
			return true;
		}

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
		 * @see Record::_doDelete
		 */
		protected function _onRemoveCommit(){
			$collection = $this->_schema->getCollection();
			$collection->setSyncLevel(Record\Collection::SYNC_FULL);
			$collection->removeItem($this);
			$collection->setSyncLevel();
		}


	}

}

