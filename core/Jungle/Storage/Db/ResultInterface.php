<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 06.03.2016
 * Time: 19:52
 */
namespace Jungle\Storage\Db {

	use Jungle\Storage\Db\Adapter;

	/**
	 * Interface ResultInterface
	 * @package Jungle\Storage\Db
	 */
	interface ResultInterface{

		/**
		 *
		 * @param Adapter $connection
		 * @param \PDOStatement $result
		 * @param string $sqlStatement
		 * @param array $bindParams
		 * @param array $bindTypes
		 */
		public function __construct(Adapter $connection, \PDOStatement $result, $sqlStatement = null, $bindParams = null, $bindTypes = null);

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
		 * @return mixed
		 */
		public function fetch();

		/**
		 * Returns an array of strings that corresponds to the fetched row, or FALSE if there are no more rows.
		 * This method is affected by the active fetch flag set using Phalcon\Db\Result\Pdo::setFetchMode
		 *
		 * @return mixed
		 */
		public function fetchArray();

		/**
		 * Returns an array of arrays containing all the records in the result
		 * This method is affected by the active fetch flag set using Phalcon\Db\Result\Pdo::setFetchMode
		 *
		 * @return array
		 */
		public function fetchAll();

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
		public function numRows();

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

