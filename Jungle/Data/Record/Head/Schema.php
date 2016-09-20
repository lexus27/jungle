<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:48
 */
namespace Jungle\Data\Record\Head {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Collection;
	use Jungle\Data\Record\DataMap;
	use Jungle\Data\Record\Head\Field;
	use Jungle\Data\Record\Head\Field\Relation;
	use Jungle\Util\Data\Schema\OuterInteraction\Mapped\Schema as MappedSchema;
	use Jungle\Util\Data\Schema\Validation;
	use Jungle\Util\Data\ShipmentInterface;
	use Jungle\Util\Data\Storage;
	use Jungle\Util\Data\Storage\StorageInterface;
	
	/**
	 * Class Schema
	 * @package modelX
	 *
	 * @property Field[]    $fields
	 * @method Field        getPrimaryField()
	 *
	 */
	class Schema extends MappedSchema{

		/** @var  string */
		protected $name;

		/** @var  Schema */
		protected $ancestor;

		/** @var  string */
		protected $source;

		/** @var  StorageInterface */
		protected $storage;

		/** @var  Field[] */
		protected $fields = [];

		/** @var  Validation|null|bool */
		protected $validation;

		/** @var  SchemaManager */
		protected $schema_manager;

		/** @var  Collection */
		protected $collection;

		/** @var  array|null  */
		protected $original_names;

		/** @var  array|null */
		protected $enumerable_names;

		/** @var array  */
		protected $readonly_names = [];

		/** @var array  */
		protected $private_names = [];

		/** @var  bool  */
		protected $dynamic_update = true;

		/** @var  Record */
		protected $flyweight_record;

		/** @var  string */
		protected $record_classname = DataMap::class;

		/**
		 * Universal instantiate class by classname from value specified in field
		 * @var null|string
		 */
		protected $derivative_field;

		/**
		 * Schema constructor.
		 * @param $name
		 */
		public function __construct($name){
			$this->setName($name);
		}

		/**
		 * @param Record $record
		 * @return $this
		 */
		public function setFlyweight(Record $record){
			$this->flyweight_record = $record;
			return $this;
		}


		/**
		 * @param Collection $collection
		 * @return $this
		 */
		public function setCollection(Collection $collection){
			$this->collection = $collection;
			return $this;
		}

