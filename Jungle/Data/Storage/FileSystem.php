<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 06.06.2016
 * Time: 2:44
 */
namespace Jungle\Data\Storage {
	
	use Jungle\Util\Data\ShipmentInterface;
	use Jungle\Util\Data\Storage\StorageInterface;

	class FileSystem implements StorageInterface{

		/**
		 * @param $condition
		 * @param $source
		 * @param null $offset
		 * @param null $limit
		 * @param array $options
		 * @return int
		 */
		public function count($condition, $source, $offset = null, $limit = null, array $options = null){
			// TODO: Implement count() method.
		}

		/**
		 * @param $data
		 * @param $source
		 * @return int
		 */
		public function create($data, $source){
			// TODO: Implement create() method.
		}

		/**
		 * @param $data
		 * @param $condition
		 * @param $source
		 * @param array $options
		 * @return int
		 */
		public function update($data, $condition, $source, array $options = null){
			// TODO: Implement update() method.
		}

		/**
		 * @param $condition
		 * @param $source
		 * @param array $options
		 * @return int
		 */
		public function delete($condition, $source, array $options = null){
			// TODO: Implement delete() method.
		}

		/**
		 * @param $columns
		 * @param $source
		 * @param $condition
		 * @param null $limit
		 * @param null $offset
		 * @param null $orderBy
		 * @param array $options
		 * @return ShipmentInterface
		 */
		public function select(
			$columns,
			$source,
			$condition,
			$limit = null,
			$offset = null,
			$orderBy = null,
			array $options = null
		){
			// TODO: Implement select() method.
		}

		/**
		 * @return bool
		 */
		public function hasConditionSupport(){
			// TODO: Implement hasConditionSupport() method.
		}

		/**
		 * @return bool
		 */
		public function hasJoinSupport(){
			// TODO: Implement hasJoinSupport() method.
		}

		/**
		 * @return mixed
		 */
		public function hasForeignControlSupport(){
			// TODO: Implement hasForeignControlSupport() method.
		}

		/**
		 * @return mixed
		 */
		public function lastCreatedIdentifier(){
			// TODO: Implement lastCreatedIdentifier() method.
		}

		/**
		 * @return mixed
		 */
		public function begin(){
			// TODO: Implement begin() method.
		}

		/**
		 * @return mixed
		 */
		public function commit(){
			// TODO: Implement commit() method.
		}

		/**
		 * @return mixed
		 */
		public function rollback(){
			// TODO: Implement rollback() method.
		}
	}
}

