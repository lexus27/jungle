<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:48
 */
namespace Jungle\Data\Foundation\Record\Head {

	use Jungle\Data\Foundation\Record;
	use Jungle\Data\Foundation\Record\Collection;
	use Jungle\Data\Foundation\Record\DataMap;
	use Jungle\Data\Foundation\Record\Head\Field;
	use Jungle\Data\Foundation\Record\Head\Field\Relation;
	use Jungle\Data\Foundation\Schema\OuterInteraction\Mapped\Schema as MappedSchema;
	use Jungle\Data\Foundation\ShipmentInterface;
	use Jungle\Data\Foundation\Storage;
	use Jungle\Data\Foundation\Storage\StorageInterface;
	
	/**
	 * Class Schema
	 * @package modelX
	 *
	 * @property Field[]    $fields
	 * @method Field        getField($key)
	 * @method Field        getPrimaryField()
	 *
	 */
	class Schema extends MappedSchema{

		/** @var  string */
		protected $name;

		/** @var  string */
		protected $source;

		/** @var  StorageInterface */
		protected $storage;

		/** @var  Field[] */
		protected $fields = [ ];

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

		/**
		 * Universal instantiate class by class name from value specified in field
		 * @var null|string
		 */
		protected $dynamic_record_class_field;



		/**
		 * Schema constructor.
		 * @param $name
		 */
		public function __construct($name){
			$this->setName($name);
			return $this;
		}

		/**
		 * @param array|null $fieldNames
		 * @return Field[]
		 */
		public function getFields(array $fieldNames = null){
			if($fieldNames!==null){
				$fs = [];
				foreach($this->fields as $f){
					$i = array_search($f->getName(),$fieldNames,true);
					if($i !==false){
						$fs[$i] = $f;
					}
				}
				return $fs;
			}
			return parent::getFields();
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
				$this->collection = new Collection();
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
		 *
		 */
		protected function _initCache(){
			$names_fill = $this->_names===null;
			if($names_fill) $this->_names = [];

			$original_names_fill = $this->original_names===null;
			if($original_names_fill) $this->original_names = [];

			$enumerable_fill = $this->enumerable_names === null;
			if($enumerable_fill) $this->enumerable_names = [];

			$readonly_fill = $this->readonly_names === null;
			if($readonly_fill) $this->readonly_names = [];

			$private_fill = $this->private_names === null;
			if($private_fill) $this->private_names = [];

			if($enumerable_fill || $names_fill || $original_names_fill || $readonly_fill || $private_fill){
				foreach($this->fields as $field){
					if($names_fill){
						$this->_names[] = $field->getName();
					}
					if($enumerable_fill && $field->isEnumerable()){
						$this->enumerable_names[] = $field->getName();
					}
					if($original_names_fill && $field->isOriginality()){
						$this->original_names[] = $field->getOriginalKey();
					}
					if($private_fill && $field->isPrivate()){
						$this->original_names[] = $field->getName();
					}
					if($readonly_fill && $field->isReadonly()){
						$this->original_names[] = $field->getName();
					}
				}
			}
		}

		public function getReadonlyNames(){
			$this->_initCache();
			return $this->readonly_names;
		}

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
			return $this->dynamic_update;
		}

		/**
		 * @return bool
		 */
		public function isDynamicRecordClass(){
			return !!$this->dynamic_record_class_field;
		}

		/**
		 * @param $field_name
		 * @return $this
		 */
		public function setDynamicRecordClassField($field_name){
			$this->dynamic_record_class_field = $field_name;
			return $this;
		}
		
		/**
		 * @return null|string
		 */
		public function getDynamicRecordClassField(){
			return $this->dynamic_record_class_field;
		}



		/**
		 * @param StorageInterface $storage
		 * @return $this
		 */
		public function setStorage(StorageInterface $storage){
			$this->storage = $storage;
			return $this;
		}

		/**
		 * @return StorageInterface
		 */
		public function getStorage(){
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
			return $this->schema_manager;
		}

		/**
		 * @param $orderBy
		 * @return mixed
		 */
		public function normalizeOrder($orderBy){

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
		 * @param $condition
		 * @param null $limit
		 * @param null $offset
		 * @param null $orderBy
		 * @return Collection
		 */
		public function load($condition, $limit = null, $offset = null, $orderBy = null){
			return $this->getCollection()->extend($condition, $limit,$offset,$orderBy);
		}

		/**
		 * @param $condition
		 * @param null $offset
		 * @param null $orderBy
		 * @return Record|null
		 */
		public function loadFirst($condition, $offset = null, $orderBy = null){
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
			return !!$this->remove([$this->getPrimaryField()->getName()=>$id]);
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
			$normalizedData = [];
			foreach($this->fields as $field){
				$key = $field->getName();
				if(array_key_exists($field->getName(),$data)){
					$normalizedData[$field->getOriginalKey()] = $data[$key];
				}
			}
			if($condition){
				$condition = $this->normalizeCondition($condition);
			}
			$store = $this->getWriteStorage();
			return $store->update($normalizedData,$condition,$this->getWriteSource());
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
			return !!$this->storageUpdate($data,[$this->getPrimaryField()->getName(),'=',$id]);
		}

		/**
		 * @param $id
		 * @return bool
		 */
		public function storageRemoveById($id){
			return !!$this->storageRemove([$this->getPrimaryField()->getName(),'=',$id]);
		}







		/** @var  Record */
		protected $not_initialized_record;

		/**
		 * @param Relation $field
		 * @return Relation[]
		 */
		public function getOppositeRelationsFor(Relation $field){
			$fields = [];
			foreach($this->fields as $f){
				if($f instanceof Relation && Relation::isOppositeRelations($f,$field)){
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
			foreach($this->fields as $f){
				if($f instanceof Relation && Relation::isIntermediateRelations($f,$field)){
					$fields[] = $f;
				}
			}
			return $fields;
		}

		/**
		 * @param Record $record
		 */
		public function markInitialized(Record $record){
			if($this->not_initialized_record){
				if($this->not_initialized_record === $record){
					$this->not_initialized_record = null;
				}
			}
		}

		/**
		 * @return DataMap
		 */
		protected function _factory(){
			return new DataMap($this);
		}

		/**
		 * @param $data
		 * @param int $operationMade
		 * @return Record
		 */
		public function initializeRecord($data = null, $operationMade = null){
			if($data === null){
				if($this->not_initialized_record){
					$this->not_initialized_record->setOperationMade(Record::OP_CREATE);
					$this->not_initialized_record->setOriginalData(null);
					return $this->not_initialized_record->initRecComplete();
				}else{
					return $this->_factory();
				}
			}
			if(!$this->not_initialized_record){
				$this->not_initialized_record = $this->_factory();
				$this->not_initialized_record->initRecStart();
			}
			$this->not_initialized_record->setOriginalData($data);
			$this->not_initialized_record->setOperationMade($operationMade?:Record::OP_UPDATE);
			return $this->not_initialized_record;
		}

	}

}

