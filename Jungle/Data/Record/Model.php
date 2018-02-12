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
	
	use Jungle\Data\Record;
	use Jungle\Data\Record\Schema\Schema;
	
	/**
	 * Class Model
	 * @package Jungle\Data\Bridge
	 */
	abstract class Model extends Record{

		/**
		 * @return Schema
		 */
		public static function getModelSchema(){
			/** @var Model $name */
			$name = get_called_class();
			return SchemaManager::getDefault()->getSchema($name);
		}

		/**
		 * @Do-initialize-current-model-schema
		 * @param Schema $schema
		 * @throws \Exception
		 */
		public static function initialize(Schema $schema){
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
		public function beforeCreate(){
			/**  @TODO: delete */
			$field_name = $this->_schema->getBootField();
			if($field_name){
				$this->{$field_name} = $this->_schema->getBootValue();
			}
		}

	}
}

