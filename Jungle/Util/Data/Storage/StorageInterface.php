<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.05.2016
 * Time: 20:29
 */
namespace Jungle\Util\Data\Storage {

	use Jungle\Util\Data\ShipmentInterface;

	/**
	 * Interface StorageInterface
	 * @package Jungle\Util\Data\Storage
	 */
	interface StorageInterface{

		/**
		 * @param $condition
		 * @param $source
		 * @param null $offset
		 * @param null $limit
		 * @param array $options
		 * @return int
		 */
		public function count($condition, $source, $offset = null, $limit = null, array $options = null);

		/**
		 * @param $data
		 * @param $source
		 * @return int
		 */
		public function create($data,$source);

		/**
		 * @param $data
		 * @param $condition
		 * @param $source
		 * @param array $options
		 * @return int
		 */
		public function update($data,$condition, $source, array $options = null);

		/**
		 * @param $condition
		 * @param $source
		 * @param array $options
		 * @return int
		 */
		public function delete($condition, $source,array $options = null);



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
		public function select($columns, $source, $condition, $limit = null, $offset = null, $orderBy = null,array $options = null);

		/**
		 * @return bool
		 */
		public function hasConditionSupport();

		/**
		 * @return bool
		 */
		public function hasJoinSupport();

		/**
		 * @return mixed
		 */
		public function hasForeignControlSupport();

		/**
		 * @return mixed
		 */
		public function lastCreatedIdentifier();

		/**
		 * @return mixed
		 */
		public function begin();

		/**
		 * @return mixed
		 */
		public function commit();

		/**
		 * @return mixed
		 */
		public function rollback();

	}
}

