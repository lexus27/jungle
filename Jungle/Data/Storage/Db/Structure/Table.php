<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 17.02.2016
 * Time: 4:12
 */
namespace Jungle\Data\Storage\Db\Structure {

	use Jungle\Data\Storage\Db;
	use Jungle\Data\Storage\Db\Dialect;
	use Jungle\Data\Storage\Db\Structure\Column\ForeignKey;
	use Jungle\Data\Storage\Db\Structure\Column\Index;
	use Jungle\Util\NamedInterface;

	/**
	 * Class TargetTable
	 * @package Jungle\Data\Storage\Db\Structure
	 */
	class Table extends StructureObject implements NamedInterface{

		/** @var  Database owner */
		protected $database;


		/** @var string */
		protected $name;


		/** @var Column[] */
		protected $columns      = [];

		/** @var ForeignKey[] */
		protected $foreign_keys = [];

		/** @var Index[]  */
		protected $indexes      = [];



		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			if($this->name!==$name){
				if($this->_adapter && !$this->isNew()){
					if($this->_adapter->renameTable($this,$name)){
						$this->name = $name;
					}else{

					}
				}else{
					$this->name = $name;
				}

			}
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getName(){
			return $this->name;
		}


		/**
		 * @return int
		 */
		public function getCount(){
			return $this->_adapter->fetchColumn([
				'table' => [$this->getName(),$this->getDatabase()->getName()],
				'columns' => 'COUNT(*)'

			]);
		}

		/**
		 * @return int
		 */
		public function getSize(){

		}

		/**
		 * @return array
		 */
		public function getRecords(){
			return $this->_adapter->fetchAll([
				'table' => [$this->getName(),$this->getDatabase()->getName()],
				'columns' => $this->_adapter->getDialect()->escapeColumns($this->getColumnNames()),
			],Db::FETCH_NUM);
		}

		/**
		 * @param Database $database
		 * @param bool $appliedInDatabase
		 * @param bool $appliedInOld
		 * @return $this
		 */
		public function setDatabase(Database $database = null, $appliedInDatabase = false, $appliedInOld = false){
			$old = $this->database;
			if($old!==$database){
				$this->database = $database;
				if($database && !$appliedInDatabase){
					$database->addTable($this,true);
				}
				if($old && !$appliedInOld){
					$old->removeTable($this,true);
				}
				if($old){
					$this->remove();
				}
			}
			return $this;
		}

		/**
		 * @return Database
		 */
		public function getDatabase(){
			return $this->database;
		}


		/**
		 * @param ForeignKey $foreignKey
		 * @param bool $appliedInKey
		 * @return $this
		 */
		public function addForeignKey(ForeignKey $foreignKey, $appliedInKey = false){
			if($this->searchForeignKey($foreignKey)===false){
				if(!$appliedInKey){
					$foreignKey->setOriginTable($this,true);
				}
				$this->foreign_keys[] = $foreignKey;
				$this->setDirty(true);
			}
			return $this;
		}

		/**
		 * @param ForeignKey $foreignKey
		 * @return mixed
		 */
		public function searchForeignKey(ForeignKey $foreignKey){
			return array_search($foreignKey,$this->foreign_keys,true);
		}

		/**
		 * @param ForeignKey $foreignKey
		 * @param bool $appliedInKey
		 * @return $this
		 */
		public function removeForeignKey(ForeignKey $foreignKey,$appliedInKey = false){
			if(($i = $this->searchForeignKey($foreignKey))!==false){
				array_splice($this->foreign_keys,$i,1);
				if(!$appliedInKey){
					$foreignKey->remove();
				}
			}
			return $this;
		}

		/**
		 * @param $name
		 * @return ForeignKey|null
		 */
		public function getForeignKey($name){
			foreach($this->getForeignKeys() as $key){
				if($key->getName() === $name){
					return $key;
				}
			}
			return null;
		}

		/**
		 * @param $name
		 * @return ForeignKey
		 */
		public function newForeignKey($name){
			$fk = new ForeignKey($this->_adapter);
			$fk->setName($name);
			return $fk;
		}


