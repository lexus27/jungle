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
	
	use Jungle\Util\Data\Foundation\ShipmentInterface;
	use Jungle\Util\Data\Foundation\Storage\StorageInterface;

	class FileSystem implements StorageInterface{

		/**
		 * @param $condition
		 * @param $source
		 * @param null $offset
		 * @param null $limit
		 */
		public function count($condition, $source, $offset = null, $limit = null){
			// TODO: Implement count() method.
		}

		/**
		 * @param $data
		 * @param $source
		 * @return bool|void
		 */
		public function create($data, $source){
			// TODO: Implement create() method.
		}

		/**
		 * @return mixed
		 */
		public function lastCreatedIdentifier(){
			// TODO: Implement lastCreatedIdentifier() method.
		}

		/**
		 * @param $condition
		 * @param $data
		 * @param $source
		 * @return bool
		 */
		public function update($data, $condition, $source){
			// TODO: Implement update() method.
		}

		/**
		 * @param $condition
		 * @param $source
		 * @return bool
		 */
		public function delete($condition, $source){
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
			$columns, $source, $condition, $limit = null, $offset = null, $orderBy = null, array $options = null
		){
			// TODO: Implement select() method.
		}

		/**
		 * @return bool
		 */
		public function begin(){
			// TODO: Implement begin() method.
		}

		public function commit(){
			// TODO: Implement commit() method.
		}

		public function rollback(){
			// TODO: Implement getRollbackData() method.
		}
	}
}

