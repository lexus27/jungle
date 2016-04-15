<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 04.03.2016
 * Time: 11:23
 */
namespace Jungle\Storage\Db {

	use Jungle\Storage\Db;
	use Jungle\Storage\Db\Structure\Column;
	use Jungle\Storage\Db\Structure\Column\ForeignKey;
	use Jungle\Storage\Db\Structure\Column\Index;
	use Jungle\Storage\Db\Structure\Column\Type;
	use Jungle\Storage\Db\Structure\Column\TypePool;
	use Jungle\Storage\Db\Structure\Database;
	use Jungle\Storage\Db\Structure\Table;

	/**
	 * Class Adapter
	 * @package Jungle\Storage\Db
	 */
	abstract class Adapter{

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
		 * @param null|int|array $types
		 * @return \PDOStatement
		 */
		abstract public function query($sql, $binds = null, $types = Db\Structure\Column::BIND_PARAM_STR);
		/**
		 * @param $sql
		 * @param null|array $binds
		 * @param null|int|array $types
		 * @return bool|int
		 */
		abstract public function execute($sql, $binds = null, $types = Db\Structure\Column::BIND_PARAM_STR);



		/**
		 * @return array
		 */
		abstract public function getLastErrorInfo();

		/**
		 * @return string
		 */
		abstract public function getLastErrorCode();


		/**
		 * @param null $sequenceName
		 * @return mixed
		 */
		abstract public function getLastInsertId($sequenceName = null);

		/**
		 * @param $table
		 * @param $values
		 * @param null|array|string $fields
		 * @param null|array|int $dataTypes
		 * @return mixed
		 */
		public function insert($table,$values,$fields = null,$dataTypes = null){
			$dialect = $this->dialect;
			$sql = $dialect->insert($table,$fields).'('.implode(',',array_fill(0,count($values)-1,'?')).')';
			$stmt = $this->query($sql,$values,$dataTypes);
			if($stmt===false){
				throw new \LogicException(
					'Insert error: Code['. $this->getLastErrorCode().
					"] info: \r\n".var_export($this->getLastErrorInfo(),true)
				);
			}else{
				return true;
			}
		}

		/**
		 * @param $table
		 * @param $data
		 * @param null|array|int $dataTypes
		 * @return mixed
		 */
		public function insertAsDict($table,$data,$dataTypes = null){
			return $this->insert($table, array_values($data), array_keys($data), $dataTypes);
		}

		/**
		 * @param array $definition
		 * @return array [(string) SQL, (array) Bindings, (array) DataTypes]
		 */
		public function getSelectSql(array $definition){
			return $this->getDialect()->select($definition);
		}

		/**-
		 * @param $table
		 * @param $values
		 * @param $fields
		 * @param null|array|int $dataTypes
		 * @param null|string|array $whereCondition
		 * @return mixed
		 */
		public function update($table, $values, $fields, $dataTypes = null, $whereCondition = null ){
			$dialect = $this->dialect;
			$sql = $dialect->update($table);
			$set = [];
			$bindings = [];
			foreach($values as $i => $value){
				$set[] = $this->dialect->escape($fields[$i]) . ' = ?';
				$bindings[] = $value;
			}
			$sql.= ' SET' . implode(",\r\n",$set);

			if($whereCondition && ($whereCondition = $dialect->prepareCondition($whereCondition))){
				list($condition,$binds,$types) = $whereCondition;
				$dialect->mergeBindings(
					$bindings,$binds,
					$dataTypes,$types
				);
				$sql.= ' WHERE ' . $condition;
			}

			$stmt = $this->query($sql,$values,$dataTypes);
			if($stmt===false){
				throw new \LogicException(
					'Insert error: Code['. $this->getLastErrorCode().
					"] info: \r\n".var_export($this->getLastErrorInfo(),true)
				);
			}else{
				return true;
			}
		}

		/**
		 * @param $table
		 * @param $data
		 * @param null|string|array $whereCondition
		 * @param null|array|int $dataTypes
		 * @return mixed
		 */
		public function updateAsDict($table,$data,$dataTypes = null, $whereCondition = null){
			return $this->update($table,array_values($data),array_keys($data),$dataTypes, $whereCondition);
		}

		/**
		 * @param $table
		 * @param mixed $whereCondition
		 * @return mixed
		 */
		public function delete($table,$whereCondition){
			$dialect = $this->dialect;
			$sql = $dialect->delete($table);
			$bindings = [];
			$dataTypes = [];
			if($whereCondition && ($whereCondition = $dialect->prepareCondition($whereCondition))){
				list($condition,$binds,$types) = $whereCondition;
				$sql.= ' WHERE ' . $condition;
				$dialect->mergeBindings(
					$bindings,$binds,
					$dataTypes,$types
				);
			}
			$stmt = $this->query($sql,$bindings,$dataTypes);
			if($stmt===false){
				throw new \LogicException(
					'Delete error: Code['. $this->getLastErrorCode().
					"] info: \r\n".var_export($this->getLastErrorInfo(),true)
				);
			}else{
				return true;
			}
		}

		/**
		 * @param $sql
		 * @param int $fetchMode
		 * @param null $bindParams
		 * @param null $bindTypes
		 * @return array
		 */
		abstract public function fetchAll($sql, $fetchMode = Db::FETCH_ASSOC, $bindParams = null, $bindTypes = null);

		/**
		 * @param $sql
		 * @param int $fetchMode
		 * @param null $bindParams
		 * @param null $bindTypes
		 * @return mixed
		 */
		abstract public function fetchOne($sql, $fetchMode = Db::FETCH_ASSOC, $bindParams = null, $bindTypes = null);

		/**
		 * @param $sql
		 * @param null $bindings
		 * @param int $column
		 * @return mixed
		 */
		abstract public function fetchColumn($sql, $bindings = null, $column = 0);


		/**
		 * @param $table
		 * @return bool
		 */
		public function tableExists($table){

		}

		/**
		 * @param $view
		 * @return bool
		 */
		public function viewExists($view){

		}


		/**
		 * @param null $database
		 * @return array
		 */
		public function listTables($database = null){

		}

		/**
		 * @param null $database
		 */
		public function listViews($database = null){

		}

		public function describeColumns($table){

		}

		/**
		 * @param $table
		 */
		public function describeIndexes($table){

		}

		/**
		 * @param $table
		 */
		public function describeReferences($table){

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
			$sql = $this->getDialect()->createTable($table->getDatabase()->getName(),$table->getName(),$columns);
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

