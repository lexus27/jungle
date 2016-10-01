<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 01.03.2016
 * Time: 13:46
 */
namespace Jungle\Data\Storage\Db\Structure\Column {

	use Jungle\Data\Storage\Db\Structure\Column;
	use Jungle\Data\Storage\Db\Structure\StructureObject;
	use Jungle\Data\Storage\Db\Structure\Table;
	use Jungle\Util\NamedInterface;

	/**
	 * Class Index
	 * @package Jungle\Data\Storage\Db\Structure\Column
	 */
	class Index extends StructureObject implements NamedInterface{

		const T_PRIMARY     = 'PRIMARY';

		const T_KEY         = 'KEY';

		const T_UNIQUE      = 'UNIQUE';

		const T_FULLTEXT    = 'FULLTEXT';

		const T_SPATIAL     = 'SPATIAL';


		const A_BTREE   = 'BTREE';

		const A_HASH    = 'HASH';

		const A_RTREE   = 'RTREE';


		const DIRECTION_ASC     = 'ASC';

		const DIRECTION_DESC    = 'DESC';


		/** @var  Column[] */
		protected $columns = [];

		/** @var array  */
		protected $columns_details = [];

		/** @var  Table */
		protected $table;

		/** @var  string */
		protected $name;

		/** @var  mixed self::T_CONST */
		protected $type = self::T_KEY;

		/** @var mixed  self::A_CONST  */
		protected $algo = self::A_BTREE;

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
			$this->name = $name;
			return $this;
		}

		/**
		 * @param Table $table
		 * @param bool $appliedIn
		 * @return $this
		 */
		public function setTable(Table $table,$appliedIn = false){
			if($this->table===null){
				$this->table = $table;
				if(!$appliedIn){
					$table->addIndex($this,true);
				}
			}else{
				throw new \LogicException('TargetTable already is defined!');
			}
			return $this;
		}

		/**
		 * @return Table
		 */
		public function getTable(){
			return $this->table;
		}

		/**
		 * @param Column $column
		 * @param null $count
		 * @param null $direction
		 * @return $this
		 */
		public function addColumn(Column $column,$count = null, $direction = null){
			if($column->getTable() !== $this->table){
				throw new \LogicException('Column table is not equal index table');
			}
			$i = $this->searchColumn($column);
			if($i!==false){
				array_splice($this->columns,$i,1);
				array_splice($this->columns_details,$i,1);
			}
			$this->columns[]            = $column;
			$this->columns_details[]    = [$count,$direction];

			$this->setDirty(true);

			return $this;
		}

		/**
		 * @param Column $column
		 * @return mixed
		 */
		public function searchColumn(Column $column){
			return array_search($column,$this->columns,true);
		}

		/**
		 * @param Column $column
		 * @return $this
		 */
		public function removeColumn(Column $column){
			if(($i = $this->searchColumn($column))!==false){
				array_splice($this->columns,$i,1);
				array_splice($this->columns_details,$i,1);
			}
			return $this;
		}

		/**
		 * @return array
		 */
		public function getColumnsDetails(){
			$a = [];
			foreach($this->columns as $i => $column){
				$a[] = [$column,$this->columns_details[$i][0],$this->columns_details[$i][1]];
			}
			return $a;
		}

		/**
		 * @return Column[]
		 */
		public function getColumns(){
			return $this->columns;
		}

		/**
		 * @return mixed
		 */
		public function getType(){
			return $this->type;
		}

		/**
		 * @param mixed $type
		 * @return $this
		 */
		public function setType($type = self::T_KEY){
			$this->type = $type;
			return $this;
		}

		/**
		 * @param string $algo
		 */
		public function setAlgo($algo = self::A_BTREE){
			$this->algo = $algo;
		}

		/**
		 * @return mixed
		 */
		public function getAlgo(){
			return $this->algo;
		}

		/**
		 * @return $this
		 */
		public function save(){
			if(!$this->table){
				return false;
			}
			if($this->isNew()){
				if($this->_adapter->addIndex($this)){
					$this->setNew(false);
					$this->setDirty(false);
				}else{

				}

			}
			if($this->isDirty()){
				if($this->_adapter->removeIndex($this)){
					if($this->_adapter->addIndex($this)){
						$this->setNew(false);
						$this->setDirty(false);
					}else{

					}
				}else{

				}
			}
			return true;
		}

		/**
		 *
		 */
		public function remove(){
			if($this->table && !$this->isNew()){
				$this->table->removeIndex($this,true);
				if($this->_adapter->removeIndex($this)){
					$this->setNew(true);
				}else{

				}
			}
		}

		/**
		 *
		 */
		public function __clone(){
			$this->table = null;
			$this->_new = true;
		}

		/**
		 * @param Table $table
		 * @param Column[] $columns
		 * @return $this
		 */
		public function setTableOnClone(Table $table , array $columns){
			foreach($columns as $i => $col){
				if($col->getName() === $this->columns[$i]->getName()){
					$this->columns[$i] = $col;
				}
			}
			$this->table = $table;
			return $this;
		}
	}
}

