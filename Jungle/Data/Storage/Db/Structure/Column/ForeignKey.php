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
	 * Class ForeignKey
	 * @package Jungle\Data\Storage\Db\Structure\Column
	 */
	class ForeignKey extends StructureObject implements NamedInterface{

		const R_NOACTION    = null;

		const R_CASCADE     = 'cascade';

		const R_RESTRICT    = 'restrict';

		const R_SETNULL     = 'setnull';


		/** @var  string*/
		protected $name;


		/** @var  Table */
		protected $origin_table;

		/** @var  Column[]  */
		protected $origin_columns = [];


		/** @var  Table */
		protected $reference_table;

		/** @var  Column[]  */
		protected $reference_columns = [];


		/** @var  int */
		protected $reaction_update = self::R_RESTRICT;

		/** @var  int */
		protected $reaction_delete = self::R_RESTRICT;


		/**
		 * @param Table $table
		 * @return $this
		 */
		public function setReferenceTable(Table $table){
			if($this->reference_table===null){
				$this->reference_table = $table;
			}else{
				throw new \LogicException('Reference table is defined!');
			}

			return $this;
		}

		/**
		 * @return Table
		 */
		public function getReferenceTable(){
			return $this->reference_table;
		}

		/**
		 * @return Column[]
		 */
		public function getReferenceColumns(){
			return $this->reference_columns;
		}

		/**
		 * @param Column $column
		 * @return mixed
		 */
		public function indexOfReference(Column $column){
			return array_search($column,$this->reference_columns,true);
		}

		/**
		 * @param Table $table
		 * @param bool $appliedIn
		 * @return $this
		 */
		public function setOriginTable(Table $table,$appliedIn = false){
			if($this->origin_table===null){
				$this->origin_table = $table;
				if(!$appliedIn){
					$table->addForeignKey($this,true);
				}
			}else{
				throw new \LogicException('Origin table already is defined!');
			}
			return $this;
		}

		/**
		 * @return Table
		 */
		public function getOriginTable(){
			return $this->origin_table;
		}

		/**
		 * @return Column[]
		 */
		public function getOriginColumns(){
			return $this->origin_columns;
		}

		/**
		 * @param Column $column
		 * @return $this
		 */
		public function removeColumn(Column $column){
			$changed = false;
			foreach($this->origin_columns as $i => $c){
				if($column === $c){
					array_splice($this->origin_columns,$i,1);
					array_splice($this->reference_columns,$i,1);
					$changed = true;
				}
			}
			if($changed){
				$this->setDirty(true);
			}
			return $this;
		}

		/**
		 * @param Column $column
		 * @return mixed
		 */
		public function indexOfOrigin(Column $column){
			return array_search($column,$this->origin_columns,true);
		}

		/**
		 * Add pair from Column to ReferenceColumn
		 * @param Column $column
		 * @param Column $referenceColumn
		 * @return $this
		 */
		public function addPair(Column $column, Column $referenceColumn){
			if($this->origin_table !== $column->getTable()){
				throw new \LogicException('column is not contains in base table');
			}
			if($this->reference_table !== $referenceColumn->getTable()){
				throw new \LogicException('Reference column is not contains in reference table');
			}
			if(!$column->isEqualType($referenceColumn)){
				throw new \LogicException('Column type is not valid');
			}

			$this->origin_columns[]     = $column;
			$this->reference_columns[]  = $referenceColumn;

			$this->setDirty(true);

			return $this;
		}

		/**
		 * @return Column[]
		 */
		public function getPairs(){
			$a = [];
			foreach($this->origin_columns as $i => $c){
				$a[] = [$c, $this->reference_columns[$i]];
			}
			return $a;
		}



		/***
		 * @param $reaction
		 * @return $this
		 */
		public function setReactionUpdate($reaction = self::R_RESTRICT){
			if($this->reaction_update !== $reaction){
				$this->reaction_update = $reaction;
				$this->setDirty(true);
			}
			return $this;
		}

		/**
		 * @return int
		 */
		public function getReactionUpdate(){
			return $this->reaction_update;
		}

		/**
		 * @param $reaction
		 * @return $this
		 */
		public function setReactionDelete($reaction = self::R_RESTRICT){
			if($this->reaction_delete !== $reaction){
				$this->reaction_delete = $reaction;
				$this->setDirty(true);
			}
			return $this;
		}

		/**
		 * @return int
		 */
		public function getReactionDelete(){
			return $this->reaction_delete;
		}

		/**
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			if($this->name!==$name){
				$this->name = $name;
				$this->setDirty(true);
			}
			return $this;
		}

		/**
		 * @return $this
		 */
		public function save(){
			if(!$this->origin_table || !$this->reference_table){
				return false;
			}
			if($this->isNew()){
				if($this->_adapter->addForeignKey($this)){
					$this->setNew(false);
					$this->setDirty(false);
				}else{

				}
			}
			if($this->isDirty()){
				if($this->_adapter->removeForeignKey($this)){
					if($this->_adapter->addForeignKey($this)){
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
		 * @return $this
		 */
		public function remove(){
			if(!$this->isNew() && $this->origin_table){
				$this->origin_table->removeForeignKey($this,true);
				if($this->_adapter->removeForeignKey($this)){
					$this->setNew(true);
				}else{

				}
			}
		}

		/**
		 *
		 */
		public function __clone(){
			$this->origin_table = null;
			$this->_new = true;
		}

		/**
		 * @param Table $table
		 * @param Column[] $columns
		 * @return $this
		 */
		public function setOriginTableOnClone(Table $table, array $columns){
			foreach($columns as $i => $col){
				if($col->getName() === $this->origin_columns[$i]->getName()){
					$this->origin_columns[$i] = $col;
				}
			}
			$this->origin_table = $table;
			return $this;
		}

	}
}

