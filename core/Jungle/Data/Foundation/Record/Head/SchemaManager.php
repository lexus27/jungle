<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:59
 */
namespace Jungle\Data\Foundation\Record\Head {

	use Jungle\Data\Foundation\Record;
	use Jungle\Data\Foundation\Record\Collection;
	
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
		 * @param $schemaName
		 * @return bool
		 */
		public function isRecognized($schemaName){
			foreach($this->schemas as $schema){
				if($schema->getName() === $schemaName){
					return true;
				}
			}
			return false;
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
			$schema = $this->loadSchema($schemaName);
			if(!$schema){
				throw new \LogicException('Required schema "'.$schemaName.'" not found!');
			}
			$this->schemas[] = $schema;
			return $schema;
		}

		/**
		 * @param $schemaName
		 * @return Schema
		 *
		 * Вот именно здесь мы и ищем и создаем схему по её установленному и полученому определению
		 *
		 */
		protected function loadSchema($schemaName){
			if(!class_exists($schemaName)){
				throw new \LogicException('Schema class "'.$schemaName.'" not found!');
			}
			$definition = $schemaName::getSchemaDefinition();
			return buildDefinition($definition);
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

