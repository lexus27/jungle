<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 04.03.2016
 * Time: 11:23
 */
namespace Jungle\Data\Storage\Db {

	use Jungle\Data\Foundation\Storage;
	use Jungle\Data\Storage\Db;
	use Jungle\Data\Storage\Db\Structure\Column;
	use Jungle\Data\Storage\Db\Structure\Column\ForeignKey;
	use Jungle\Data\Storage\Db\Structure\Column\Index;
	use Jungle\Data\Storage\Db\Structure\Column\Type;
	use Jungle\Data\Storage\Db\Structure\Column\TypePool;
	use Jungle\Data\Storage\Db\Structure\Database;
	use Jungle\Data\Storage\Db\Structure\Table;

	/**
	 * Class Adapter
	 * @package Jungle\Data\Storage\Db
	 */
	abstract class Adapter implements Storage\StorageInterface{

		/** @var  Dialect */
		protected $dialect;

		/** @var  Structure */
		protected $structure;

		/** @var string */
		protected $driverType;

		/** @var string */
		protected $dialectType;

		/** @var  TypePool */
		protected $column_type_manager;

		/** @var array  */
		protected $descriptor = [];

		/**
		 * @param $value
		 * @return int
		 */
		public static function getBestDbColumnType($value){
			switch(gettype($value)){
				case 'integer':
				case 'float':
					return Column::BIND_PARAM_INT;
					break;
				case 'boolean':
					return Column::BIND_PARAM_BOOL;
					break;
				case 'null':
					return Column::BIND_PARAM_NULL;
					break;
				default:
					return Column::BIND_PARAM_STR;
					break;
			}
		}

		/**
		 * @return string
		 */
		public function getDefaultDatabaseName(){
			return $this->descriptor['dbname'];
		}


		/**
		 * @param Dialect $dialect
		 * @return $this
		 */
		public function setDialect(Dialect $dialect){
			$this->dialect = $dialect;
			return $this;
		}

		/**
		 * @return Dialect
		 */
		public function getDialect(){
			return $this->dialect;
		}

		/**
		 * @param TypePool $manager
		 * @return $this
		 */
		public function setColumnTypeManager(TypePool $manager){
			$this->column_type_manager = $manager;
			return $this;
		}

		/**
		 * @return TypePool
		 */
		public function getColumnTypeManager(){
			return $this->column_type_manager;
		}

		/**
		 * @param $name
		 * @return Type
		 */
		public function getColumnType($name){
			return $this->column_type_manager->get($name);
		}

		/**
		 * @param $sql
		 * @param null|array $binds
		 * @param array|int|null $types
		 * @param bool $internalStatement
		 * @return ResultInterface
		 */
		abstract public function query($sql, $binds = null, $types = null,$internalStatement = false);

		/**
		 * @param $sql
		 * @param null|array $binds
		 * @param null|int|array $types
		 * @return bool|int - count affected rows OR false if failure
		 */
		abstract public function execute($sql, $binds = null, $types = null);

		/**
		 * @param $sql
		 * @return bool
		 */
		abstract public function executeSimple($sql);


		/**
		 * @return array
		 */
		abstract public function getLastErrorInfo();

		/**
		 * Error Description
		 * @return string
		 */
		abstract public function getLastErrorMessage();

		/**
		 * SQL State standart code
		 * @return string
		 */
		abstract public function getLastErrorCode();

		/**
		 * @return bool
		 */
		abstract public function hasLastError();


		/**
		 * @param null $sequenceName
		 * @return mixed
		 */
		abstract public function getLastInsertId($sequenceName = null);


		/**
		 * @param $data
		 * @param $source
		 * @param bool $many - if true $data must be array of data array
		 * @return bool
		 */
		public function create($data, $source, $many = false){
			$types = [];
			foreach($data as $k => $v){
				$types[] = isset($dataTypes[$k])?$dataTypes[$k]:null;
			}
			$sql = $this->dialect->insert($source,array_keys($data),array_values($data),$types,$many);
			$affected = $this->execute($sql->getSql(),$sql->getBindings(),$sql->getDataTypes());
			if($affected===false){
				throw new \LogicException(
					'Insert error: Code['. $this->getLastErrorCode().
					"] info: \r\n".var_export($this->getLastErrorInfo(),true)
				);
			}else{
				return $affected;
			}
		}

