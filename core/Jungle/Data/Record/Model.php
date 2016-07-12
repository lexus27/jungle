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
	use Jungle\Data\Record\Head\SchemaManager;

	/**
	 * Class Model
	 * @package Jungle\Data\Bridge
	 */
	class Model extends Record{

		/** @var  array */
		protected $_initialized_properties = [];

		/** @var  Record\Head\ModelSchema */
		protected static $_model_schema;

		/**
		 * Model constructor.
		 */
		public function __construct(){
			parent::__construct();
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

			$name = get_called_class();
			$manager = SchemaManager::getDefault();
			$schema = $manager->getSchemaNative($name);
			if(!$schema){
				$schema = new Record\Head\ModelSchema($name);
				$schema->setBaseClassName($name);
				$schema->setSchemaManager($manager);
				$this->_schema = $schema;
				static::$_model_schema = $this->_schema;
				$schema->initialize($this);
				$this->initialize();
				$manager->addSchema($schema);
			}else{
				$this->_schema = $schema;
			}
			$this->onConstruct();
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
			if(!static::$_model_schema){
				static::$_model_schema = SchemaManager::getDefault()->getSchema(get_called_class());
			}
			return static::$_model_schema;
		}

		/**
		 * @Do-initialize-current-model-schema
		 */
		public function initialize(){}

		/**
		 * @param $property
		 * @param $referencedSchemaName
		 * @param string $onDelete
		 * @param string $onUpdate
		 * @param bool $virtualConstraint
		 * @param array $fields
		 * @param array $referencedFields
		 * @param bool $nullable
		 * @return $this
		 */
		public function belongsTo($property, $referencedSchemaName, $onDelete, $onUpdate, $virtualConstraint, array $fields,array $referencedFields, $nullable = false){
			if( !($field = $this->_schema->getField($property)) ){
				$field = new Field\Relation($property);
				$field->belongsTo($referencedSchemaName,$fields,$referencedFields,null,[
					'onUpdate' => $onUpdate,
				    'onDelete' => $onDelete,
				    'onUpdateVirtual' => $virtualConstraint,
				    'onDeleteVirtual' => $virtualConstraint
				]);
				$field->setNullable($nullable);
				$field->setDefault(null);
				$this->_schema->addField($field);
			}else{
				/** @var Field\Relation $field */
				$field->belongsTo($referencedSchemaName,$fields,$referencedFields,null,[
					'onUpdate' => $onUpdate,
					'onDelete' => $onDelete,
					'onUpdateVirtual' => $virtualConstraint,
					'onDeleteVirtual' => $virtualConstraint
				]);
				$field->setNullable($nullable);
				$field->setDefault(null);
			}
			return $this;
		}

		/**
		 * @param $property
		 * @param $referencedSchemaSpecifierFieldName
		 * @param $fieldName
		 * @param string $onUpdate
		 * @param string $onDelete
		 * @param array $allowedSchemaNames
		 * @param array $allowedStorageTypes
		 */
		public function belongsToDynamic($property, $referencedSchemaSpecifierFieldName, $fieldName, $onUpdate, $onDelete,array $allowedSchemaNames = null, array $allowedStorageTypes = null){

		}

		/**
		 * @param $property
		 * @param $referencedSchemaName
		 * @param array $fields
		 * @param array $referencedFields
		 * @param bool $branch
		 * @return $this
		 */
		public function hasOne($property, $referencedSchemaName,array $fields,array $referencedFields, $branch = false){
			if( !($field = $this->_schema->getField($property)) ){
				$field = new Field\Relation($property);
				$field->hasOne($referencedSchemaName,$fields,$referencedFields);
				$field->setNullable(true);
				$field->setDefault(null);
				$this->_schema->addField($field);
			}else{
				/** @var Field\Relation $field */
				$field->hasOne($referencedSchemaName,$fields,$referencedFields);
				$field->setNullable(true);
				$field->setDefault(null);
			}
			return $this;
		}
		


		/**
		 * @param $property
		 * @param $referencedSchemaSpecifierFieldName
		 * @param $fieldName
		 * @param bool|false $branch
		 * @param array $allowedSchemaNames
		 * @param array $allowedStorageTypes
		 */
		public function hasOneDynamic($property, $referencedSchemaSpecifierFieldName, $fieldName, $branch = false,array $allowedSchemaNames = null, array $allowedStorageTypes = null){

		}
		

		/**
		 * @param $property
		 * @param $referencedSchemaName
		 * @param array $fields
		 * @param array $referencedFields
		 * @param bool $branch
		 */
		public function hasMany($property, $referencedSchemaName,array $fields,array $referencedFields, $branch = false){
			if( !($field = $this->_schema->getField($property)) ){
				$field = new Field\Relation($property);
				$field->hasMany($referencedSchemaName,$fields,$referencedFields);
				$field->setNullable(true);
				$field->setDefault(null);
				$this->_schema->addField($field);
			}else{
				/** @var Field\Relation $field */
				$field->hasMany($referencedSchemaName,$fields,$referencedFields);
				$field->setNullable(true);
				$field->setDefault(null);
			}
		}

		/**
		 * @param $property
		 * @param $intermediateSchemaName
		 * @param $referencedSchemaName
		 * @param array $fields
		 * @param array $intermediateFields
		 * @param array $intermediateReferencedFields
		 * @param array $referencedFields
		 * @return $this
		 */
		public function hasManyToMany($property, $intermediateSchemaName, $referencedSchemaName, array $fields, array $intermediateFields, array $intermediateReferencedFields, array $referencedFields){
			if( !($field = $this->_schema->getField($property)) ){
				$field = new Field\Relation($property);
				$field->hasManyThrough($intermediateSchemaName,$referencedSchemaName,$fields,$intermediateFields,$intermediateReferencedFields,$referencedFields);
				$field->setNullable(true);
				$field->setDefault(null);
				$this->_schema->addField($field);
			}else{
				/** @var Field\Relation $field */
				$field->hasManyThrough($intermediateSchemaName,$referencedSchemaName,$fields,$intermediateFields,$intermediateReferencedFields,$referencedFields);
				$field->setNullable(true);
				$field->setDefault(null);
			}
			return $this;
		}


		/**
		 * @param $name
		 * @param $type
		 * @param null $originalKey
		 * @return $this
		 */
		public function specifyPrimaryField($name, $type, $originalKey = null){
			$field = new Field($name,$type);
			$field->setDefault(null);
			$field->setNullable(false);
			$field->setReadonly(true);
			$field->setOriginalKey($originalKey);
			$this->_schema->addField($field);
			return $this;
		}

		/**
		 * @param $name
		 * @param $type
		 * @param null $default
		 * @param bool|false $nullable
		 * @param null $originalKey
		 * @param array $otherOptions
		 * @return $this
		 */
		public function specifyField($name, $type, $originalKey = null, $default = null, $nullable = false, array $otherOptions = []){
			$notIn = false;
			if( !($field  = $this->_schema->getField($name)) ){
				$notIn = true;
				$field = new Field($name,$type);
			}
			$field->setType($type);
			$field->setDefault($default);
			$field->setNullable($nullable);
			$field->setOriginalKey($originalKey);

			/**
			$getterMethod = null;
			$setterMethod = null;
			$getterPrefix = 'get';
			$setterPrefix = 'set';
			if($otherOptions){
				$o = array_replace_recursive([
					'face' => [
						'getter_prefix' => 'get',
						'setter_prefix' => 'set',
						'getter' => null,
					    'setter' => null,
					]
				],$otherOptions);

				if($o['face']['getter']){
					$getterMethod = $o['face']['getter'];
				}
				if($o['face']['setter']){
					$setterMethod = $o['face']['setter'];
				}

				$getterPrefix = $o['face']['getter_prefix'];
				$setterPrefix = $o['face']['setter_prefix'];
			}

			$mName = $getterPrefix.$name;
			if(!$getterMethod && method_exists($this,$mName)){
				$getterMethod = $mName;
			}
			$mName = $setterPrefix.$name;
			if(!$setterMethod && method_exists($this,$mName)){
				$setterMethod = $mName;
			}


			if($setterMethod){
				$field->setSetterMethod($setterMethod);
			}
			if($getterMethod){
				$field->setGetterMethod($getterMethod);
			}
			*/

			if($notIn){
				$this->_schema->addField($field);
			}
			return $this;
		}



		// TODO get related method
		// TODO re define get|setProperty method
		/**
		 * @param $name
		 * @param bool|true $readonly
		 * @param bool|false $private
		 * @return $this
		 */
		public function specifyFieldVisibility($name, $readonly = true, $private = false){
			if( ($field  = $this->_schema->getField($name)) ){
				$field->setReadonly($readonly);
				$field->setPrivate($private);
			}
			return $this;
		}

		/**
		 * @param $type
		 * @param array $fields
		 * @param array $sizes
		 * @param array $directions
		 */
		public function specifyIndex($type, array $fields, array $sizes = [], array $directions = []){

		}

		/**
		 * @param $fieldName
		 * @param $originalKey
		 * @return $this
		 */
		public function specifyFieldAlias($fieldName, $originalKey){
			if( ($field  = $this->_schema->getField($fieldName)) ){
				$field->setOriginalKey($originalKey);
			}
			return $this;
		}

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
		 * @param $field_name
		 * @return $this
		 */
		public function useDynamicClassFieldName($field_name){
			$this->_schema->setDynamicRecordClassField($field_name);
			return $this;
		}

		/**
		 * @param bool|true $dynamicUpdate
		 * @return $this
		 */
		public function useDynamicUpdate($dynamicUpdate = true){
			$this->_schema->setDynamicUpdate($dynamicUpdate);
			return $this;
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
			foreach($this->getAutoInitializeProperties() as $property_name){
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
		protected function _getFrontProperty($name){
			if(!isset($this->_initialized_properties[$name])){
				$this->_initialized_properties[$name] = true;
				return $this->{$name} = $this->_getProcessed($name);
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
	}
}

