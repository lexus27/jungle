<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 01.03.2016
 * Time: 13:14
 */
namespace Jungle\Data\Storage\Db\Structure {

	use Jungle\Data\Storage\Db\Dialect;
	use Jungle\Data\Storage\Db\Structure;
	use Jungle\Util\Named\NamedInterface;

	/**
	 * Class Database
	 * @package Jungle\Data\Storage\Db\Structure
	 * TargetTable schema structure
	 */
	class Database extends StructureObject implements NamedInterface{

		/** @var  string */
		protected $name;

		/** @var  Structure */
		protected $structure;

		/** @var  Table[] */
		protected $tables = [];

		/**
		 * Получить имя объекта
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * Выставить имя объекту
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			if($this->name!==$name){
				if($this->_adapter && !$this->isNew()){
					if($this->_adapter->renameDatabase($this,$name)){
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
		 * @param Table $table
		 * @return $this
		 */
		public function addTable(Table $table,$appliedIn = false){
			if($this->searchTable($table)===false){
				$this->tables[] = $table;
				if(!$appliedIn){
					$table->setDatabase($this,true);
				}
			}
			return $this;
		}

		/**
		 * @param Table $table
		 * @return mixed
		 */
		public function searchTable(Table $table){
			return array_search($table,$this->tables,true);
		}

		/**
		 * @param Table $table
		 * @return $this
		 */
		public function removeTable(Table $table,$appliedIn = false){
			$i = $this->searchTable($table);
			if($i!==false){
				array_splice($this->tables,$i,1);
				if(!$appliedIn){
					$table->setDatabase(null,true,true);
				}
			}
			return $this;
		}

		/**
		 * @param $name
		 * @return Table
		 */
		public function getTable($name){

		}

		/**
		 * @param $name
		 * @return Table
		 */
		public function newTable($name){
			$table = new Table($this->_adapter);
			$table->setName($name);
			return $table;
		}

		/**
		 * @return Table[]
		 */
		public function getTables(){
			return $this->tables;
		}

		/**
		 * @return $this
		 */
		public function save(){
			if($this->isNew()){
				if($this->_adapter->createDatabase($this)){
					$this->_new = false;
				}else{

				}

			}
			foreach($this->tables as $table){
				$table->save();
			}
		}


		/**
		 *
		 */
		public function onNew(){
			foreach($this->getTables() as $table){
				$table->setNew(true);
			}
		}

		/**
		 * @return $this
		 */
		public function remove(){
			if($this->structure && !$this->isNew()){
				if($this->_adapter->removeDatabase($this)){
					$this->setNew(true);
				}else{

				}
			}
		}


		/**
		 * @param bool $ifNotExists
		 * @param array $tablesToSql
		 * @param Dialect|null $dialect
		 * @return string
		 */
		public function getTablesDefinitionSql($ifNotExists = false,array $tablesToSql = null,Dialect $dialect = null){
			if(!$dialect)$dialect = $this->_adapter->getDialect();
			$tables = $this->getTables();

			if(is_array($tablesToSql)){
				$tables = array_filter($tables,function(Table $table) use ($tablesToSql){
					return in_array($table->getName(),$tablesToSql,true);
				});
			}
			$sql = [];
			foreach($tables as $table){
				$sql[] = $table->getCreateSql($ifNotExists,$dialect);
			}

			$sql = implode("\r\n\r\n",$sql)."\r\n";

			foreach($tables as $table){
				$sql.= $table->getRelationSql($dialect)."\r\n";
			}

			return $sql;
		}

		/**
		 * @param null $tablesToSql
		 * @param Dialect|null $dialect
		 * @return \string[]
		 */
		public function getTablesContentSql($tablesToSql = null, Dialect $dialect = null){
			if(!$dialect)$dialect = $this->_adapter->getDialect();
			$tables = $this->getTables();
			if(is_array($tablesToSql)){
				$tables = array_filter($tables,function(Table $table) use ($tablesToSql){
					return in_array($table->getName(),$tablesToSql,true);
				});
			}
			$sql = [];
			usort($tables,function(Table $t1, Table $t2){
				$fk1 = $t1->getForeignKeys();
				$fk2 = $t2->getForeignKeys();
				if ($fk1 && $fk2) {
					return 0;
				}
				return (!$fk1) ? -1 : 1;
			});
			foreach($tables as $table){
				$sql[$table->getName()] = $table->getRecordsSql($dialect);
			}
			return $sql;

		}


	}
}