		/**
		 * @param null $sequenceName
		 * @return string
		 */
		abstract public function lastInsertId($sequenceName = null);

		/**
		 * @return bool
		 */
		public function haveConditionSupport(){
			return true;
		}

		/**
		 * @param $data
		 * @param $condition
		 * @param $source
		 * @param array $options
		 * @return bool
		 */
		public function update($data, $condition, $source, array $options = null){
			$types = [];
			foreach($data as $k => $v){
				$types[] = isset($dataTypes[$k])?$dataTypes[$k]:null;
			}
			$sql = $this->dialect->update($source,array_keys($data),array_values($data),$types,$condition,$options);
			$affected = $this->execute($sql->getSql(),$sql->getBindings(),$sql->getDataTypes());
			if($affected===false){
				throw new \LogicException(
					'Insert error: Code['. $this->getLastErrorCode().
					"] info: \r\n".var_export($this->getLastErrorInfo(),true)  . 'sql: "' . $sql->getSql().'"'
				);
			}else{
				return $affected;
			}
		}

		/**
		 * @param $condition
		 * @param $source
		 * @param array $options
		 * @return int
		 */
		public function delete($condition, $source, array $options = null){
			$sql = $this->dialect->delete($source,$condition,$options);
			$affected = $this->execute($sql->getSql(),$sql->getBindings(),$sql->getDataTypes());
			if($affected===false){
				throw new \LogicException(
					'Delete error: Code['. $this->getLastErrorCode().
					"] info: \r\n".var_export($this->getLastErrorInfo(),true)  . 'sql: "' . $sql->getSql().'"'
				);
			}else{
				return $affected;
			}
		}


		/**
		 * @param $condition
		 * @param $source
		 * @param $offset
		 * @param $limit
		 * @param array $options
		 * @return int
		 */
		public function count($condition, $source, $offset = null, $limit = null, array $options = null){
			$sql = $this->dialect->select(array_replace([
				'table' => $source,
				'columns' => 'COUNT(*)',
				'columns_escape' => false,
				'limit' => $limit,
				'offset' => $offset,
				'where' => $condition
			],(array)$options));
			$stmt = $this->query($sql->getSql(),$sql->getBindings(),$sql->getDataTypes(), true);
			if($stmt){
				return $stmt->fetchColumn(0);
			}else{
				return false;
			}
		}


		/**
		 * @param $columns
		 * @param $source
		 * @param $condition
		 * @param null $limit
		 * @param null $offset
		 * @param null $orderBy
		 * @param array|null $options
		 * @return ResultInterface
		 */
		public function select($columns, $source, $condition, $limit = null, $offset = null, $orderBy = null, array $options = null){
			$sql = $this->getDialect()->select(array_replace([
				'table' => $source,
			    'columns' => $columns,
			    'where' => $condition,
			    'limit' => $limit,
			    'offset' => $offset,
			    'order_by' => $orderBy,
			],(array)$options));
			$result = $this->query($sql->getSql(),$sql->getBindings(),$sql->getDataTypes());
			if($result){
				$result->setFetchMode(Db::FETCH_NUM);
				return $result;
			}else{
				throw new \LogicException($this->getLastErrorMessage() . ' SQL: "'.$sql.'"');
			}
		}


		/**
		 * @param array $definition
		 * @param int|null $fetchMode
		 * @return mixed
		 */
		public function fetchAll(array $definition, $fetchMode = null){
			$sql = $this->getDialect()->select($definition);
			if($fetchMode===null){
				$fetchMode = Db::FETCH_ASSOC;
			}
			$stmt = $this->query($sql->getSql(),$sql->getBindings(),$sql->getDataTypes(),true);
			if($stmt){
				$stmt->setFetchMode($fetchMode);
				return $stmt->fetchAll($fetchMode);
			}else{
				throw new \LogicException($this->getLastErrorMessage());
			}
		}

