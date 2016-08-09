<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.06.2016
 * Time: 21:44
 */
namespace Jungle\Data\Record\Head {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Model;

	/**
	 * Class ModelSchema
	 * @package Jungle\Data\Record\Head
	 */
	class ModelSchema extends Schema{

		/** @var  Model[] */
		protected $flyweight_record = [];

		/** @var  ModelSchema|null */
		protected $ancestor;

		protected $record_classname;

		/**
		 * ModelSchema constructor.
		 * @param $name
		 */
		public function __construct($name){
			parent::__construct($name);
			$this->record_classname = $name;
		}

		/**
		 * @param $className
		 * @return $this
		 */
		public function setModelClassName($className){
			$this->record_classname = $className;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getModelClassName(){
			return $this->record_classname;
		}

		/**
		 * @param Record $record
		 */
		public function initialize(Record $record){
			$storage = $record->getStorage();
			$source = $record->getSource();
			$this->storage = $this->getSchemaManager()->getStorageService($storage);
			$this->source = $source;
			$this->setFlyweight($record);
		}

		/**
		 * @param $className
		 * @return Model
		 */
		protected function _instantiate($className){
			return new $className();
		}


	}
}