		/**
		 * @param Index $index
		 * @param bool $appliedInIndex
		 * @return $this
		 */
		public function addIndex(Index $index, $appliedInIndex = false){
			if($this->searchIndex($index)===false){
				if(!$appliedInIndex){
					$index->setTable($this);
				}
				$this->indexes[] = $index;
				$this->setDirty(true);
			}
			return $this;
		}

		/**
		 * @param Index $index
		 * @return mixed
		 */
		public function searchIndex(Index $index){
			return array_search($index,$this->indexes,true);
		}

		/**
		 * @param Index $index
		 * @param bool $appliedInIndex
		 * @return $this
		 */
		public function removeIndex(Index $index,$appliedInIndex = false){
			if(($i = $this->searchIndex($index))!==false){
				array_splice($this->indexes,$i,1);
				if(!$appliedInIndex){
					$index->remove();
				}
			}
			return $this;
		}

		/**
		 * @return string[]
		 */
		public function getColumnNames(){
			$columnNames = $this->getColumns();
			foreach($columnNames as & $c){
				$c = $c->getName();
			}
			return $columnNames;
		}

		/**
		 * @param $name
		 * @return Index|null
		 */
		public function getIndex($name){
			foreach($this->getIndexes() as $index){
				if($index->getName() === $name){
					return $index;
				}
			}
			return null;
		}

		/**
		 * @param $name
		 * @return Index
		 */
		public function newIndex($name){
			$index = new Index($this->_adapter);
			$index->setName($name);
			return $index;
		}

		/**
		 * @param Column $column
		 * @param bool $appliedIn
		 * @return $this
		 */
		public function addColumn(Column $column, $appliedIn = false){
			$i = $this->searchColumn($column);
			if($i !== false){
				array_splice($this->columns,$i,1);
			}else{
				if(!$appliedIn){
					$column->setTable($this,true);
				}
			}
			$last = $this->columns[count($this->columns) - 1];
			$column->setAfter($last);
			$this->columns[] = $column;
			return $this;
		}

		/**
		 * @param Column $column
		 * @return mixed
		 */
		public function searchColumn(Column $column){
			return array_search($column,$this->getColumns(),true);
		}

		/**
		 * @param Column $column
		 * @param bool $appliedInColumn
		 * @return $this
		 */
		public function removeColumn(Column $column,$appliedInColumn = false){
			$i = $this->searchColumn($column);
			if($i!==false){
				foreach($this->getIndexes() as $index){
					$index->removeColumn($column);
					if(!$index->getColumns()){
						$this->removeIndex($index);
					}
				}
				foreach($this->getForeignKeys() as $key){
					$key->removeColumn($column);
					if(!$key->getOriginColumns()){
						$this->removeForeignKey($key);
					}
				}
				array_splice($this->columns,$i,1);
				if(!$appliedInColumn){
					$column->setTable(null,true,true);
				}
			}
			return $this;
		}

		/**
		 * @param $name
		 * @return Column|null
		 */
		public function getColumn($name){
			foreach($this->getColumns() as $c){
				if($c->getName() === $name){
					return $c;
				}
			}
			return null;
		}

		/**
		 * @param $name
		 * @return Column
		 */
		public function newColumn($name){
			$column = new Column($this->_adapter);
			$column->setName($name);
			return $column;
		}

		/**
		 * @return Column[]
		 */
		public function getColumns(){
			return $this->columns;
		}

		/**
		 * @return Column\Index[]
		 */
		public function getIndexes(){
			return $this->indexes;
		}

		/**
		 * @return Column\ForeignKey[]
		 */
		public function getForeignKeys(){
			return $this->foreign_keys;
		}

		/**
		 * @return $this
		 */
		public function save(){
			if($this->isNew()){
				$this->_adapter->createTable($this);
				$this->setNew(false);
			}
			if($this->isDirty()){
				foreach($this->foreign_keys as $key){
					$key->save();
				}
				foreach($this->indexes as $index){
					$index->save();
				}
				foreach($this->columns as $column){
					$column->save();
				}
				$this->setDirty(false);
			}
		}