		/**
		 * @param array $definition
		 * @param int|null $fetchMode
		 * @return mixed
		 */
		public function fetchOne(array $definition, $fetchMode = null){
			$definition['limit'] = 1;
			$sql = $this->dialect->select($definition);
			$stmt = $this->query($sql,$sql->getBindings(),$sql->getDataTypes(),true);
			if($fetchMode===null){
				$fetchMode = Db::FETCH_ASSOC;
			}
			if($stmt){
				$stmt->setFetchMode($fetchMode);
				return $stmt->fetch($fetchMode);
			}else{
				return false;
			}
		}

		/**
		 * @param array $definition
		 * @param int $columnOffset
		 * @return mixed
		 */
		public function fetchColumn(array $definition, $columnOffset = 0){
			$sql = $this->dialect->select($definition);
			$stmt = $this->query($sql,$sql->getBindings(),$sql->getDataTypes(),true);
			if($stmt){
				return $stmt->fetchColumn($columnOffset);
			}else{
				return false;
			}
		}


		/**
		 * @param $table
		 * @param $newIncrement
		 * @return bool
		 */
		public function setAutoIncrement($table, $newIncrement){
			return $this->executeSimple($this->dialect->setAutoIncrement($table,$newIncrement));
		}


		/**
		 * @param $table
		 * @param null $database
		 * @return bool
		 */
		public function tableExists($table, $database = null){
			return !empty($this->listTables($database, $table));
		}

		/**
		 * @param $view
		 * @param null $database
		 * @return bool
		 */
		public function viewExists($view, $database = null){
			return !empty($this->listViews($database, $view));
		}


		/**
		 * @param null $database
		 * @param null $like
		 * @return array
		 */
		public function listTables($database = null, $like = null){
			$sql = $this->dialect->listTables($database,$like);
			$sth = $this->query($sql,null,null,true);
			$tables = [];
			while(($itm = $sth->fetch(Db::FETCH_NUM))!==false){
				$tables[] = $itm[0];
			}
			return $tables;
		}

		/**
		 * @param null $database
		 * @param null $like
		 * @return array
		 */
		public function listViews($database = null, $like = null){
			$sql = $this->dialect->listViews($database,$like);
			$sth = $this->query($sql,null,null,true);
			$tables = [];
			while(($itm = $sth->fetch(Db::FETCH_NUM))!==false){
				$tables[] = $itm[0];
			}
			return $tables;
		}

		public function getConnectionId(){

		}

		/**
		 * @param $name
		 * @return bool
		 */
		public function createSavepoint($name){
			return $this->executeSimple('SAVEPOINT '.$name.';');
		}

		/**
		 * @param $name
		 * @return bool
		 */
		public function releaseSavepoint($name){
			return $this->executeSimple('RELEASE SAVEPOINT '.$name.';');
		}

		/**
		 * @param $name
		 * @return bool
		 */
		public function rollbackToSavepoint($name){
			return $this->executeSimple('ROLLBACK TO SAVEPOINT '.$name.';');
		}


		/**
		 * @return bool
		 */
		public function beginTransaction(){
			if($this->executeSimple('START TRANSACTION;')){
				$this->in_transaction = true;
				return true;
			}
			return false;
		}

		/**
		 * @return bool
		 */
		public function commitTransaction(){
			if($this->executeSimple('COMMIT;')){
				$this->in_transaction = false;
				return true;
			}
			return false;
		}

		/**
		 * @return bool
		 */
		public function isSupportTransaction(){
			return true;
		}

		/**
		 * @return bool
		 */
		public function isSupportSavePoints(){
			return true;
		}

		/**
		 * @return bool
		 */
		public function rollbackTransaction(){
			if($this->executeSimple('ROLLBACK;')){
				$this->in_transaction = false;
				return true;
			}
			return false;
		}

		/**
		 * @return bool
		 */
		public function inTransaction(){
			return $this->in_transaction;
		}


		public function lockTables(){}

		public function unlockTables(){}


		/** @var int */
		protected $transaction_level = 0;

		/** @var  bool */
		protected $in_transaction = false;

		/**
		 * @return int
		 */
		public function getTransactionNestingLevel(){
			return $this->transaction_level;
		}





		/**
		 * @param bool|false $autoCommit
		 */
		public function setAutoCommit($autoCommit = false){
			$this->executeSimple('SET AUTOCOMMIT = '.($autoCommit?'1':'0').';');
		}

