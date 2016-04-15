<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 06.03.2016
 * Time: 19:54
 */
namespace Jungle\Storage\Db\Result {

	use Jungle\Storage\Db;
	use Jungle\Storage\Db\Adapter;
	use Jungle\Storage\Db\ResultInterface;

	/**
	 * Class Pdo
	 * @package Jungle\Storage\Db\Result
	 */
	class Pdo implements ResultInterface{

		/** @var int */
		protected $fetchMode = Db::FETCH_ASSOC;

		/** @var callable|null */
		protected $fetchHandler;

		/**
		 * @param callable $handler
		 * @return $this
		 */
		public function setFetchHandler(callable $handler){
			$this->fetchHandler = $handler;
			return $this;
		}

		/**
		 *
		 * @param Adapter $connection
		 * @param \PDOStatement $result
		 * @param string $sqlStatement
		 * @param array $bindParams
		 * @param array $bindTypes
		 */
		public function __construct(
			Adapter $connection, \PDOStatement $result, $sqlStatement = null, $bindParams = null, $bindTypes = null
		){
			$this->connection       = $connection;
			$this->result           = $result;
			$this->sql_statement    = $sqlStatement;
			$this->bind_parameters  = $bindParams;
			$this->bind_types       = $bindTypes;
		}

		/**
		 * Allows to executes the statement again. Some database systems don't support scrollable cursors,
		 * So, as cursors are forward only, we need to execute the cursor again to fetch rows from the begining
		 *
		 * @return boolean
		 */
		public function execute(){
			return $this->result->execute();
		}

		/**
		 * Fetches an array/object of strings that corresponds to the fetched row, or FALSE if there are no more rows.
		 * This method is affected by the active fetch flag set using Phalcon\Db\Result\Pdo::setFetchMode
		 *
		 * @return mixed
		 */
		public function fetch(){
			$mode = $this->fetchMode;
			if($mode === Db::FETCH_HANDLER){
				$handler    = $this->fetchHandler;
				$mode       = Db::FETCH_ASSOC;
				return call_user_func($handler, $this->result->fetch($mode), $this);
			}else{
				return $this->result->fetch($mode);
			}
		}

		/**
		 * Returns an array of strings that corresponds to the fetched row, or FALSE if there are no more rows.
		 * This method is affected by the active fetch flag set using Phalcon\Db\Result\Pdo::setFetchMode
		 *
		 * @return mixed
		 */
		public function fetchArray(){
			return $this->result->fetch(Db::FETCH_NUM);
		}

		/**
		 * Returns an array of arrays containing all the records in the result
		 * This method is affected by the active fetch flag set using Phalcon\Db\Result\Pdo::setFetchMode
		 *
		 * @return array
		 */
		public function fetchAll(){
			$mode = $this->fetchMode;
			if($mode === Db::FETCH_HANDLER){
				$rows = [];
				foreach($this->fetch() as $row){
					$rows[] = $row;
				}
				return $rows;
			}else{
				return $this->result->fetchAll($mode);
			}
		}

		/**
		 * @param string $className
		 * @param array|null $constructorArguments
		 * @return object
		 */
		public function fetchObject($className = 'stdClass', array $constructorArguments = null){
			return $this->result->fetchObject($className,$constructorArguments);
		}

		/**
		 * Gets number of rows returned by a resultset
		 *
		 * @return int
		 */
		public function numRows(){
			return $this->result->rowCount();
		}

		/**
		 * Changes the fetching mode affecting Phalcon\Db\Result\Pdo::fetch()
		 *
		 * @param int $fetchMode
		 * @return bool
		 */
		public function setFetchMode($fetchMode){
			$this->fetchMode = $fetchMode;
			return true;
		}

		/**
		 * Gets the internal PDO result object
		 *
		 * @return \PDOStatement
		 */
		public function getInternalResult(){
			return $this->result;
		}

		/**
		 * @param $index
		 * @return string
		 */
		public function getColumnNameByIndex($index){
			$meta = $this->result->getColumnMeta($index);
			return $meta['name'];
		}

		/**
		 * @return int
		 */
		public function getColumnCount(){
			return $this->result->columnCount();
		}

		/**
		 * @param $index
		 * @return string
		 */
		public function getColumnTypeByIndex($index){
			$meta = $this->result->getColumnMeta($index);
			return $meta['native_type'];
		}
	}
}

