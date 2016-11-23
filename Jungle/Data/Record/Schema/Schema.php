<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:48
 */
namespace Jungle\Data\Record\Schema {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Collection;
	use Jungle\Data\Record\DataMap;
	use Jungle\Data\Record\Field\Field;
	use Jungle\Data\Record\Model;
	use Jungle\Data\Record\Relation\Relation;
	use Jungle\Data\Record\Repository;
	use Jungle\Data\Record\Validation\Validation;
	use Jungle\Data\Record\Validation\ValidationCollector;
	use Jungle\Data\Storage\Exception;
	use Jungle\Util\Data\Condition\Condition;
	use Jungle\Util\Data\Schema\OuterInteraction\ValueAccessAwareInterface;
	use Jungle\Util\Data\ShipmentInterface;
	use Jungle\Util\Data\Storage;
	use Jungle\Util\Data\Storage\StorageInterface;

	/**
	 * Class Schema
	 * @package modelX
	 */
	class Schema implements ValueAccessAwareInterface{

		const IDENTITY_SOURCE = 'source';
		const IDENTITY_SCHEMA = 'schema';
		const IDENTITY_CRC32  = 'crc32';

		/** @var  string */
		public $name;

		/** @var  \ReflectionClass */
		protected $reflection;

		/** @var  Repository */
		protected $repository;

		/** @var  Schema */
		protected $ancestor;


		/** @var  string */
		public $pk;

		/** @var bool  */
		public $pk_auto_generation = true;

		/** @var  Field[] */
		public $fields = [];

		/** @var  Relation[]  */
		public $relations = [];

		/** @var  UniqueKey[] */
		protected $uniques = [];



		/** @var  Validation[] */
		protected $validations = [];

		/** @var array  */
		protected $behaviours = [];

		/**
		 * Символизирует то что запись создается посредством Schema::makeRecord
		 * Этот параметр используется из конструктора записи для определения состояния создаваемого объекта
		 * Если этот параметр равен лжи, то создаваемые объекты будут сразу же помечены состоянием @see \Jungle\Data\Record::STATE_NEW
		 * Если правда, то объекты ждут своей иницилизации в Коллекции,
		 * @var bool
		 */
		protected $record_making = false;

		/** @var  Collection */
		protected $collection;








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
		protected $boot_field;



		/** @var string  */
		protected $identity_take = self::IDENTITY_SOURCE;

		/** @var  string|null */
		protected $identity;

		/** @var array  */
		protected $derivative_schemas = [];

		/** @var array  */
		protected $ignore_enum = [];


		/** @var  string */
		protected $source;

		/** @var  StorageInterface */
		protected $storage;


		/** @var array  */
		public $mapping = [];