		/**
		 * @return $this
		 */
		public function begin(){
			if($this->transaction_level === 0){
				$this->getInternalAdapter()->beginTransaction();
			}else{
				$this->createSavepoint('POINT_LEVEL_'.$this->transaction_level);
			}
			$this->transaction_level++;
			return $this;
		}

		/**
		 * @return $this
		 */
		public function commit(){
			$this->transaction_level--;
			if($this->transaction_level === 0){
				$this->getInternalAdapter()->commit();
			}else{
				$this->releaseSavepoint('POINT_LEVEL_'.$this->transaction_level);
			}
			return $this;
		}

		/**
		 * @return $this
		 */
		public function rollback(){
			$this->transaction_level--;
			if($this->transaction_level === 0){
				$this->getInternalAdapter()->rollback();
			}else{
				$this->releaseSavepoint('POINT_LEVEL_'.$this->transaction_level);
			}
			return $this;
		}




		/**
		 * @param $identifier
		 * @return string
		 */
		public function escapeIdentifier($identifier){
			return $this->dialect->escape($identifier);
		}

		/**
		 * @param $string
		 * @return string
		 */
		public function escapeString($string){
			return $this->dialect->escapeString($string);
		}


		/**
		 * @param $table
		 * @return array
		 */
		public function describeColumns($table){
			$sql = $this->dialect->describeColumns($table);
			$sth = $this->query($sql,null,null,true);
			$columns = [];
			while((list($name, $type, $nullable, $index, $default, $extra) = $sth->fetch(Db::FETCH_NUM))!==false){
				$unsigned = false;
				$size = [null,null];
				if(preg_match('@(\w+)(?:\((\d+(?:,\d+)?)\))?(?:\s*(\w+))?@',$type,$m)){
					$type = $m[1];
					$size = $m[2]?array_replace([null,null],array_map(function($v){return intval(trim($v));},explode(',',$m[2]))):[null,null];
					$unsigned = isset($m[3]) && $m[3] ==='unsigned';
				}
				$columns[$name] = [
					'name'      => $name,
					'type'      => $type,
					'size'      => $size,
					'unsigned'  => $unsigned,
					'nullable'  => $nullable==='NO'?false:true,
					'default'   => $default,
					'auto_increment' => $extra==='auto_increment',
					'primary'   => $index === 'PRI'?true:false,
					'unique'    => $index === 'PRI' || $index === 'UNI'?true:false,
				];
			}
			return $columns;
		}

		/**
		 * @param $table
		 * @return array
		 */
		public function describeIndexes($table){
			$sql = $this->dialect->describeIndexes($table);
			$sth = $this->query($sql,null,null,true);
			$indexes = [];
			while((list($tableName, $noUnique, $keyName, $seqInIndex, $columnName, $collation, $cardinality, $subPart, $packed, $null, $type, $comment,$indexComment) = $sth->fetch(\PDo::FETCH_NUM))!==false){
				$noUnique = boolval($noUnique);
				if(isset($indexes[$keyName])){
					$indexes[$keyName]['columns'][] = $columnName;
				}else{
					$indexes[$keyName] = [
						'columns' => [$columnName],
						'type' => $keyName==='PRIMARY' && !$noUnique?'PRIMARY':(!$noUnique?'UNIQUE':'KEY'),
						'algo' => $type,
						'collation' => $collation,
						'cardinality' => intval($cardinality),
						'null' => boolval($null),
						'comment'  =>$comment,
						'index_comment' => $indexComment,
						'seq_in_index' => boolval($seqInIndex)
					];
				}
			}
			return $indexes;
		}

