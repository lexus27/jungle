<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 06.03.2016
 * Time: 19:54
 */
namespace Jungle\Data\Storage\Db\Result {

	use Jungle\Data\Storage\Db;
	use Jungle\Data\Storage\Db\Adapter;
	use Jungle\Data\Storage\Db\ResultInterface;

	/**
	 * Class Pdo
	 * @package Jungle\Data\Storage\Db\Result
	 */
	class Pdo implements ResultInterface{

		/** @var int */
		protected $fetchMode = Db::FETCH_ASSOC;

		/** @var  Adapter */
		protected $adapter;

		/** @var  \PDOStatement */
		protected $statement;

		/** @var bool  */
		protected $bounded = false;

		/** @var  array|null */
		protected $bind_parameters;

		/** @var  array|null  */
		protected $bind_types;

		/**
		 *
		 * @param Adapter $adapter
		 * @param \PDOStatement $statement
		 * @param array $bindParams
		 * @param array $bindTypes
		 */
		public function __construct(Adapter $adapter, \PDOStatement $statement, $bindParams = null, $bindTypes = null){
			$this->adapter          = $adapter;
			$this->statement        = $statement;
			$this->bind_parameters  = $bindParams;
			$this->bind_types       = $bindTypes;
		}

		/**
		 * @param \PDOStatement $statement
		 * @param $bindings
		 * @param $types
		 * @param bool $useAutoDetectTypeIfEmpty
		 * @param int $defaultType
		 */
		public static function mountBindings(\PDOStatement $statement, $bindings, $types, $useAutoDetectTypeIfEmpty = true, $defaultType = Db\Structure\Column::BIND_PARAM_STR){
			if(is_array($bindings)){
				foreach($bindings as $key => $value){
					$type = null;
					if(is_array($types)){
						$type = isset($types[$key])?$types[$key]:null;
					}else{
						$type = $types;
					}
					if(!$type){
						$type = $useAutoDetectTypeIfEmpty?Adapter::getBestDbColumnType($value):$defaultType;
					}
					if(is_numeric($key)) $key = $key+1;
					$statement->bindValue($key,$value,$type);
				}
			}
		}

		/**
		 * @param \Jungle\Data\Storage\Db\Adapter $adapter
		 * @return mixed
		 */
		public function setAdapter(Adapter $adapter){
			$this->adapter = $adapter;
		}

		/**
		 * @param array $bindParams
		 * @return mixed
		 */
		public function setBindParams(array $bindParams = null){
			if($this->bind_parameters !== $bindParams){
				$this->bind_parameters = $bindParams;
				$this->bounded = false;
			}

			return $this;
		}

		/**
		 * @param array|null|null $bindTypes
		 * @return mixed
		 */
		public function setBindTypes(array $bindTypes = null){
			if($this->bind_types !== $bindTypes){
				$this->bind_types = $bindTypes;
				$this->bounded = false;
			}
			return $this;
		}


		/**
		 * Allows to executes the statement again. Some database systems don't support scrollable cursors,
		 * So, as cursors are forward only, we need to execute the cursor again to fetch rows from the begining
		 *
		 * @return boolean
		 */
		public function execute(){
			if(!$this->bounded){
				$params = $this->bind_parameters;
				$types = $this->bind_types;
				static::mountBindings($this->statement,$params, $types);
				$this->bounded = true;
			}
			return $this->statement->execute();
		}

		/**
		 * Fetches an array/object of strings that corresponds to the fetched row, or FALSE if there are no more rows.
		 * This method is affected by the active fetch flag set using Phalcon\Db\Result\Pdo::setFetchMode
		 *
		 * @param null $fetchMode
		 * @return mixed
		 */
		public function fetch($fetchMode = null){
			return $this->statement->fetch($fetchMode===null?$this->fetchMode:$fetchMode);
		}

		/**
		 * Returns an array of strings that corresponds to the fetched row, or FALSE if there are no more rows.
		 * This method is affected by the active fetch flag set using Phalcon\Db\Result\Pdo::setFetchMode
		 *
		 * @return mixed
		 */
		public function fetchArray(){
			return $this->statement->fetch(Db::FETCH_NUM);
		}

		/**
		 * Returns an array of arrays containing all the records in the result
		 * This method is affected by the active fetch flag set using Phalcon\Db\Result\Pdo::setFetchMode
		 *
		 * @param null $fetchMode
		 * @return array
		 */
		public function fetchAll($fetchMode = null){
			return $this->statement->fetchAll($fetchMode!==null?$fetchMode:$this->fetchMode);
		}

		/**
		 * @param string $className
		 * @param array|null $constructorArguments
		 * @return object
		 */
		public function fetchObject($className = 'stdClass', array $constructorArguments = null){
			return $this->statement->fetchObject($className,$constructorArguments);
		}

		/**
		 * Gets number of rows returned by a resultset
		 *
		 * @return int
		 */
		public function count(){
			return $this->statement->rowCount();
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
			return $this->statement;
		}

		/**
		 * @param $index
		 * @return string
		 */
		public function getColumnNameByIndex($index){
			$meta = $this->statement->getColumnMeta($index);
			return $meta['name'];
		}

		/**
		 * @return int
		 */
		public function getColumnCount(){
			return $this->statement->columnCount();
		}

		/**
		 * @param $offset
		 * @return string
		 */
		public function getColumnTypeByIndex($offset){
			$meta = $this->statement->getColumnMeta($offset);
			return $meta['native_type'];
		}

		/**
		 * @param $offset
		 * @return mixed
		 */
		public function fetchColumn($offset){
			return $this->statement->fetchColumn($offset);
		}

		/**
		 * @return Pdo
		 */
		public function asIndexed(){
			$this->setFetchMode(Db::FETCH_NUM);
			return $this;
		}

		/**
		 * @return Pdo
		 */
		public function asAssoc(){
			$this->setFetchMode(Db::FETCH_ASSOC);
			return $this;
		}
	}
}

