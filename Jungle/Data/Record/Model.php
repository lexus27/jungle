<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 20:50
 */
namespace Jungle\Data\Record {

	use App\Model\Usergroup;
	use Jungle\Data\Record;
	use Jungle\Data\Record\Collection\Relationship;
	use Jungle\Data\Record\Head\Field;
	use Jungle\Data\Record\Head\ModelSchema;
	use Jungle\Data\Record\Head\SchemaManager;

	/**
	 * Class Model
	 * @package Jungle\Data\Bridge
	 */
	abstract class Model extends Record{

		/** @var  array */
		protected $_initialized_properties = [];

		/** @var  Record\Head\ModelSchema */
		protected static $_model_schema;

		/**
		 * Model constructor.
		 * @param null $validationCollector
		 */
		public function __construct($validationCollector = null){
			parent::__construct($validationCollector);
			//  Здесь мы проверяем инициализованая ли модель. по статическому менеджеру схем
			//  Если нет то инициализуем её в регистре, совместно с получением самой схемы
			//  Для формирования схемы требуются
			//      Тип хранилища,
			//      Название источника,
			//      Базовый класс записи,
			//      Настройки схемы,
			//      Поля,
			//      Типы полей,
			//      Умолчания полей,
			//      Nullable полей,
			//      Валидаторы полей

			// Нужно получить Менеджер схем, чтобы проверить инициализацию данной модели
			/** @var Model $name */
			$name = get_called_class();
			$manager = SchemaManager::getDefault();
			/** @var ModelSchema $schema */
			$schema = $manager->getSchemaNative($name);
			if(!$schema){
				$schema = $manager->initializeFromConstructor($name,$this);
				$this->_schema = $schema;
				$schema->initialize($this);
				$manager->addSchema($schema);
			}else{
				$this->_schema = $schema;
			}
			$this->_initValidationCollector($validationCollector);
			$this->onConstruct();
			$this->onRecordReady();
		}

		public function markRecordInitialized(){
			parent::markRecordInitialized();
		}

		/**
		 * @return string
		 */
		public function getStorage(){
			return 'database';
		}

		/**
		 * @return Head\ModelSchema
		 */
		public static function getModelSchema(){
			/** @var Model $name */
			$name = get_called_class();
			return SchemaManager::getDefault()->getSchema($name);
		}

		/**
		 * @param $classname
		 * @return bool
		 */
		public static function isRealModel($classname){
			return !in_array($classname,[Model::class], true);
		}

		/**
		 * @Do-initialize-current-model-schema
		 * @param Head\Schema $schema
		 * @throws \Exception
		 */
		public static function initialize(Record\Head\Schema $schema){
			throw new \Exception('Could not initialize '.Model::class.', please overload method initialize');
		}

		/**
		 * @param null $condition
		 * @param null $offset
		 * @return int
		 */
		public static function count($condition = null, $offset = null){
			return self::getModelSchema()->count($condition,$offset);
		}

		/**
		 * @param $columns
		 * @param null $condition
		 * @param null $offset
		 */
		public static function countDistinct($columns,$condition = null, $offset = null){

		}

		/**
		 *
		 */
		public static function query(){

		}

		/**
		 * @param $condition
		 * @return int
		 */
		public static function deleteCollection($condition){
			return self::getModelSchema()->remove($condition);
		}

		/**
		 * @param null $condition
		 * @param null $limit
		 * @param null $offset
		 * @param null $orderBy
		 * @return Collection
		 */
		public static function find($condition = null, $limit = null, $offset = null, $orderBy = null){
			return self::getModelSchema()->load($condition,$limit,$offset,$orderBy);
		}

		/**
		 * @param null $condition
		 * @param null $offset
		 * @param null $orderBy
		 * @return Model
		 */
		public static function findFirst($condition = null, $offset = null, $orderBy = null){
			return self::getModelSchema()->loadFirst($condition,$offset,$orderBy);
		}

		/**
		 *
		 */
		protected function beforeCreate(){
			$field_name = $this->_schema->getDerivativeField();
			if($field_name){
				$this->_setFrontProperty($field_name, get_class());
			}
			parent::beforeCreate();
		}

		/**
		 * @param $column
		 * @param null $condition
		 */
		public static function sum($column, $condition = null){

		}

		/**
		 * @param $column
		 * @param null $condition
		 */
		public static function average($column,$condition = null){

		}

		/**
		 * @param $column
		 * @param null $condition
		 */
		public static function maximum($column,$condition = null){

		}

		/**
		 * @param $column
		 * @param null $condition
		 */
		public static function minimum($column,$condition = null){

		}



		/**
		 * @return array
		 */
		public function getAutoInitializeProperties(){
			return [];
		}

		/**
		 *
		 */
		protected function onRecordReady(){
			$autoInitProps = $this->getAutoInitializeProperties();
			if($autoInitProps === true){
				$autoInitProps = $this->_schema->getEnumerableNames();
			}
			foreach($autoInitProps as $property_name){
				$this->_getFrontProperty($property_name);
			}
		}

		/**
		 * @param null $fieldName
		 * @return mixed
		 */
		public function reset($fieldName = null){
			if($fieldName === null){
				$this->_initialized_properties = [];
				$this->_afterReset();
			}else{
				unset($this->_initialized_properties[$fieldName]);
			}
			$this->onRecordReady();
		}

		/**
		 * @param null $fieldName
		 */
		protected function _resetAll($fieldName = null){
			if($fieldName === null){
				$this->_processed = [];
				$this->_initialized_properties = [];
				$this->_afterResetAll();
			}else{
				unset($this->_processed[$fieldName]);
				unset($this->_initialized_properties[$fieldName]);
			}
			$this->onRecordReady();
		}


		/**
		 * @param $name
		 * @param $value
		 * @return mixed|void
		 */
		protected function _setFrontProperty($name, $value){
			$this->_initialized_properties[$name] = true;
			$this->{$name} = $value;
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		protected function &_getFrontProperty($name){
			if($this->_operation_made === self::OP_CREATE){
				return $this->{$name};
			}
			if(!isset($this->_initialized_properties[$name])){
				$this->_initialized_properties[$name] = true;
				$this->{$name} = $this->_getProcessed($name);
			}
			return $this->{$name};
		}

		/**
		 * @param $name
		 * @return bool
		 */
		public function isInitializedProperty($name){
			return isset($this->_initialized_properties[$name]);
		}

		public function getRelatedRecord($name){

		}

		/**
		 * @param $name
		 * @param array $parameters
		 */
		public function getRelatedCollection($name, array $parameters = []){

		}
	}
}