		/**
		 * @param $table
		 * @return array
		 */
		public function describeReferences($table){
			$database = null;
			if(is_array($table)){
				$table = array_replace([null,null],$table);
				list($database, $table) = $table;
			}
			$sth = $this->query('select key_usage.TABLE_NAME, key_usage.COLUMN_NAME, key_usage.CONSTRAINT_NAME, key_usage.REFERENCED_TABLE_NAME, key_usage.REFERENCED_COLUMN_NAME,cRefs.UPDATE_RULE,cRefs.DELETE_RULE from INFORMATION_SCHEMA.KEY_COLUMN_USAGE as key_usage LEFT JOIN INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS AS cRefs ON cRefs.CONSTRAINT_SCHEMA=key_usage.TABLE_SCHEMA AND cRefs.CONSTRAINT_NAME=key_usage.CONSTRAINT_NAME where key_usage.TABLE_SCHEMA = '.($database?$this->dialect->escapeString($database):'DATABASE()').' and key_usage.TABLE_NAME = '.$this->dialect->escapeString($table).' and referenced_column_name is not NULL;');
			$references = [];
			while( (list($table, $column, $name, $referenced_table, $referenced_column, $onUpdate, $onDelete) = $sth->fetch(Db::FETCH_NUM))){
				if(!isset($references[$name])){
					$references[$name] = [
						'name' => $name,
						'table' => $table,
						'columns' => [],
						'referenced_table' => $referenced_table,
						'referenced_columns' => [],
					    'on_update' => $onUpdate,
					    'on_delete' => $onDelete
					];
				}
				$references[$name]['columns'][] = $column;
				$references[$name]['referenced_columns'][] = $referenced_column;
			}
			return array_values($references);
		}


		/**
		 * @return Structure
		 */
		public function getStructure(){

		}

		/**
		 * @param Database $database
		 * @return bool
		 */
		public function createDatabase(Database $database){
			$sql = $this->getDialect()->createDatabase($database->getName());
			$binds = null;
			$types = null;
			if(is_array($sql)){
				list($sql,$binds,$types) = $sql;
			}
			return $this->execute($sql,$binds,$types);
		}

		/**
		 * @param Database $database
		 * @param $newName
		 * @return bool
		 */
		public function renameDatabase(Database $database,$newName){
			$sql = $this->getDialect()->renameDatabase($database->getName(),$newName);
			$binds = null;
			$types = null;
			if(is_array($sql)){
				list($sql,$binds,$types) = $sql;
			}
			return $this->execute($sql,$binds,$types);
		}

		/**
		 * @param Database $database
		 * @return bool
		 */
		public function removeDatabase(Database $database){
			$sql = $this->getDialect()->removeDatabase($database->getName());
			$binds = null;
			$types = null;
			if(is_array($sql)){
				list($sql,$binds,$types) = $sql;
			}
			return $this->execute($sql,$binds,$types);
		}

		/**
		 * @param Table $table
		 * @return bool
		 */
		public function createTable(Table $table){
			$columns = [];
			foreach($table->getColumns() as $c){
				$columns[] = $c->toArray();
			}
			$sql = $this->getDialect()->createTable([$table->getDatabase()->getName(),$table->getName()],$columns);
			$binds = null;
			$types = null;
			if(is_array($sql)){
				list($sql,$binds,$types) = $sql;
			}
			return $this->execute($sql,$binds,$types);
		}

		/**
		 * @param Table $table
		 * @param $newName
		 *
		 * @return bool
		 */
		public function renameTable(Table $table, $newName){
			$sql = $this->getDialect()->renameTable([$table->getName(),$table->getDatabase()->getName()],$newName);
			$binds = null;
			$types = null;
			if(is_array($sql)){
				list($sql,$binds,$types) = $sql;
			}
			return $this->execute($sql,$binds,$types);
		}

		/**
		 * @param Table $table
		 * @return bool
		 */
		public function removeTable(Table $table){
			$sql = $this->getDialect()->removeTable([$table->getName(),$table->getDatabase()->getName()]);
			$binds = null;
			$types = null;
			if(is_array($sql)){
				list($sql,$binds,$types) = $sql;
			}
			return $this->execute($sql,$binds,$types);
		}

		/**
		 * @param Column $column
		 * @return bool
		 */
		public function addColumn(Column $column){
			$sql = $this->getDialect()->addColumn(
				[
					$column->getTable()->getName(),
					$column->getTable()->getDatabase()->getName()
				],
				$column->toArray()
			);
			$binds = null;
			$types = null;
			if(is_array($sql)){
				list($sql,$binds,$types) = $sql;
			}
			return $this->execute($sql,$binds,$types);
		}

		/**
		 * @param Column $column
		 * @return bool
		 */
		public function removeColumn(Column $column){
			$sql = $this->getDialect()->removeColumn(
				[
					$column->getTable()->getName(),
					$column->getTable()->getDatabase()->getName()
				],
				$column->getName()
			);
			$binds = null;
			$types = null;
			if(is_array($sql)){
				list($sql,$binds,$types) = $sql;
			}
			return $this->execute($sql,$binds,$types);
		}

