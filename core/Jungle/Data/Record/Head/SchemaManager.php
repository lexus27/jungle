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

		/** @var  Collection[] */
		protected $collections = [];

		/** @var  Schema[] */
		protected $schemas = [];

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
			throw new \LogicException('Required schema "'.$schemaName.'" not found!');
		}



		/**
		 * @param $schemaName
		 * @return Schema|null
		 */
		public function getSchemaNative($schemaName){
			if($schemaName instanceof Schema){
				return $schemaName;
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
		 * @param $schemaName
		 * @return Schema
		 * @throws \Exception
		 *
		 * Вот именно здесь мы и ищем и создаем схему по её установленному и полученому определению
		 *
		 */
		protected function initializeSchema($schemaName){
			if(!class_exists($schemaName)){
				throw new \LogicException('Schema class "'.$schemaName.'" not found!');
			}
			/** @var Model $instance */
			$instance = new $schemaName();
			$schema = $instance->getSchema();
			if(!$schema){
				$this->_throwSchemaInitError($schemaName);
			}
			return $schema;
		}

		/**
		 * @return int|mixed
		 */
		public function getStatusRecordsLoadedCount(){
			$c = 0;
			foreach($this->schemas as $schema){
				$c+=$schema->getCollection()->count();
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