		/**
		 * @param bool $ifNotExists
		 * @param Dialect $dialect
		 * @return string
		 */
		public function getCreateSql($ifNotExists = false, Dialect $dialect = null){
			if(!$dialect)$dialect = $this->_adapter->getDialect();
			$columns = [];
			foreach($this->getColumns() as $column){
				$columns[] = $column->toArray();
			}
			$indexes = [];
			foreach($this->getIndexes() as $index){
				/**
				 * @var Column $column
				 * @var int $size
				 * @var string $direction
				 */
				$c = [];
				foreach($index->getColumnsDetails() as list($column,$size,$direction)){
					$c[$column->getName()] = [];
					if($size)$c[$column->getName()]['size'] = $size;
					if($direction)$c[$column->getName()]['direction'] = $direction;
				}
				$indexes[] = [
					'name'      => $index->getName(),
					'type'      => $index->getType(),
					'algo'      => $index->getAlgo(),
					'columns'   => $c
				];
			}
			return $dialect->createTable($this->getName(),$columns,$indexes,[],$ifNotExists);
		}

		/**
		 * @param Dialect|null $dialect
		 * @return string
		 */
		public function getRelationSql(Dialect $dialect = null){
			if(!$dialect)$dialect = $this->_adapter->getDialect();

			$servant = new Db\Sql();
			foreach($this->getForeignKeys() as $key){
				$origins = [];
				foreach($key->getOriginColumns() as $c){
					$origins[] = $c->getName();
				}
				$references = [];
				foreach($key->getReferenceColumns() as $c){
					$references[] = $c->getName();
				}
				$servant->push($dialect->addForeignKey(
					$this->getName(),$key->getName(),
					$origins,$references,
					$key->getReferenceTable()->getName(),
					$key->getReactionDelete(),$key->getReactionUpdate()
				),"\r\n\t")->ending();
			}
			return $servant->getSql();

		}


		/**
		 * @param bool $singleAlter
		 * @param Dialect $dialect
		 * @return string
		 */
		public function getRecordsSql($singleAlter = false, Dialect $dialect = null){
			if(!$dialect)$dialect = $this->_adapter->getDialect();

			$servant = new Db\Sql();

			$columns = $this->getColumnNames();
			if($singleAlter){
				$dialect->insert($servant, $this->getName(),$columns);
				foreach($this->getRecords() as $record){
					$dialect->addInlineInsertValues($servant , $record);
				}
				$servant->ending();
				return $servant->getSql();
			}else{
				foreach($this->getRecords() as $record){
					$dialect->insert($servant,$this->getName(),null,$columns);
					$dialect->addInlineInsertValues($servant , $record);
					$servant->ending();
				}
				return $servant->getSql();
			}
		}



		public function onNew(){
			foreach($this->getForeignKeys() as $o){
				$o->setNew(true);
			}
			foreach($this->getIndexes() as $o){
				$o->setNew(true);
			}
			foreach($this->getColumns() as $o){
				$o->setNew(true);
			}
		}

		/**
		 * @return $this
		 */
		public function remove(){
			if($this->database && !$this->isNew()){
				if($this->_adapter->removeTable($this)){
					$this->setNew(true);

					return true;
				}else{
					return false;
				}
			}
			return true;
		}

		/***
		 *
		 */
		public function __clone(){
			$this->database = null;
			$this->_new     = true;
			$columns = [];
			foreach($this->getColumns() as & $column){
				$column = clone $column;
				$columns[$column->getName()] = $column;
				$column->setTable($this);
			}
			foreach($this->getForeignKeys() as & $key){
				$key = clone $key;
				$c = [];
				foreach($key->getOriginColumns() as $col){
					$c[] = $columns[$col->getName()];
				}
				$key->setOriginTableOnClone($this,$c);
			}
			foreach($this->getIndexes() as & $index){
				$index = clone $index;
				$c = [];
				foreach($index->getColumns() as $col){
					$c[] = $columns[$col->getName()];
				}
				$index->setTableOnClone($this,$c);
			}

		}
	}
}