		/**
		 * @param Column $column
		 * @return bool
		 */
		public function modifyColumn(Column $column){
			$sql = $this->getDialect()->modifyColumn(
				[
					$column->getTable()->getName(),
					$column->getTable()->getDatabase()->getName()
				],
				$column->getName(),
				$column->toArray()
			);
			$binds = null;
			$types = null;
			if(is_array($sql)){
				list($sql,$binds,$types) = $sql;
			}
			return $this->execute($sql,$binds,$types);
		}

		/**
		 * @param Column $column
		 * @param $newName
		 * @return bool
		 */
		public function renameColumn(Column $column, $newName){
			$sql = $this->getDialect()->renameColumn(
				[
					$column->getTable()->getName(),
					$column->getTable()->getDatabase()->getName()
				],
				$column->getName(), $newName
			);
			$binds = null;
			$types = null;
			if(is_array($sql)){
				list($sql,$binds,$types) = $sql;
			}
			return $this->execute($sql,$binds,$types);
		}

		/**
		 * @param ForeignKey $foreignKey
		 * @return bool
		 */
		public function addForeignKey(ForeignKey $foreignKey){
			$origins = [];
			foreach($foreignKey->getOriginColumns() as $c){
				$origins[] = $c->getName();
			}
			$references = [];
			foreach($foreignKey->getReferenceColumns() as $c){
				$references[] = $c->getName();
			}
			$sql = $this->getDialect()->addForeignKey(
				[
					$foreignKey->getOriginTable()->getName(),
					$foreignKey->getOriginTable()->getDatabase()->getName()
				],
				$foreignKey->getName(),
				$origins, $references,
				[
					$foreignKey->getReferenceTable()->getName(),
					$foreignKey->getReferenceTable()->getDatabase()->getName()
				],
				$foreignKey->getReactionDelete(),
				$foreignKey->getReactionUpdate()
			);
			$binds = null;
			$types = null;
			if(is_array($sql)){
				list($sql,$binds,$types) = $sql;
			}
			return $this->execute($sql,$binds,$types);
		}

		/**
		 * @param ForeignKey $foreignKey
		 * @return bool
		 */
		public function removeForeignKey(ForeignKey $foreignKey){
			$sql = $this->getDialect()->removeForeignKey(
				[
					$foreignKey->getOriginTable()->getName(),
					$foreignKey->getOriginTable()->getDatabase()->getName()
				],
				$foreignKey->getName()
			);
			$binds = null;
			$types = null;
			if(is_array($sql)){
				list($sql,$binds,$types) = $sql;
			}
			return $this->execute($sql,$binds,$types);
		}

		/**
		 * @param Index $index
		 * @return bool
		 */
		public function addIndex(Index $index){
			$columns = [];
			/**
			 * @var Column $c,
			 * @var int $size,
			 * @var string $direction
			 */
			foreach($index->getColumnsDetails() as list($c,$size,$direction)){
				$columns[$c->getName()] = [];
				if($size!==null)$columns[$c->getName()]['size'] = $size;
				if($direction)$columns[$c->getName()]['direction'] = $direction;
			}
			$sql = $this->getDialect()->addIndex(
				[
					$index->getTable()->getName(),
					$index->getTable()->getDatabase()->getName()
				],
				$index->getName(), $columns,$index->getType(),$index->getAlgo()
			);
			$binds = null;
			$types = null;
			if(is_array($sql)){
				list($sql,$binds,$types) = $sql;
			}
			return $this->execute($sql,$binds,$types);
		}

		/**
		 * @param Index $index
		 * @return bool
		 */
		public function removeIndex(Index $index){
			$sql = $this->getDialect()->removeIndex(
				[$index->getTable()->getName(),$index->getTable()->getDatabase()->getName()],
				$index->getName()
			);
			$binds = null;
			$types = null;
			if(is_array($sql)){
				list($sql,$binds,$types) = $sql;
			}
			return $this->execute($sql,$binds,$types);
		}

		/**
		 * @return object
		 */
		abstract public function getInternalAdapter();



	}
}

