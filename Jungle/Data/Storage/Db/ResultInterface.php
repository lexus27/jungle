<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 06.03.2016
 * Time: 19:52
 */
namespace Jungle\Data\Storage\Db {

	use Jungle\Data\Storage\Db\Adapter;
	use Jungle\Util\Data\ShipmentOriginalInterface;

	/**
	 * Interface ResultInterface
	 * @package Jungle\Data\Storage\Db
	 */
	interface ResultInterface extends ShipmentOriginalInterface{

		/**
		 * @param \Jungle\Data\Storage\Db\Adapter $adapter
		 * @return mixed
		 */
		public function setAdapter(Adapter $adapter);

		/**
		 * @param array|null $bindTypes
		 * @return mixed
		 */
		public function setBindParams(array $bindTypes = null);

		/**
		 * @param array|null $bindTypes
		 * @return mixed
		 */
		public function setBindTypes(array $bindTypes = null);

		/**
		 * Allows to executes the statement again. Some database systems don't support scrollable cursors,
		 * So, as cursors are forward only, we need to execute the cursor again to fetch rows from the begining
		 *
		 * @return boolean
		 */
		public function execute();

		/**
		 * Fetches an array/object of strings that corresponds to the fetched row, or FALSE if there are no more rows.
		 * This method is affected by the active fetch flag set using Phalcon\Db\Result\Pdo::setFetchMode
		 *
		 * @param null $fetchMode
		 * @return mixed
		 */
		public function fetch($fetchMode = null);

		/**
		 * Returns an array of strings that corresponds to the fetched row, or FALSE if there are no more rows.
		 * This method is affected by the active fetch flag set using Phalcon\Db\Result\Pdo::setFetchMode
		 *
		 * @return mixed
		 */
		public function fetchArray();

		/**
		 * @param $offset
		 * @return mixed
		 */
		public function fetchColumn($offset);
		
		/**
		 * Returns an array of arrays containing all the records in the result
		 * This method is affected by the active fetch flag set using Phalcon\Db\Result\Pdo::setFetchMode
		 *
		 * @param null $fetchMode
		 * @return array
		 */
		public function fetchAll($fetchMode = null);

		/**
		 * @param string $className
		 * @param array|null $constructorArguments
		 * @return object
		 */
		public function fetchObject($className = 'stdClass',array $constructorArguments = null);

		/**
		 * Gets number of rows returned by a resultset
		 *
		 * @return int
		 */
		public function count();

		/**
		 * Changes the fetching mode affecting Phalcon\Db\Result\Pdo::fetch()
		 *
		 * @param int $fetchMode
		 * @return bool
		 */
		public function setFetchMode($fetchMode);

		/**
		 * Gets the internal PDO result object
		 *
		 * @return \PDOStatement
		 */
		public function getInternalResult();

		/**
		 * @return int
		 */
		public function getColumnCount();

		/**
		 * @param $index
		 * @return string
		 */
		public function getColumnNameByIndex($index);

		/**
		 * @param $index
		 * @return string
		 */
		public function getColumnTypeByIndex($index);

	}
}