		/**
		 * @param $pk
		 * @param bool|true $auto_generate
		 * @return $this
		 */
		public function setPk($pk, $auto_generate = true){
			$this->pk = $pk?:($this->fields?key($this->fields):null);
			$this->pk_auto_generation = $auto_generate;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getPk(){
			return $this->pk;
		}

		/**
		 * @return Field|null
		 */
		public function getPkField(){
			return isset($this->fields[$this->pk])?$this->fields[$this->pk]:null;
		}

		/**
		 * @return string
		 */
		public function getPkOriginal(){
			return $this->mapping[$this->pk];
		}

		/**
		 * @param $key
		 * @return \Jungle\Data\Record\Field\Field|null
		 */
		public function getFieldByOriginalKey($key){
			if($i = array_search($key, $this->mapping, true) !== false){
				return $this->fields[$i];
			}
			return null;
		}



		/**
		 * Schema constructor.
		 * @param $name
		 */
		public function __construct($name){
			$this->setName($name);

			// сначала проверяем на NULL потом проверяем Тип
			$this->validations = [
				new Record\Validation\CheckNullable(),
				new Record\Validation\CheckField()
			];

		}

		/**
		 * @param array $field_map
		 * @param bool $merge
		 * @return $this
		 */
		public function setMapping(array $field_map, $merge = true){
			$this->mapping = $merge?array_replace($this->mapping, $field_map):$field_map;
			return $this;
		}

		/**
		 * @param $field
		 * @param $original_key
		 * @return $this
		 */
		public function setMap($field, $original_key){
			$this->mapping[$field] = $original_key;
			return $this;
		}

		/**
		 * @param $field_name
		 * @return mixed
		 */
		public function getOriginal($field_name){
			return isset($this->mapping[$field_name])?$this->mapping[$field_name]:$field_name;
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
		 * @param Record $record
		 * @return $this
		 */
		public function setFlyweight(Record $record){
			$this->flyweight_record = $record;
			return $this;
		}


		/**
		 * Проверка, действует ли эта схема в базе данных, или является просто носителем мета-данных
		 * @return bool
		 */
		public function isActual(){
			return $this->storage !== null && $this->source !== null;
		}


		/**
		 * @return bool
		 */
		public function isAbstract(){
			return $this->reflection->isAbstract();
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
				// Наследуем коллекцию если предок является актуальным
				if($this->ancestor && $this->ancestor->isActual() &&
				   $this->source === $this->ancestor->source // проверяем что используются в одном и том же источнике
				){
					$ancestor_collection    = $this->ancestor->getCollection();
					if($this->boot_field){

						$boot_field = $this->getBootField();
						$boot_value = $this->getIdentity();

						$this->collection = $ancestor_collection->extend([
							[ $boot_field , '=' , $boot_value ]
						]);
					}else{
						// если нет загрузочного поля то и не зачем наследоваться
						$this->collection = $this->_factory_collection();
					}
				}else{
					$this->collection = $this->_factory_collection();
				}
				$this->collection->setSchema($this);
			}
			return $this->collection;
		}

		/**
		 * @return Collection
		 */
		protected function _factory_collection(){
			// создаем базовую(root) коллекцию для текущей схемы
			return new Collection();
		}

		/**
		 * @return array
		 */
		public function getDefaultAttributes(){
			$a = [];
			foreach($this->fields as $name => $field){
				$a[$name] = $field->default;
			}
			return $a;
		}

		/**
		 * @return array
		 */
		public function getOriginalNames(){
			return array_values($this->mapping);
		}


		/**
		 * @return array|null
		 */
		public function getEnumerableNames(){
			return array_diff(array_keys($this->fields), $this->ignore_enum);
		}



		/**
		 * @param array $fields
		 */
		public function ignoreEnum(array $fields){
			$this->ignore_enum = array_merge($this->ignore_enum, $fields);
		}

		/**
		 * @param Record $record
		 * @param $fieldKey
		 * @param $value
		 * @return mixed
		 */
		public function stabilize(Record $record, $fieldKey, $value){
			return $this->fields[$fieldKey]->stabilize($value);
		}


		/**
		 * @param Validation $validator
		 */
		public function addValidator(Validation $validator){
			$this->validations[] = $validator;
		}

		/**
		 * @param $field_name
		 * @param $validatorType
		 * @return Validation|null
		 */
		public function getValidationFor($field_name, $validatorType){
			foreach($this->validations as $v){
				if($v->type === $validatorType && in_array($field_name, $v->fields())){
					return $v;
				}
			}
			return null;
		}

		/**
		 * @return array|Record\Validation\Validation[]
		 */
		public function getValidations(){
			return $this->validations;
		}

		public function beginTransaction(){
			$this->getDefaultStorage()->begin();
		}

		public function commitTransaction(){
			$this->getDefaultStorage()->commit();
		}

		public function rollbackTransaction(){
			$this->getDefaultStorage()->rollback();
		}

		/**
		 * @param Record $record
		 * @param ValidationCollector $validator_collector
		 * @return ValidationCollector
		 */
		public function validate(Record $record, ValidationCollector $validator_collector = null){
			if(!$validator_collector){
				$validator_collector = new ValidationCollector();
			}
			foreach($this->validations as $validator){
				$validator->validate($record,$validator_collector);
			}
			return $validator_collector;
		}




		/**
		 * @param Repository $schemaManager
		 * @return $this
		 */
		public function setRepository($schemaManager){
			$this->repository = $schemaManager;
			return $this;
		}

		/**
		 * @return Repository
		 */
		public function getRepository(){
			return $this->repository;
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
		public function getAncestorNames(){
			$names = [];$o = $this;
			do{ $names[] = $o->name; }while(($o = $o->ancestor));
			return $names;
		}


		/**
		 * @param array $derivative_schemas
		 */
		public function setDerivativeSchemas(array $derivative_schemas = []){
			// Определение наследников для загрузки из данной схемы,
			// определяет какие схемы будут подставленны в условие выборки относительно
			// этой схемы если у нее есть наследники которые следует
			$this->derivative_schemas = $derivative_schemas;
		}

		/**
		 * @return array
		 */
		public function getDerivedIdentity(){
			$names = [];$o = $this;
			do{ $names[] = $o->getIdentity(); }while(($o = $o->ancestor));
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
			$schema = clone $this;
			$schema->name   = $name;
			$schema->setAncestor($this);
			return $schema;
		}


		public function __clone(){
			$this->identity = null;
			foreach($this->relations as & $relation){
				$relation = clone $relation;
				$relation->schema = $this;
			}
		}





		/**
		 * @param Record $record
		 */
		public function onCreate(Record $record){
			$this->getCollection()->onItemCreated($record);
			$record->onCreate();
			$record->onSave();
			// behaviours handle
			foreach($this->behaviours as $behaviour){
				if(is_object($behaviour) && method_exists($behaviour,'onCreate')){
					call_user_func([$behaviour,'onCreate'],$record, $this);
				}
			}
		}

		/**
		 * @param Record $record
		 */
		public function onUpdate(Record $record){
			$this->getCollection()->onItemUpdated($record);
			$record->onUpdate();
			$record->onSave();
			// behaviours handle
			foreach($this->behaviours as $behaviour){
				if(is_object($behaviour) && method_exists($behaviour,'onUpdate')){
					call_user_func([$behaviour,'onUpdate'],$record, $this);
				}
			}
		}

		/**
		 * @param $record
		 */
		public function onDelete(Record $record){
			$this->getCollection()->onItemDeleted($record);
			$record->onDelete();

			// behaviours handle
			foreach($this->behaviours as $behaviour){
				if(is_object($behaviour) && method_exists($behaviour,'onDelete')){
					call_user_func([$behaviour,'onDelete'],$record, $this);
				}
			}
		}


		/**
		 * @param Record $record
		 * @return bool
		 */
		public function beforeCreate(Record $record){

			// behaviours handle
			foreach($this->behaviours as $behaviour){
				if(is_object($behaviour) && method_exists($behaviour,'beforeCreate')){
					if(call_user_func([$behaviour,'beforeCreate'],$record, $this) === false){
						return false;
					}
				}
			}

			if($record->beforeSave() === false){
				return false;
			}
			if($record->beforeCreate() === false){
				return false;
			}
			return true;
		}

		/**
		 * @param Record $record
		 * @return bool
		 */
		public function beforeUpdate(Record $record){
			// behaviours handle
			foreach($this->behaviours as $behaviour){
				if(is_object($behaviour) && method_exists($behaviour,'beforeUpdate')){
					if(call_user_func([$behaviour,'beforeUpdate'],$record, $this) === false){
						return false;
					}
				}
			}

			if($record->beforeSave() === false){
				return false;
			}
			if($record->beforeUpdate() === false){
				return false;
			}
			return true;
		}

		/**
		 * @param Record $record
		 * @return bool
		 */
		public function beforeDelete(Record $record){
			// behaviours handle
			foreach($this->behaviours as $behaviour){
				if(is_object($behaviour) && method_exists($behaviour,'beforeDelete')){
					if(call_user_func([$behaviour,'beforeDelete'],$record, $this) === false){
						return false;
					}
				}
			}
			return $record->beforeDelete()!==false;
		}


		/**
		 * @param Record $record
		 */
		public function afterFetch(Record $record){
			$record->afterFetch();

			// behaviours handle
			foreach($this->behaviours as $behaviour){
				if(is_object($behaviour) && method_exists($behaviour,'afterFetch')){
					call_user_func([$behaviour,'afterFetch'],$record, $this);
				}
			}

		}



		/**
		 * @param array $data
		 * @param null $raw
		 * @return mixed raw data
		 */
		public function encodeRaw(array $data, $raw = null){
			// Значение проходит модификацию по полю только в случае если оно не NULL
			if(!$raw)$raw = [];

			/** @var Field[] $fields */
			$fields = array_intersect_key($this->fields, $data);
			foreach($fields as $name => $field){
				if(isset($data[$name])){
					$raw[$this->mapping[$name]] = $field->encode($data[$name]);
				}else{
					$raw[$this->mapping[$name]] = null;
				}
			}
			return $raw;
		}

		/**
		 * @param $raw
		 * @return array
		 */
		public function decodeRaw($raw){
			// Значение проходит модификацию по полю только в случае если оно не NULL
			$a = [];
			foreach($this->fields as $name => $field){
				if(isset($raw[ ($mapName = $this->mapping[$name]) ])){
					$a[$name] = $field->decode($raw[$mapName]);
				}else{
					$a[$name] = null;
				}
			}
			return $a;
		}





		/**
		 * @param $data
		 * @param $key
		 * @return mixed
		 */
		public function valueAccessGet($data, $key){
			// Значение проходит модификацию по полю только в случае если оно не NULL
			if(!$data) return null;
			if(isset($data[ ($mapping = $this->mapping[$key]) ])){
				return $this->fields[$key]->decode($data[$mapping]);
			}else{
				return null;
			}
		}

		/**
		 * @param $data
		 * @param $key
		 * @param $value
		 * @return mixed
		 */
		public function valueAccessSet($data, $key, $value){
			// Значение проходит модификацию по полю только в случае если оно не NULL
			if(!$data) $data = [];
			if(isset($value)){
				$data[$this->mapping[$key]] = $this->fields[$key]->encode($value);
			}else{
				$data[$this->mapping[$key]] = null;
			}
			return $data;
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function valueAccessExists($key){
			return isset($this->fields[$key]);
		}


		/**
		 * @param Record $record
		 * @param \Jungle\Data\Record\Field\Field $pk_field
		 * @param StorageInterface $store
		 * @param $source
		 * @return float|int|null|string
		 */
		public function pkBeforeCreate(Record $record, Record\Field\Field $pk_field, StorageInterface $store, $source){
			return null;
		}

		/**
		 * @return bool
		 */
		public function isRecordMaking(){
			return $this->record_making;
		}

		/**
		 * @param Relation $relation
		 */
		public function setRelation(Relation $relation){
			$this->relations[$relation->name] = $relation;
			$relation->schema = $this;
		}

		/**
		 * @return Relation[]
		 */
		public function getRelations(){
			return $this->relations;
		}

		/**
		 * @param $name
		 * @return Relation
		 */
		public function getRelation($name){
			return isset($this->relations[$name])?$this->relations[$name]:null;
		}


		/**
		 * @param string $name
		 * @return Field|null
		 */
		public function getField($name){
			return isset($this->fields[$name])?$this->fields[$name]:null;
		}

		/**
		 * @return array
		 */
		public function getFieldNames(){
			return array_keys($this->fields);
		}


		/**
		 * @param Relation|Field|Validation $object
		 */
		public function set($object){
			if($object instanceof Relation){
				$this->setRelation($object);
			}elseif($object instanceof Field){
				$this->setField($object);
			}elseif($object instanceof Validation){
				$this->addValidator($object);
			}
		}

		/**
		 * @param Relation|Field|Validation $relation_or_field
		 */
		public function __invoke($relation_or_field){
			if($relation_or_field instanceof Relation){
				$this->setRelation($relation_or_field);
			}elseif($relation_or_field instanceof Field){
				$this->setField($relation_or_field);
			}elseif($relation_or_field instanceof Validation){
				$this->addValidator($relation_or_field);
			}
		}

		/**
		 * @param Field $field
		 * @return $this
		 */
		public function setField(Field $field){
			if(!$field->name){
				throw new \LogicException('Field passed without name');
			}
			$this->fields[$field->name] = $field;
			return $this;
		}

		/**
		 * @param array|null $fieldNames
		 * @return Field[]
		 */
		public function getFields(array $fieldNames = null){
			if($fieldNames !== null){
				return array_intersect_key($this->fields, array_flip($fieldNames));
			}else{
				return $this->fields;
			}
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
		 * @param $className
		 * @return bool
		 */
		public function isDerivativeRecordClassName($className){
			return is_a($className,$this->record_classname,true);
		}


		/**
		 * @param Schema|string $schema
		 * @return bool
		 */
		public function isDerivativeFrom($schema){
			if($schema instanceof Schema){
				$schema = $schema->name;
			}
			if($schema === $this->name) return true;
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
		 * @param $field_name
		 * @return $this
		 */
		public function setBootField($field_name){
			$this->boot_field = $field_name;
			return $this;
		}

		/**
		 * @return null|string
		 */
		public function getBootField(){
			return $this->boot_field;
		}

		/**
		 * @param $id
		 * @return $this
		 */
		public function setIdentity($id){
			$this->identity = $id;
			return $this;
		}

		public function setIdentityTake($take){
			$this->identity_take = $take;
		}

		/**
		 * @return string
		 */
		public function getIdentity(){
			if(!$this->identity){
				switch($this->identity_take){
					case self::IDENTITY_SCHEMA: return $this->identity = $this->name;
					case self::IDENTITY_SOURCE: return $this->identity = $this->source;
					case self::IDENTITY_CRC32: return $this->identity = crc32($this->name);
				}
			}
			return $this->identity;
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
		 * @throws \Exception
		 */
		public function getDefaultSource(){
			if(!$this->source){
				throw new \Exception('Source is not specified for "'.$this->name.'" schema ');
			}
			return $this->source;
		}

		/**
		 * @param Record $record
		 * @return string
		 * @throws \Exception
		 */
		public function getSource(Record $record){
			$source = $record->getSource();
			if($source){
				return $source;
			}
			if($this->source){
				return $this->source;
			}else{
				throw new \Exception('Source is not specified for "'.$this->name.'" schema ');
			}
		}

		/**
		 * @param Record|null $record
		 * @return string
		 * @throws \Exception
		 */
		public function getWriteSource(Record $record){
			$source = $record->getWriteSource();
			if($source){
				return $source;
			}
			if($this->source){
				return $this->source;
			}else{
				throw new \Exception('Source is not specified for "'.$this->name.'" schema ');
			}
		}

		/**
		 * @param $storage
		 * @return $this
		 */
		public function setStorage($storage){
			$this->storage = $storage;
			return $this;
		}


		/**
		 * @return StorageInterface
		 */
		public function getDefaultStorage(){
			return $this->storage instanceof StorageInterface? $this->storage:$this->_checkout_storage($this->storage);
		}

		/**
		 * @param Record $record
		 * @return StorageInterface
		 * @throws \Exception
		 */
		public function getStorage(Record $record){
			$storage = $record->getStorageService();
			if($storage){
				return $storage instanceof StorageInterface? $storage:$this->_checkout_storage($storage);
			}
			if($this->storage){
				return $this->storage instanceof StorageInterface? $this->storage:$this->_checkout_storage($this->storage);
			}else{
				throw new \Exception('Storage is not specified for "'.$this->name.'" schema ');
			}
		}

		/**
		 * @param Record|null $record
		 * @return StorageInterface
		 */
		public function getWriteStorage(Record $record){
			$storage = $record->getWriteStorageService();
			if($storage){
				return $storage instanceof StorageInterface? $storage:$this->_checkout_storage($storage);
			}
			if($this->storage){
				return $this->storage instanceof StorageInterface? $this->storage:$this->_checkout_storage($this->storage);
			}elseif($this->ancestor){
				return $this->ancestor->getWriteStorage($record);
			}
			return null;
		}

		/**
		 * @param $storage
		 * @return StorageInterface|null
		 * @throws \Exception
		 */
		protected function _checkout_storage($storage){
			if($storage){
				return $this->repository->getStorageService($storage);
			}
			throw new \Exception('Storage is not specified for "'.$this->name.'" schema ');
		}

		/**
		 * @param Record $record
		 */
		public function justConstruct(Record $record){
			if($this->boot_field){
				$record->{$this->boot_field} = $this->getIdentity();
			}
			$record->onConstruct();
		}


		/**
		 * @param Record $record запись присутствует если инициализация схемы происходит из конструктора
		 */
		public function initialize(Record $record = null){

			/** @var Model $class_name */
			$class_name = $this->record_classname;
			$class_name::initialize($this);

			$this->reflection = new \ReflectionClass($this->record_classname);


			// нормализуем PrimaryKey
			$this->pk = $this->pk?: key($this->fields) ;

			// Нормализуем маппинг полей
			$mapping = [];
			foreach($this->fields as $k => $f){
				$mapping[$k] = isset($this->mapping[$k])?$this->mapping[$k]:$k;
			}
			$this->mapping = $mapping;


			// Даем команду инициализировать нужное состояние поля-отношения
			$this->foreign_dependency = [];
			foreach($this->relations as $name => $relation){
				if($relation instanceof Record\Relation\RelationForeign){
					foreach($relation->getLocalFields() as $f){
						$this->foreign_dependency[$f] = $name;
					}
				}
				$relation->initialize();
			}

			if(!$this->isAbstract()){

				// NOTICE: Для DATAMAP приспособленец не должен использоваться для конфигурирования
				// DataMap не может поддерживать boot_field


				// ---- получаем настройки используя приспособленца ----

				// Приспособленец может быть ранее создан в случае "создания экземпляра"
				if($record){
					$this->flyweight_record = $record;
				}else{
					$record = $this->flyweight_record = $this->_instantiate($this->record_classname);
				}

				// загружаем из преспособленца данные по загрузочному полю
				if(!$this->boot_field && $s = $record->getBootField()){
					$this->boot_field = $s;
				}


				// выставляем настройки для хранилища если у этой схемы они не выставленны
				if(!$this->source && $s = $record->getSource()){
					$this->setSource($record->getSource());
				}
				if(!$this->storage && $s = $record->getStorageService()){
					$this->setStorage($s);
				}

				// здесь можно слить конфигурацию из предка для свойств которые так и не были определены
			}


		}



		/**
		 * @param $className
		 * @return DataMap
		 */
		protected function _instantiate($className){
			return new $className();
		}

		/**
		 * @param $data
		 * @param int $record_state
		 * @param bool $_without_derivative_match
		 * @return Record
		 * @throws \Exception
		 */
		public function makeRecord($data = null, $record_state = null, $_without_derivative_match = false){

			if(!in_array($record_state, [Record::STATE_NEW, Record::STATE_LOADED, null])){
				throw new \Exception(__METHOD__ . ' $record_state must be Record::STATE_TRANSIENT | Record::STATE_PERSISTENT | null');
			}

			if($_without_derivative_match){
				$schemaName = $this->name;
			}else{
				// Вычисляем по $data - имя схемы в которой запись далее будет инициализироваться
				$schemaName = $this->_matchBootSchema($data);
			}

			if($this->name === $schemaName){
				try{

					$this->record_making = true;

					if($data === null){

						// Создаем переходящую запись или получаем из существующей
						if($this->flyweight_record){
							$record = $this->flyweight_record;
							$record->setInitialized(false);
							$this->initializeRecord($record);
						}else{
							$record = $this->_instantiate($schemaName);
						}

						$record->setRecordState(Record::STATE_NEW, null);

					}else{

						// Создаем устойчивую запись или получаем из существующей
						if($this->flyweight_record){
							$record = $this->flyweight_record;
							$record->setInitialized(false);
						}else{
							$record = $this->_instantiate($schemaName);
							$record->setInitialized(false);
							$this->flyweight_record = $record;
						}

						$record->setRecordState($record_state?:Record::STATE_LOADED, $data);

					}

					return $record;

				}finally{
					$this->record_making = false;
				}
			}else{
				// Ищем схему по полученному имени схемы из данных в методе _matchBootSchema
				$schema = $this->getRepository()->getSchema($schemaName);

				if(!$schema->isDerivativeFrom($this)){
					throw new \Exception('Schema "'.$schemaName.'" is not derivative from "'.$this->name.'"');
				}
				return $schema->makeRecord($data, $record_state, true );
			}
		}


		/**
		 * @param Record $record
		 * @param array|null $_data
		 */
		public function initializeRecord(Record $record,array $_data = null){
			if($this->flyweight_record){
				if($this->flyweight_record === $record){
					$this->flyweight_record = null;
				}
			}
			$record->setInitialized(true,$_data);
		}



		/**
		 * @param null $data
		 * @return string
		 * @throws \Exception
		 */
		protected function _matchBootSchema($data = null){
			if($data && $this->boot_field){
				$schemaName = $this->valueAccessGet($data,$this->boot_field);
				if($schemaName !== null){
					return $schemaName;
				}
			}
			return $this->record_classname;
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
			if($condition!==null && !is_array($condition)){
				$condition = [$this->getPk() =>$condition];
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
			return !!$this->update($data, [$this->getPk() =>$id]);
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
			return !!$this->remove([$this->getPk() =>$id]);
		}






		/**
		 * @param null $condition
		 * @param null $offset
		 * @param null $limit
		 * @param array $options
		 * @param null $_source
		 * @param StorageInterface $_store
		 * @return int
		 * @throws Exception\FieldValueException
		 * @throws Exception\Operation
		 * @throws \Exception
		 */
		public function storageCount($condition = null, $offset = null, $limit = null, array $options = null, $_source = null, StorageInterface $_store = null){
			if(!$_source) $_source = $this->getDefaultSource();
			if(!$_store) $_store = $this->getDefaultStorage();
			if(!$_source) throw new \Exception('Source is not defined');
			try{
				return $_store->count($condition,$_source, $offset, Collection::isInfinityLimit($limit)?null:$limit, $options);
			}catch(Exception\Operation $e){
				$this->handleStorageOperationException($e);
				return false;
			}
		}

		/**
		 * @param $data
		 * @param null $_source
		 * @param StorageInterface $_store
		 * @return int
		 * @throws Exception\FieldValueException
		 * @throws Exception\Operation
		 * @throws \Exception
		 */
		public function storageCreate($data, $_source = null, StorageInterface $_store = null){
			if(!$_source) $_source = $this->getDefaultSource();
			if(!$_store) $_store = $this->getDefaultStorage();
			if(!$_source) throw new \Exception('Source is not defined');
			try{
				return $_store->create($data,$_source);
			}catch(Exception\Operation $e){
				$this->handleStorageOperationException($e);
				return false;
			}
		}

		/**
		 * @param $condition
		 * @param null $limit
		 * @param null $offset
		 * @param null $orderBy
		 * @param array $options
		 * @param $_source
		 * @param StorageInterface $_store
		 * @return ShipmentInterface
		 * @throws Exception\FieldValueException
		 * @throws Exception\Operation
		 * @throws \Exception
		 * @internal param StorageInterface $storage
		 */
		public function storageLoad(
			$condition, $limit = null, $offset = null, $orderBy = null,array $options = null,
			$_source = null, StorageInterface $_store = null
		){

			if(!$_source) $_source = $this->getDefaultSource();
			if(!$_store) $_store = $this->getDefaultStorage();
			if(!$_source) throw new \Exception('Source is not defined');


			if($condition){
				$condition = $this->normalizeCondition($condition);
			}
			if($orderBy){
				$orderBy = $this->normalizeOrder($orderBy);
			}
			$columns = array_values($this->mapping);
			try{
				return $_store->select($columns,$_source, $condition, Collection::isInfinityLimit($limit)?null:$limit, $offset, $orderBy, $options);
			}catch(Exception\Operation $e){
				$this->handleStorageOperationException($e);
				return false;
			}
		}

		/**
		 * @param $data
		 * @param $condition
		 * @param null $_source
		 * @param StorageInterface $_store
		 * @return int
		 * @throws Exception\FieldValueException
		 * @throws Exception\Operation
		 * @throws \Exception
		 */
		public function storageUpdate($data, $condition, $_source = null, StorageInterface $_store = null){
			if($condition){
				$condition = $this->normalizeCondition($condition);
			}
			if(!$_source) $_source = $this->getDefaultSource();
			if(!$_store) $_store = $this->getDefaultStorage();
			if(!$_source) throw new \Exception('Source is not defined');
			try{
				return $_store->update($data,$condition, $_source);
			}catch(Exception\Operation $e){
				$this->handleStorageOperationException($e);
				return false;
			}

		}

		/**
		 * @param $condition
		 * @param null $_source
		 * @param StorageInterface $_store
		 * @return int
		 * @throws Exception\FieldValueException
		 * @throws Exception\Operation
		 * @throws \Exception
		 */
		public function storageRemove($condition, $_source = null, StorageInterface $_store = null){
			if($condition){
				if(!is_array($condition)){
					$condition = [$this->mapping[$this->pk],'=',$condition];
				}else{
					$condition = $this->normalizeCondition($condition);
				}
			}
			if(!$_source) $_source = $this->getDefaultSource();
			if(!$_store) $_store = $this->getDefaultStorage();
			if(!$_source) throw new \Exception('Source is not defined');
			try{
				return $_store->delete($condition, $_source);
			}catch(Exception\Operation $e){
				$this->handleStorageOperationException($e);
				return false;
			}
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
		 * @throws Exception\FieldValueException
		 * @throws Exception\Operation
		 * @throws \Exception
		 */
		public function storageLoadThrough(
			$condition, $intermediateSchema, $intermediateCondition, $intermediateCollation, $limit = null, $offset = null, $orderBy = null,
			$result_intermediate = false, $result_intermediate_prefix = null
		){

			$store = $this->getDefaultStorage();
			$source = $this->getDefaultSource();
			if(!$source) throw new \Exception('Source is not defined');

			$schemaManager = $this->getRepository();
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
			foreach($this->mapping as $k => $o){
				$columns[$o] = ['self',$o];
			}
			if($result_intermediate){
				foreach($intermediateSchema->mapping as $k => $o){
					$columns[$result_intermediate_prefix . $o] = [ 'intermediate', $o];
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


			try{
				$shipment = $store->select($columns,$source,$condition,Collection::isInfinityLimit($limit)?null:$limit,$offset,$orderBy,[
					'alias' => 'self',
					'joins' => [[
						'table' => $intermediateSchema->getDefaultSource(),
						'alias' => 'intermediate',
						'on'    => $intermediateOn
					]],
				]);
				return $shipment;
			}catch(Exception\Operation $e){
				$this->handleStorageOperationException($e);
				return false;
			}


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


			$store = $this->getDefaultStorage();
			$source = $this->getDefaultSource();

			$schemaManager = $this->getRepository();
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
				if(array_key_exists($field->name,$data)){
					$normalizedData[$this->mapping[$field->name]] = $data[$field->name];
				}
			}

			try{
				return $store->update($data,$condition,$source,[
					'joins' => [[
						'table' => $intermediateSchema->getDefaultSource(),
						'alias' => 'intermediate',
						'on'    => $intermediateOn
					]],
				]);
			}catch(Exception\Operation $e){
				$this->handleStorageOperationException($e);
				return false;
			}

		}


		/**
		 * @param $condition
		 * @param $intermediateSchema
		 * @param $intermediateCondition
		 * @param $intermediateCollation
		 * @return int
		 */
		public function storageRemoveThrough($condition, $intermediateSchema, $intermediateCondition, $intermediateCollation){
			$store = $this->getDefaultStorage();
			$source = $this->getDefaultSource();
			$schemaManager = $this->getRepository();
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


			try{
				return $store->delete($condition,$source,[
					'joins' => [[
						'table' => $intermediateSchema->getDefaultSource(),
						'alias' => 'intermediate',
						'on'    => $intermediateOn
					]],
				]);
			}catch(Exception\Operation $e){
				$this->handleStorageOperationException($e);
				return false;
			}


		}

		/**
		 * @param $id
		 * @return null|array
		 */
		public function storageLoadById($id){
			$result = $this->storageLoad(
				[ $this->mapping[$this->pk] , '=' , $id ], 1
			);
			return $result?$result->asAssoc()->fetch():null;
		}

		/**
		 * @param $data
		 * @param $id
		 * @return bool
		 */
		public function storageUpdateById($data, $id){
			return !!$this->storageUpdate($data,[
				[ $this->mapping[$this->pk], '=', $id ]
			]);
		}

		/**
		 * @param $id
		 * @return bool
		 */
		public function storageRemoveById($id){
			return !!$this->storageRemove($id);
		}





		/**
		 * @param Exception\Operation $exception
		 * @throws Exception\FieldValueException
		 * @throws Exception\Operation
		 * @throws \Exception
		 */
		public function handleStorageOperationException(Exception\Operation $exception){
			if($exception instanceof Exception\FieldValueException){
				if($originalKey = $exception->getFieldName()){
					$field = $this->getFieldByOriginalKey($originalKey);
					$exception->setFieldName($field->name);
					throw $exception;
				}else{
					throw new \Exception('Bad Exception for handle',0,$exception);
				}
			}
			throw $exception;
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
				if($this->fields[$name]){
					$a[$this->mapping[$name]] = $direction;
				}
			}
			return $a;
		}


		public function _condition_types(){

			$type_predicate = [
				'user_id' => 2,
				'is_main' => true
			];

			$type_predicate_extended = [
				'user_id:=' => 2,
				'AND:is_main' => true,
			];

			$type_blocked = [

				['user_id', '=', 2],
				'AND',
				['is_main', '=', true],
				'AND',
				[

					['LOWER(user_name)','=','LOWER(\'user_alex\')']

				]

			];

			$type_simple = ['user_id','=',2];


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
			$sManager = $this->getRepository();
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
							$right['identifier'][1] = $sManager->getSchema($schemas[$right['identifier'][0]])->getOriginal($right['identifier'][1]);
						}
					}
					unset($condition[$key]);
					$condition[$this->getOriginal($key)] = $right;
				}elseif($count === 3 || $count === 2){


					//$left = isset($c[0])?$c[0]:$c['left'];
					//$operator = isset($c[1])?$c[1]:$c['operator'];
					//$right = isset($c[2])?$c[2]:$c['right'];
					// сдесь была раньше проблема если значения является NULL , при проверке на isset()

					list($left, $operator, $right) = Condition::toList($c,[0,'left'],[1,'operator'],[2,'right']);


					if(is_array($left)){
						$left[1] = $sManager->getSchema($schemas[$left[0]])->getOriginal($left[1]);
					}else{
						$left = $this->getOriginal($left);
					}

					if(is_array($right) && isset($right['identifier'])){
						if(is_array($right['identifier'])){
							if(is_array($right['identifier'])){
								$right['identifier'][1] = $sManager->getSchema($schemas[$right['identifier'][0]])->getOriginal($right['identifier'][1]);
							}else{
								$right['identifier'] = $this->getOriginal($right['identifier']);
							}
						}
					}
					$c = [$left, $operator, $right];
				}
				return $condition;
			}
			return $condition;
		}

		public $foreign_dependency = [];

		/**
		 * @param $name
		 * @return array
		 */
		public function getForeignFromField($name){


			$relation_names = [];
			foreach($this->relations as $name => $relation){
				if($relation instanceof Record\Relation\RelationForeign
				   && in_array($name,$relation->fields, true)){
					$relation_names[] = $name;
				}
			}
			return $relation_names;
		}

	}

}

