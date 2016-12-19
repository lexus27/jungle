<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:59
 */
namespace Jungle\Data\Record {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Collection;
	use Jungle\Data\Record\Schema\Schema;
	use Jungle\Di;
	use Jungle\FileSystem\Model\Directory;
	
	/**
	 * Class Repository
	 * @package modelX
	 */
	class Repository{

		/** @var  Schema[] */
		protected $schemas = [];

		/** @var  Schema */
		protected $initializing_schema;

		/** @var  Repository */
		protected static $default_schema_manager;

		/** @var array  */
		protected $directories = [];

		/**
		 * @return Repository
		 */
		public static function getDefault(){
			if(!self::$default_schema_manager){
				self::$default_schema_manager = new self();
			}
			return self::$default_schema_manager;
		}

		/**
		 * @param Repository $manager
		 */
		public static function setDefault(Repository $manager){
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
		 * @return Schema
		 * @throws RepositoryException
		 * @throws \Exception
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
			$schema = $this->_loadSchema($schemaName);
			if(!$schema){
				throw new \Exception('Schema "'.$schemaName.'" not found');
			}
			//$this->initializeSchema($schema);
			return $schema;
		}


		/**
		 * @param $schemaName
		 * @return Collection
		 */
		public function getCollection($schemaName){
			return $this->getSchema($schemaName)->getCollection();
		}


		protected function _throwSchemaInitError($schemaName){
			throw new RepositoryException('Required schema "' . $schemaName . '" not found!');
		}

		/** @var  OperationControl */
		protected $current_operation;

		/**
		 * @return OperationControl
		 */
		public function currentOperationControl(){
			return $this->current_operation;
		}


		/**
		 * @param Record $record
		 * @return OperationControl
		 */
		public function startOperation(Record $record){
			if(!$this->current_operation){
				$this->current_operation = new OperationControl();
			}
			$this->current_operation->start($record);
			return $this->current_operation;
		}

		/**
		 * @param Record $record
		 */
		public function endOperation(Record $record){
			$this->current_operation->end($record);
			if($this->current_operation->isEmpty()){
				$this->current_operation = null;
			}
		}



		/**
		 * @param $schemaName
		 * @return Schema|null
		 */
		public function getLoadedSchema($schemaName){
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
		protected function _loadSchema($schemaName){
			if(!class_exists($schemaName)){
				throw new RepositoryException('Schema class "' . $schemaName . '" not found!');
			}
			$schema = $this->factorySchema($schemaName);
			$this->initializeSchema($schema);
			return $schema;
		}


		/**
		 * @param Schema $schema
		 * @param Record|null $record
		 * @return Schema
		 * @throws \Exception
		 */
		public function initializeSchema(Schema $schema,Record $record = null){
			$this->schemas[] = $schema;
			$name = $schema->getName();
			$schema->setRecordClassname($name);
			$schema->setRepository($this);
			$schema->initialize($record);
		}

		/**
		 * @param $name
		 * @return Schema
		 */
		public function factorySchema($name){
			$ancestorSchema = null;

			// если родительский класс не является Model то следует иницилизировать родительскую схему
			$ancestorName = get_parent_class($name);
			if(!in_array($ancestorName,[Model::class], true)){
				$ancestorSchema = $this->getSchema($ancestorName);
				return $ancestorSchema->extend($name);
			}else{
				return new Record\Schema\Schema($name);
			}
		}


		/**
		 * @return int|mixed
		 */
		public function getRecordsInCollections(){
			$c = 0;
			foreach($this->schemas as $schema){
				$c += $schema->getCollection()->count();
			}
			return $c;
		}

		/**
		 * @return int
		 */
		public function getRecordsInMemory(){
			return Record::getStatusInstantiatedRecordsCount();
		}


		/**
		 * @param $key
		 * @param $directory
		 * @return $this
		 */
		public function setDirectory($key, Directory $directory){
			$this->directories[$key] = $directory;
			return $this;
		}


		/**
		 * @param $key
		 * @return Directory
		 */
		public function getDirectory($key){
			return isset($this->directories[$key])?$this->directories[$key]:null;
		}



	}
}

