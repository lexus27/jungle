<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:59
 */
namespace Jungle\Data\Record\Head {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Collection;
	use Jungle\Data\Record\Model;
	use Jungle\Di;
	
	/**
	 * Class SchemaManager
	 * @package modelX
	 */
	class SchemaManager{

		/** @var  Schema[] */
		protected $schemas = [];

		/** @var  Schema */
		protected $initializing_schema;

		/** @var  SchemaManager */
		protected static $default_schema_manager;

		/**
		 * @return SchemaManager
		 */
		public static function getDefault(){
			if(!self::$default_schema_manager){
				self::$default_schema_manager = new self();
			}
			return self::$default_schema_manager;
		}

		/**
		 * @param SchemaManager $manager
		 */
		public static function setDefault(SchemaManager $manager){
			self::$default_schema_manager = $manager;
		}



		/**
		 * @param $schema
		 * @return $this
		 */
		public function addSchema(Schema $schema){
			$this->schemas[] = $schema;
			return $this;
		}

		/**
		 * @param $schemaName
		 * @return Collection
		 */
		public function getCollection($schemaName){
			return $this->getSchema($schemaName)->getCollection();
		}


		/**
		 * @param $schemaName
		 * @return Schema
		 */
		public function getSchema($schemaName){
			if($schemaName instanceof Schema){
				return $schemaName;
			}
			foreach($this->schemas as $schema){
				if($schema->getName() === $schemaName){
					return $schema;
				}
			}
			return $this->initializeSchema($schemaName);
		}

		protected function _throwSchemaInitError($schemaName){
			throw new SchemaManagerException('Required schema "'.$schemaName.'" not found!');
		}



		/**
		 * @param $schemaName
		 * @return Schema|null
		 */
		public function getSchemaNative($schemaName){
			if($schemaName instanceof Schema){
				return $schemaName;
			}
			if($this->initializing_schema && $this->initializing_schema->getName() === $schemaName){
				return $this->initializing_schema;
			}
			foreach($this->schemas as $schema){
				if($schema->getName() === $schemaName){
					return $schema;
				}
			}
			return null;
		}

		/**
		 * @param $storage
		 * @return mixed
		 */
		public function getStorageService($storage){
			if(is_object($storage)){
				return $storage;
			}
			return Di::getDefault()->get($storage);
		}

		/**
		 * @param Model $schemaName
		 * @return Schema
		 * @throws \Exception
		 *
		 * Вот именно здесь мы и ищем и создаем схему по её установленному и полученому определению
		 *
		 */
		public function initializeSchema($schemaName){
			if(!class_exists($schemaName)){
				throw new SchemaManagerException('Schema class "'.$schemaName.'" not found!');
			}
			$reflection = new \ReflectionClass($schemaName);
			if(!$reflection->isAbstract()){
				/** @var Record $record */
				$record = new $schemaName($this);
				$schema = $record->getSchema();
			}else{
				$ancestorSchema = null;
				$ancestorName = get_parent_class($schemaName);
				if(!in_array($ancestorName,[Model::class], true)){
					$ancestorSchema = $this->getSchema($ancestorName);
				}
				if($ancestorSchema){
					$schema = $ancestorSchema->extend($schemaName);
				}else{
					$schema = new Record\Head\ModelSchema($schemaName);
				}
				$schema->setRecordClassname($schemaName);
				$schema->setSchemaManager($this);
				$schemaName::initialize($schema);
			}
			return $schema;
		}

		/**
		 * @param Model $schemaName
		 * @param Record $record
		 * @return ModelSchema|static
		 * @throws SchemaManagerException
		 */
		public function initializeFromConstructor($schemaName, Record $record){
			$ancestorSchema = null;
			$ancestorName = get_parent_class($schemaName);
			if(!in_array($ancestorName,[Model::class], true)){
				$ancestorSchema = $this->getSchema($ancestorName);
			}
			if($ancestorSchema){
				$schema = $ancestorSchema->extend($schemaName);
			}else{
				$schema = new Record\Head\ModelSchema($schemaName);
			}
			$schema->setRecordClassname($schemaName);
			$schema->setSchemaManager($this);
			$schemaName::initialize($schema);
			$schema->initialize($record);
			return $schema;
		}

		/**
		 * @return int|mixed
		 */
		public function getStatusRecordsLoadedCount(){
			$c = 0;
			foreach($this->schemas as $schema){
				$c+= $schema->getCollection()->count();
			}
			return $c;
		}

		/**
		 * @return int
		 */
		public function getStatusRecordsInstantiatedCount(){
			return Record::getStatusInstantiatedRecordsCount();
		}

	}
}