		/**
		 * @return Collection
		 */
		public function getCollection(){
			if(!$this->collection){
				if($this->ancestor){
					$this->collection = $this->ancestor->collection->extend([
						$this->getDerivativeField() => $this->name
					]);
				}else{
					$this->collection = new Collection();
				}
				$this->collection->setSchema($this);
			}
			return $this->collection;
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @param Validation $validation
		 * @return $this
		 */
		public function setValidation(Validation $validation = null){
			$this->validation = $validation;
			return $this;
		}

		/**
		 * @return $this
		 */
		public function disableValidation(){
			$this->validation = false;
			return $this;
		}

		/**
		 * @return Validation|null
		 */
		public function getValidation(){
			if($this->validation){
				return $this->validation;
			}elseif($this->validation === null && $this->ancestor){
				if($this->ancestor->validation === false && $this->ancestor->ancestor){
					return $this->ancestor->ancestor->getValidation();
				}
				return $this->ancestor->getValidation();
			}else{
				return null;
			}
		}

		/**
		 *
		 */
		protected function _initCache(){
			$original_names_fill = $this->original_names===null;
			if($original_names_fill) $this->original_names = [];

			$enumerable_fill = $this->enumerable_names === null;
			if($enumerable_fill) $this->enumerable_names = [];

			$readonly_fill = $this->readonly_names === null;
			if($readonly_fill) $this->readonly_names = [];

			$private_fill = $this->private_names === null;
			if($private_fill) $this->private_names = [];

			if($enumerable_fill || $original_names_fill || $readonly_fill || $private_fill){
				foreach($this->getFields() as $field){
					if($enumerable_fill && $field->isEnumerable()){
						$this->enumerable_names[] = $field->getName();
					}
					if($original_names_fill && $field->isOriginality()){
						$this->original_names[] = $field->getOriginalKey();
					}
					if($private_fill && $field->isPrivate()){
						$this->private_names[] = $field->getName();
					}
					if($readonly_fill && $field->isReadonly()){
						$this->readonly_names[] = $field->getName();
					}
				}
			}
		}

		/**
		 * @return array
		 */
		public function getReadonlyNames(){
			$this->_initCache();
			return $this->readonly_names;
		}

		/**
		 * @return array
		 */
		public function getPrivateNames(){
			$this->_initCache();
			return $this->private_names;
		}

		/**
		 * @return array|null
		 */
		public function getEnumerableNames(){
			$this->_initCache();
			return $this->enumerable_names;
		}




		/**
		 * @param $field
		 */
		protected function beforeAddField($field){
			if(!$field instanceof Field){
				throw new \LogicException('');
			}
		}

		/**
		 * @param $field
		 */
		protected function afterAddField($field){
			$this->original_names = null;
		}



		/**
		 * @param string $source
		 * @return $this
		 */
		public function setSource($source){
			if($this->ancestor){
				$this->ancestor->setSource($source);
			}
			$this->source = $source;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getSource(){
			return $this->source;
		}

		/**
		 * @param Record|null $record
		 * @return string
		 */
		public function getWriteSource(Record $record = null){
			if($record && ($source = $record->getSource())){
				return $source;
			}
			return $this->source;
		}


		/**
		 * @param bool|true $dynamic
		 * @return $this
		 */
		public function setDynamicUpdate($dynamic = true){
			$this->dynamic_update = $dynamic;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isDynamicUpdate(){
			if($this->ancestor && $this->dynamic_update === null){
				return $this->ancestor->isDynamicUpdate();
			}
			return $this->dynamic_update;
		}


		/**
		 * @param $className
		 * @return $this
		 */
		public function setRecordClassname($className){
			$this->record_classname = $className;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getRecordClassname(){
			return $this->record_classname;
		}

		/**
		 * @return bool
		 */
		public function isDynamic(){
			return !!$this->derivative_field;
		}

		/**
		 * @param $field_name
		 * @return $this
		 */
		public function setDerivativeField($field_name){
			$this->derivative_field = $field_name;
			return $this;
		}
		
		/**
		 * @return null|string
		 */
		public function getDerivativeField(){
			if($this->ancestor && $this->derivative_field === null){
				return $this->ancestor->getDerivativeField();
			}
			return $this->derivative_field;
		}

		/**
		 * @param StorageInterface $storage
		 * @return $this
		 */
		public function setStorage(StorageInterface $storage){
			if($this->ancestor){
				$this->ancestor->setStorage($storage);
			}
			$this->storage = $storage;
			return $this;
		}

		/**
		 * @return StorageInterface
		 */
		public function getStorage(){
			if($this->ancestor && $this->storage === null){
				return $this->ancestor->getStorage();
			}
			if(!$this->storage){
				throw new \LogicException('Storage is not defined!');
			}
			return $this->storage;
		}

		/**
		 * @param Record|null $record
		 * @return StorageInterface
		 */
		public function getWriteStorage(Record $record = null){
			if($record && ($storage = $record->getWriteStorage())){
				return $storage;
			}
			if($this->ancestor && $this->storage === null){
				return $this->ancestor->getWriteStorage($record);
			}
			return $this->storage;
		}

		/**
		 * @param SchemaManager $schemaManager
		 * @return $this
		 */
		public function setSchemaManager($schemaManager){
			$this->schema_manager = $schemaManager;
			return $this;
		}

		/**
		 * @return SchemaManager
		 */
		public function getSchemaManager(){
			if($this->ancestor && $this->schema_manager === null){
				return $this->ancestor->getSchemaManager();
			}
			return $this->schema_manager;
		}


		/**
		 * @param Relation $field
		 * @return Field\Relation[]
		 */
		public function getOppositeRelationsFor(Relation $field){
			$fields = [];
			foreach($this->getFields() as $f){
				if($f instanceof Relation && $field->isOppositeRelation($f, false)){
					$fields[] = $f;
				}
			}
			return $fields;
		}

		/**
		 * @param Relation $field
		 * @return Relation[]
		 */
		public function getIntermediateRelationsFor(Relation $field){
			$fields = [];
			foreach($this->getFields() as $f){
				if($f instanceof Relation && $field->isIntermediateRelation($f)){
					$fields[] = $f;
				}
			}
			return $fields;
		}

		/**
		 * @param Record $record
		 */
		public function markInitialized(Record $record){
			if($this->flyweight_record){
				if($this->flyweight_record === $record){
					$this->flyweight_record = null;
				}
			}
		}

		/**
		 * @param null $data
		 * @return string
		 * @throws \Exception
		 */
		protected function _matchSchemaName($data = null){
			if($this->ancestor){
				return $this->ancestor->_matchSchemaName($data);
			}
			if($data && $this->derivative_field){
				$schemaName = $this->valueAccessGet($data,$this->derivative_field);
				if($schemaName !== null){
					return $schemaName;
				}
			}
			return $this->record_classname;
		}


		/**
		 * @param $className
		 * @return DataMap
		 */
		protected function _instantiate($className){
			return new $className($this);
		}

		/**
		 * @param $className
		 * @return bool
		 */
		public function checkRecordClassname($className){
			return is_a($className,$this->record_classname,true);
		}


		/**
		 * @param Schema|string $schema
		 * @return bool
		 */
		public function isDerivativeFrom($schema){
			if($schema instanceof Schema){
				$schema = $schema->getName();
			}
			if($schema === $this->getName()) return true;
			if($this->ancestor){
				$o = $this;
				do{
					if($o->name === $schema){
						return true;
					}
				}while(($o = $o->ancestor));
			}
			return false;
		}

		/**
		 * @param $data
		 * @param int $operationMade
		 * @param bool $withoutMatch
		 * @return Record
		 * @throws \Exception
		 */
		public function initializeRecord($data = null, $operationMade = null, $withoutMatch = false){

			if($withoutMatch){
				$schemaName = $this->name;
			}else{
				$schemaName = $this->_matchSchemaName($data);
			}

			if($withoutMatch || $this->name === $schemaName){
				if($data === null){
					if($this->flyweight_record){
						$record = $this->flyweight_record;
						$record->markRecordInitialized();
					}else{
						$record = $this->_instantiate($schemaName);
					}
					$record->setOperationMade(Record::OP_CREATE);
					$record->setOriginalData(null);
					return $record;
				}else{
					if($this->flyweight_record){
						$record = $this->flyweight_record;
					}else{
						$record = $this->_instantiate($schemaName);
						$record->toFlyweight();
						$this->flyweight_record = $record;
					}
					$record->setOriginalData($data);
					$record->setOperationMade($operationMade?:Record::OP_UPDATE);
					return $record;
				}
			}else{
				$schema = $this->getSchemaManager()->getSchema($schemaName);
				if(!$schema){
					throw new \Exception('Schema "'.$schemaName.'" not found for initialize record');
				}
				if(!$schema->isDerivativeFrom($this)){
					throw new \Exception('Schema "'.$schemaName.'" is not derivative from "'.$this->name.'"');
				}
				return $schema->initializeRecord($data, $operationMade,  true  );
			}
		}


		/**
		 * @param null|array $condition
		 * @param null|int $limit
		 * @param null|int $offset
		 * @return int
		 */
		public function count($condition = null, $limit = null, $offset = null){
			$collection = $this->getCollection();
			$collection->setSyncLevel(Collection::SYNC_STORE);
			$affected = $this->getCollection()->count( $condition, $offset, $limit);
			$collection->setSyncLevel();
			return $affected;
		}


		/**
		 * @param $condition
		 * @param null $limit
		 * @param null $offset
		 * @param null $orderBy
		 * @return Collection
		 */
		public function load($condition = null, $limit = null, $offset = null, $orderBy = null){
			return $this->getCollection()->extend($condition, $limit, $offset, $orderBy);
		}

		/**
		 * @param $condition
		 * @param null $offset
		 * @param null $orderBy
		 * @return Record|null
		 */
		public function loadFirst($condition = null, $offset = null, $orderBy = null){
			if(!is_array($condition)){
				$condition = [$this->getPrimaryFieldName()=>$condition];
			}
			return $this->getCollection()->single($condition,$offset,$orderBy);
		}

		/**
		 * @param $data
		 * @param $condition
		 * @return $this
		 */
		public function update($data, $condition){
			$collection = $this->getCollection();
			$collection->setSyncLevel(Collection::SYNC_STORE);
			$affected = $this->getCollection()->update($data,$condition);
			$collection->setSyncLevel();
			return $affected;
		}

		/**
		 * @param $data
		 * @param $id
		 * @return bool
		 */
		public function updateById($data, $id){
			return !!$this->update($data, [$this->getPrimaryFieldName()=>$id]);
		}

		/**
		 * @param $condition
		 * @return int
		 */
		public function remove($condition){
			$collection = $this->getCollection();
			$collection->setSyncLevel(Collection::SYNC_STORE);
			$affected = $this->getCollection()->remove($condition);
			$collection->setSyncLevel();
			return $affected;
		}

		/**
		 * @param $id
		 * @return bool
		 */
		public function removeById($id){
			return !!$this->remove([$this->getPrimaryFieldName()=>$id]);
		}


		/**
		 * @param $data
		 * @param null $source
		 * @return int
		 */
		public function storageCreate($data, $source = null){
			if($source===null){
				$source = $this->getSource();
			}
			$storage = $this->getStorage();
			return $storage->create($data,$source);
		}


		/**
		 * @param null $condition
		 * @param null $offset
		 * @param null $limit
		 * @param array $options
		 * @return int
		 */
		public function storageCount($condition = null, $offset = null, $limit = null, array $options = null){
			return $this->getStorage()->count($condition,$this->getSource(), $offset, Collection::isInfinityLimit($limit)?null:$limit, $options);
		}

		/**
		 * @param $condition
		 * @param null $limit
		 * @param null $offset
		 * @param null $orderBy
		 * @param array $options
		 * @return ShipmentInterface
		 */
		public function storageLoad($condition, $limit = null, $offset = null, $orderBy = null,array $options = null){
			if($condition){
				$condition = $this->normalizeCondition($condition);
			}
			if($orderBy){
				$orderBy = $this->normalizeOrder($orderBy);
			}
			$columns = [];
			foreach($this->getFields() as $field){
				if($field->isOriginality()){
					$columns[] = $field->getOriginalKey();
				}
			}
			$manager = $this->getStorage();
			return $manager->select($columns,$this->getSource(), $condition, Collection::isInfinityLimit($limit)?null:$limit, $offset, $orderBy, $options);
		}

		/**
		 * @param $data
		 * @param $condition
		 * @return int
		 */
		public function storageUpdate($data, $condition){
			if($condition){
				$condition = $this->normalizeCondition($condition);
			}
			$store = $this->getWriteStorage();
			return $store->update($data,$condition,$this->getWriteSource());
		}

		/**
		 * @param $condition
		 * @return int
		 */
		public function storageRemove($condition){
			if($condition){
				if(!is_array($condition)){
					$condition = [$this->getPrimaryField()->getOriginalKey(),'=',$condition];
				}else{
					$condition = $this->normalizeCondition($condition);
				}
			}
			$store = $this->getWriteStorage();
			return $store->delete($condition, $this->getWriteSource());
		}

		/**
		 * @param $condition - self schema field name used
		 * @param $intermediateSchema
		 * @param $intermediateCondition - intermediate schema field name user
		 * @param $intermediateCollation - [INTERMEDIATE_FIELD_NAME => SELF_FIELD_NAME]
		 * @param null $limit
		 * @param null $offset
		 * @param null $orderBy - self schema field name used
		 * @param bool $result_intermediate
		 * @param null|string $result_intermediate_prefix - if $result_intermediate - required
		 * @return ShipmentInterface
		 */
		public function storageLoadThrough(
			$condition, $intermediateSchema, $intermediateCondition, $intermediateCollation, $limit = null, $offset = null, $orderBy = null,
			$result_intermediate = false, $result_intermediate_prefix = null
		){
			$store = $this->getStorage();
			$schemaManager = $this->getSchemaManager();
			$intermediateSchema = $schemaManager->getSchema($intermediateSchema);

			if(!$result_intermediate_prefix){
				$result_intermediate_prefix = 'intermediate_';
			}

			//prepare intermediate collation
			$intermediateOn = [];
			foreach($intermediateCollation as $intermediateField => $selfField){
				$intermediateOn[] = [
					[ 'intermediate', $intermediateField], '=', [ 'identifier' =>[ 'self', $selfField]]
				];
			}

			// prepare intermediate condition
			if($intermediateCondition){
				$intermediateOn = array_merge($intermediateOn,(array)$this->normalizeConditionFieldsBy(
					function($container, $key, $stringReturn = false) use($schemaManager){
						$container = 'intermediate';
						return $stringReturn?($container.'.'.$key):[$container,$key];
					},
					$intermediateCondition
				));
			}


			/**
			 * Prepare columns
			 */
			$columns = [];
			foreach($this->getFields() as $field){
				if($field->isOriginality()){
					$columns[$field->getOriginalKey()] = ['self',$field->getOriginalKey()];
				}
			}
			if($result_intermediate){
				foreach($intermediateSchema->getFields() as $field){
					if($field->isOriginality()){
						$columns[$result_intermediate_prefix . $field->getOriginalKey()] = [ 'intermediate', $field->getOriginalKey()];
					}
				}
			}
			$condition = $this->normalizeConditionFieldsBy(function($container, $key, $stringReturn = false){
				if(!$container){
					$container = 'self';
				}
				return $stringReturn?($container.'.'.$key):[$container,$key];
			},$condition);
			$intermediateOn = $this->normalizeCondition($intermediateOn,[
				'self' => $this,
				'intermediate' => $intermediateSchema
			]);

			if($orderBy){
				$o = [];
				foreach($this->normalizeOrder($orderBy) as $n => $d){
					$o['self.'.$n] = $d;
				}
				$orderBy = $o;
			}

			$shipment = $store->select($columns,$this->getSource(),$condition,Collection::isInfinityLimit($limit)?null:$limit,$offset,$orderBy,[
				'alias' => 'self',
				'joins' => [[
					            'table' => $intermediateSchema->getSource(),
					            'alias' => 'intermediate',
					            'on'    => $intermediateOn
				            ]],
			]);
			return $shipment;
		}

		/**
		 * @param $data
		 * @param $condition
		 * @param $intermediateSchema
		 * @param $intermediateCondition
		 * @param $intermediateCollation
		 * @return int
		 */
		public function storageUpdateThrough($data, $condition, $intermediateSchema, $intermediateCondition, $intermediateCollation){
			$store = $this->getStorage();
			$schemaManager = $this->getSchemaManager();
			$intermediateSchema = $schemaManager->getSchema($intermediateSchema);


			//prepare intermediate collation
			$intermediateOn = [];
			foreach($intermediateCollation as $intermediateField => $selfField){
				$intermediateOn[] = [
					[ 'intermediate', $intermediateField], '=', [ 'identifier' => $selfField ]
				];
			}

			// prepare intermediate condition
			if($intermediateCondition){
				$intermediateOn = array_merge($intermediateOn,(array)$this->normalizeConditionFieldsBy(
					function($container, $key, $stringReturn = false) use($schemaManager){
						return $stringReturn?('intermediate.'.$key):['intermediate',$key];
					},
					$intermediateCondition
				));
			}

			$condition = $this->normalizeConditionFieldsBy(function($container, $key, $stringReturn = false){
				return $stringReturn?($key):[null,$key];
			},$condition);

			$intermediateOn = $this->normalizeCondition($intermediateOn,[
				'intermediate' => $intermediateSchema
			]);


			$normalizedData = [];
			foreach($this->fields as $field){
				$key = $field->getName();
				if(array_key_exists($field->getName(),$data)){
					$normalizedData[$field->getOriginalKey()] = $data[$key];
				}
			}

			return $store->update($data,$condition,$this->getSource(),[
				'joins' => [[
		            'table' => $intermediateSchema->getSource(),
		            'alias' => 'intermediate',
		            'on'    => $intermediateOn
	            ]],
			]);
		}


		/**
		 * @param $condition
		 * @param $intermediateSchema
		 * @param $intermediateCondition
		 * @param $intermediateCollation
		 * @return int
		 */
		public function storageRemoveThrough($condition, $intermediateSchema, $intermediateCondition, $intermediateCollation){
			$store = $this->getStorage();
			$schemaManager = $this->getSchemaManager();
			$intermediateSchema = $schemaManager->getSchema($intermediateSchema);


			//prepare intermediate collation
			$intermediateOn = [];
			foreach($intermediateCollation as $intermediateField => $selfField){
				$intermediateOn[] = [ [ 'intermediate',$intermediateField ] , '=' , [ 'identifier' => $selfField ] ];
			}

			// prepare intermediate condition
			if($intermediateCondition){
				$intermediateOn = array_merge($intermediateOn,(array)$this->normalizeConditionFieldsBy(
					function($container, $key, $stringReturn = false) use($schemaManager){
						return $stringReturn?('intermediate.'.$key):['intermediate',$key];
					},
					$intermediateCondition
				));
			}

			$condition = $this->normalizeConditionFieldsBy(function($container, $key, $stringReturn = false){
				return $stringReturn?($key):[null,$key];
			},$condition);

			$intermediateOn = $this->normalizeCondition($intermediateOn,[
				'intermediate' => $intermediateSchema
			]);

			return $store->delete($condition,$this->getSource(),[
				'joins' => [[
					            'table' => $intermediateSchema->getSource(),
					            'alias' => 'intermediate',
					            'on'    => $intermediateOn
				            ]],
			]);
		}


		/**
		 * @param $id
		 * @return null|array
		 */
		public function storageLoadById($id){
			$result = $this->storageLoad([$this->getPrimaryField()->getName(),'=',$id],1);
			return $result?$result->asAssoc()->fetch():null;
		}

		/**
		 * @param $data
		 * @param $id
		 * @return bool
		 */
		public function storageUpdateById($data, $id){
			return !!$this->storageUpdate($data,[[$this->getPrimaryField()->getName(),'=',$id]]);
		}

		/**
		 * @param $id
		 * @return bool
		 */
		public function storageRemoveById($id){
			return !!$this->storageRemove([$this->getPrimaryField()->getName(),'=',$id]);
		}



		/**
		 * @param $orderBy
		 * @return array
		 */
		public function normalizeOrder($orderBy){
			if(!$orderBy){
				return null;
			}
			$a = [];
			foreach($orderBy as $name => $direction){
				if( ($field = $this->getField($name)) && $field->isOriginality()){
					$a[$field->getOriginalKey()] = $direction;
				}
			}
			return $a;
		}


		/**
		 * @param callable $handler
		 * @param $condition
		 * @return array
		 */
		public function normalizeConditionFieldsBy(callable $handler, $condition){
			if($condition){
				foreach($condition as $key => & $c){
					$s = is_string($key);
					$count = count($c);
					$block = false;
					if(!$s){
						$block = true;
						foreach($c as $i){
							if(!is_array($i)){
								$block = false;
							}
						}
					}
					if($block){
						$c = $this->normalizeConditionFieldsBy($handler, $c);
					}elseif($s){
						$operator = null;
						if(strpos($key,':')!==false){
							list($key, $operator) = explode(':',$key,2);
							if(!$operator)$operator = '=';
						}$right = $c;
						if(is_array($right) && isset($right['identifier'])){
							if(is_array($right['identifier'])){
								$right['identifier'] = call_user_func($handler,$right['identifier'][0], $right['identifier'][1]);
							}
						}
						unset($condition[$key]);
						$condition[] = [call_user_func($handler,null, $key, true),$operator, $right];
					}elseif($count === 3 || $count === 2){
						$left = isset($c[0])?$c[0]:$c['left'];
						$operator = isset($c[1])?$c[1]:$c['operator'];
						$right = isset($c[2])?$c[2]:$c['right'];

						if(is_array($left)){
							$left = call_user_func($handler,$left[0], $left[1]);
						}else{
							$left = call_user_func($handler,null, $left);
						}

						if(is_array($right) && isset($right['identifier'])){
							if(is_array($right['identifier'])){
								if(is_array($right['identifier'])){
									$right['identifier'] = call_user_func($handler, $right['identifier'][0] ,$right['identifier'][1]);
								}else{
									$right['identifier'] = call_user_func($handler, null ,$right['identifier']);
								}
							}
						}
						$c = [$left, $operator, $right];
					}
				}
			}
			return $condition;
		}

		/**
		 * @param $condition
		 * @param array $schemas
		 * @return mixed
		 */
		public function normalizeCondition($condition,array $schemas = []){
			$sManager = $this->getSchemaManager();
			foreach($condition as $key => & $c){
				$s = is_string($key);
				$count = count($c);
				$block = false;
				if(!$s){
					$block = true;
					foreach($c as $i){
						if(!is_array($i)){
							$block = false;
						}
					}
				}
				if($block){
					$c = $this->normalizeCondition($c);
				}elseif($s){
					$operator = null;
					if(strpos($key,':')!==false){
						list($key,$operator) = array_replace([null,$operator],explode(':',$key,2));
					}$right = $c;

					if(is_array($right) && isset($right['identifier'])){
						if(is_array($right['identifier'])){
							$right['identifier'][1] = $sManager->getSchema($schemas[$right['identifier'][0]])->getField($right['identifier'][1])->getOriginalKey();
						}
					}
					unset($condition[$key]);
					$condition[$this->getField($key)->getOriginalKey()] = $right;
				}elseif($count === 3 || $count === 2){
					$left = isset($c[0])?$c[0]:$c['left'];
					$operator = isset($c[1])?$c[1]:$c['operator'];
					$right = isset($c[2])?$c[2]:$c['right'];

					if(is_array($left)){
						$left[1] = $sManager->getSchema($schemas[$left[0]])->getField($left[1])->getOriginalKey();
					}else{
						$left = $this->getField($left)->getOriginalKey();
					}

					if(is_array($right) && isset($right['identifier'])){
						if(is_array($right['identifier'])){
							if(is_array($right['identifier'])){
								$right['identifier'][1] = $sManager->getSchema($schemas[$right['identifier'][0]])->getField($right['identifier'][1])->getOriginalKey();
							}else{
								$right['identifier'] = $this->getField($right['identifier'])->getOriginalKey();
							}
						}
					}
					$c = [$left, $operator, $right];
				}
				return $condition;
			}
			return $condition;
		}

		/**
		 * @param $name
		 * @return Field|null
		 */
		public function getField($name){
			if(!($field = parent::getField($name)) && $this->ancestor){
				return $this->ancestor->getField($name);
			}
			return $field;
		}

		/**
		 * @return array
		 */
		public function getFieldNames(){
			if(!$this->ancestor){
				return array_keys($this->field_indexes);
			}
			$names = $this->ancestor->getFieldNames();
			if($this->field_indexes){
				foreach(array_keys($this->field_indexes) as $name){
					if(!in_array($name,$names, true)){
						$names[] = $name;
					}
				}
			}
			return $names;
		}

		/**
		 * @param array|null $fieldNames
		 * @return Field[]
		 */
		public function getFields(array $fieldNames = null){
			if($fieldNames !== null){
				$fields = [];
				foreach($fieldNames as $name){
					$field = $this->getField($name);
					if($field){
						$fields[$this->getFieldIndex($name)] = $field;
					}
				}
				return $fields;
			}else{
				$fieldNames = $this->getFieldNames();
				$fields = [];
				foreach($fieldNames as $i => $name){
					$field = $this->getField($name);
					if($field){
						$fields[$i] = $field;
					}
				}
				return $fields;
			}
		}

		/**
		 * @param Schema $ancestor
		 * @return $this
		 */
		public function setAncestor(Schema $ancestor){
			$this->ancestor = $ancestor;
			return $this;
		}

		/**
		 * @return Schema
		 */
		public function getAncestor(){
			return $this->ancestor;
		}

		/**
		 * @return array
		 */
		public function getDerivedNames(){
			$o = $this;
			$names = [];
			do{
				$names[] = $o->name;
			}while(($o = $o->ancestor));
			return $names;
		}

		/**
		 * @return bool
		 */
		public function isRoot(){
			return !$this->ancestor;
		}

		/**
		 * @param $name
		 * @return static
		 */
		public function extend($name){
			$schema = new static($name);
			foreach($this->fields as $i => $field){
				$nf = clone $field;
				$nf->setSchema($schema);
				$schema->fields[$i] = $nf;
			}
			$schema->setAncestor($this);
			return $schema;
		}



		/**
		 * @param $name
		 * @param array $definition
		 * @return \Jungle\Data\Record\Head\Field
		 */
		public function field($name, $definition = null){
			if(is_array($definition)){
				$c = array_replace([
					'type'      => 'string',
				], $definition);
			}elseif($definition){
				$c = [
					'type' => $definition
				];
			}else{
				$c = [];
			}

			$field = $this->getField($name);
			if(!$field){
				$field = new Field($name,isset($c['type'])?$c['type']:'string');
				$field->setSchema($this);
			}
			$field->define($c);
			$this->addField($field);
			return $field;
		}

		/**
		 * @param $name
		 * @param array $definition
		 * @return Relation
		 * @throws Field\RelationError
		 */
		public function relation($name, array $definition){
			$field = $this->getField($name);
			if(!$field){
				$field = new Relation($name);
				$field->setSchema($this);
			}
			$field->define($definition);
			$this->addField($field);
			return $field;
		}

		/**
		 * @param $name
		 * @param $referencedSchema
		 * @param $fields
		 * @param $referencedFields
		 * @param array|null $options
		 * @return Relation
		 */
		public function belongsTo($name, $referencedSchema, $fields, $referencedFields, array $options = null){
			return $this->relation($name, array_replace([
				'type' => Relation::TYPE_BELONGS,
				'fields' => $fields,
				'referenced_fields' => $referencedFields,
				'referenced_schema' => $referencedSchema,
			], (array)$options));
		}

		/**
		 * @param $name
		 * @param $referencedSchema
		 * @param $fields
		 * @param $referencedFields
		 * @param array|null $options
		 * @return Relation
		 */
		public function hasOne($name, $referencedSchema, $fields, $referencedFields, array $options = null){
			return $this->relation($name, array_replace([
				'type' => Relation::TYPE_ONE,
				'fields' => $fields,
				'referenced_fields' => $referencedFields,
				'referenced_schema' => $referencedSchema,
			], (array)$options));
		}

		/**
		 * @param $name
		 * @param $referencedSchema
		 * @param $fields
		 * @param $referencedFields
		 * @param array|null $options
		 * @return Relation
		 */
		public function hasMany($name, $referencedSchema, $fields, $referencedFields, array $options = null){
			return $this->relation($name, array_replace([
				'type' => Relation::TYPE_MANY,
				'fields' => $fields,
				'referenced_fields' => $referencedFields,
				'referenced_schema' => $referencedSchema,
			], (array)$options));
		}

		/**
		 * @param $name
		 * @param $intermediateSchema
		 * @param $referencedSchema
		 * @param $fields
		 * @param $intermediateFields
		 * @param $intermediateReferencedFields
		 * @param $referencedFields
		 * @param array|null $options
		 * @return Relation
		 */
		public function hasManyToMany($name,
			$intermediateSchema,$referencedSchema, $fields,
			$intermediateFields, $intermediateReferencedFields, $referencedFields,
			array $options = null
		){
			return $this->relation($name, array_replace([
				'type' => Relation::TYPE_MANY_THROUGH,
				'fields' => $fields,
				'intermediate_fields' => $intermediateFields,
				'intermediate_referenced_fields' => $intermediateReferencedFields,
				'intermediate_schema' => $intermediateSchema,
				'referenced_fields' => $referencedFields,
				'referenced_schema' => $referencedSchema,
			], (array)$options));
		}

		/**
		 * @param $name
		 * @param $schemafield
		 * @param $fields
		 * @param null $defaultReferencedFields
		 * @param array $allowedSchemaNames
		 * @param array $schemasReferencedFields
		 * @param array|null $options
		 * @return Relation
		 */
		public function belongsToDynamic($name,
			$schemafield, $fields, $defaultReferencedFields = null,
			$allowedSchemaNames = [],
			$schemasReferencedFields = [],
			array $options = null
		){
			return $this->relation($name,array_replace_recursive([
				'type' => Relation::TYPE_BELONGS,
				'fields' => $fields,
				'referenced_fields' => $defaultReferencedFields,
				'dynamic' => true,
				'dynamic_config' => [
					'schemafield' => $schemafield,
					'schemas' => $allowedSchemaNames,
					'schemas_fields' => $schemasReferencedFields,
				]
			], (array)$options));
		}

		/**
		 * @param $name
		 * @param $referencedSchema
		 * @param $fields
		 * @param $referencedFields
		 * @param $referencedSchemafield
		 * @param array $referencedOppositeRelations
		 * @param array|null $options
		 * @return mixed
		 */
		public function hasOneDynamic($name,
			$referencedSchema,
			$fields,
			$referencedFields,
			$referencedSchemafield, $referencedOppositeRelations = [],
			array $options = null
		){
			return $this->define(array_replace_recursive([
				'type' => self::TYPE_ONE,
				'fields' => $fields,
				'referenced_fields' => $referencedFields,
				'referenced_schema' => $referencedSchema,
				'dynamic' => true,
				'dynamic_config' => [
					'referenced_schemafield' => $referencedSchemafield,
					'referenced_opposites' => $referencedOppositeRelations
				]
			], (array)$options));
		}

		public function hasManyDynamic($name,
			$referencedSchema,
			$fields,
			$referencedFields,
			$referencedSchemafield,
			$referencedOppositeRelations = [],
			array $options = null
		){
			return $this->relation($name, array_replace_recursive([
				'type' => Relation::TYPE_MANY,
				'fields' => $fields,
				'referenced_fields' => $referencedFields,
				'referenced_schema' => $referencedSchema,
				'dynamic' => true,
				'dynamic_config' => [
					'referenced_schemafield' => $referencedSchemafield,
					'referenced_opposites' => $referencedOppositeRelations
				]
			], (array)$options));
		}



		// TODO get related method
		// TODO re define get|setProperty method

		/**
		 * @param $name
		 * @param array $options
		 */
		public function specifyVirtualField($name, array $options){

		}

		/**
		 * @param $name
		 * @param $setterMethodName
		 */
		public function specifySetterFor($name, $setterMethodName){

		}

		/**
		 * @param $name
		 * @param $getterMethodName
		 */
		public function specifyGetterFor($name, $getterMethodName){

		}

		/**
		 * @param $singleRelationName
		 */
		public function useCompositeFetchingWith($singleRelationName){

		}

		/**
		 * @param callable $validator
		 */
		public function addValidator(callable $validator){

		}

		/**
		 * @param $behaviour
		 */
		public function addBehaviour($behaviour){

		}

	}

}

