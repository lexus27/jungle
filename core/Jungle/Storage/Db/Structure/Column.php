<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 17.02.2016
 * Time: 4:12
 */
namespace Jungle\Storage\Db\Structure {

	use Jungle\Basic\INamed;
	use Jungle\Storage\Db\Adapter;
	use Jungle\Storage\Db\Structure\Column\ForeignKey;
	use Jungle\Storage\Db\Structure\Column\Index;
	use Jungle\Storage\Db\Structure\Column\Type;

	/**
	 * Class Column
	 * @package Jungle\Storage\Db\Structure
	 */
	class Column extends StructureObject implements INamed{

		/**
		 * Bind Type Null
		 */
		const BIND_PARAM_NULL = 0;

		/**
		 * Bind Type Integer
		 */
		const BIND_PARAM_INT = 1;

		/**
		 * Bind Type String
		 */
		const BIND_PARAM_STR = 2;

		/**
		 * Bind Type Blob
		 */
		const BIND_PARAM_BLOB = 3;

		/**
		 * Bind Type Bool
		 */
		const BIND_PARAM_BOOL = 5;

		/**
		 * Bind Type Decimal
		 */
		const BIND_PARAM_DECIMAL = 32;

		/**
		 * Skip binding by type
		 */
		const BIND_SKIP = 1024;


		/** @var Table owner */
		protected $table;

		/** @var string */
		protected $name;

		/** @var Type */
		protected $type;

		/** @var int */
		protected $size             = null;

		/** @var  Column */
		protected $after;

		/** @var mixed */
		protected $default          = null;

		/** @var bool */
		protected $notnull          = false;

		/** @var bool */
		protected $unsigned         = false;

		/** @var bool  */
		protected $zerofill         = false;

		/** @var bool */
		protected $auto_increment   = false;


		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			if($this->name!==$name){
				if($this->_adapter && !$this->isNew()){
					if($this->_adapter->renameColumn($this,$name)){
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
		 * @return Type
		 */
		public function getType(){
			return $this->type;
		}

		/**
		 * @param Type|string $type
		 * @return $this
		 */
		public function setType($type){
			$type = $this->_adapter->getColumnType($type);
			if($this->type !== $type){
				$this->type = $type;
				$this->setDirty(true);
			}
			return $this;
		}

		/**
		 * @return Column|null
		 */
		public function getAfter(){
			return $this->after;
		}

		/**
		 * @param Column $column
		 * @return $this
		 */
		public function setAfter(Column $column = null){
			if($this->after !== $column){
				$this->after = $column;
				$this->setDirty(true);
			}
			return $this;
		}


		/**
		 * @param $size
		 * @return $this
		 */
		public function setSize($size){
			if($this->size !== $size){
				$this->size = $size;
				$this->setDirty(true);
			}
			return $this;
		}

		/**
		 * @return int
		 */
		public function getSize(){
			return $this->size;
		}



		/**
		 * @param bool|false $auto_increment
		 * @return $this
		 */
		public function setAutoIncrement($auto_increment = false){
			$auto_increment = boolval($auto_increment);
			if($this->auto_increment !== $auto_increment){
				$this->auto_increment = $auto_increment;
				$this->setDirty(true);
			}
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isAutoIncrement(){
			return $this->auto_increment;
		}

		/**
		 * @return mixed
		 */
		public function getDefault(){
			return $this->default;
		}

		/**
		 * @param null $default
		 * @return $this
		 */
		public function setDefault($default = null){
			if($this->default !== $default){
				$this->default = $default;
				$this->setDirty(true);
			}
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isUnsigned(){
			return $this->unsigned;
		}

		/**
		 * @param bool|false $unsigned
		 * @return $this
		 */
		public function setUnsigned($unsigned = false){
			$unsigned = boolval($unsigned);
			if($this->unsigned !== $unsigned){
				$this->unsigned = $unsigned;
				$this->setDirty(true);
			}
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isNotNull(){
			return $this->notnull;
		}

		/**
		 * @param bool|false $notnull
		 * @return $this
		 */
		public function setNotNull($notnull = false){
			$notnull = boolval($notnull);
			if($this->notnull !== $notnull){
				$this->notnull = $notnull;
				$this->setDirty(true);
			}
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isZerofill(){
			return $this->zerofill;
		}

		/**
		 * @param bool|false $zerofill
		 * @return $this
		 */
		public function setZerofill($zerofill = false){
			$zerofill = boolval($zerofill);
			if($this->zerofill !== $zerofill){
				$this->zerofill = $zerofill;
				$this->setDirty(true);
			}
			return $this;
		}


		/**
		 * @param Column $column
		 * Сопоставление этой колонки с другой колонкой по типу данных хранимых в ней
		 * @return bool
		 */
		public function isEqualType(Column $column){
			return  $this->type === $column->getType() &&
					$this->unsigned === $column->isUnsigned() &&
					$this->size === $column->getSize();
		}


		/**
		 * @return Table
		 */
		public function getTable(){
			return $this->table;
		}

		/**
		 * @param Table $table
		 * @param bool $appliedInNew
		 * @param bool $appliedInOld
		 * @return $this
		 */
		public function setTable(Table $table = null, $appliedInNew = false, $appliedInOld = false){
			$old = $this->table;
			if($old !== $table){
				$this->table = $table;
				if($old && !$appliedInOld){
					$table->removeColumn($this,true);
				}
				if($table && !$appliedInNew){
					$table->addColumn($this,true);
				}
				if($old){
					$this->remove();
				}
			}
			return $this;
		}

		/**
		 * @return int
		 */
		public function getColumnIndex(){
			return $this->table->searchColumn($this);
		}

		/**
		 * @return bool
		 */
		public function isPrimary(){
			foreach($this->getTable()->getIndexes() as $index){
				if($index->searchColumn($this)!==false && $index->getType() === $index::T_PRIMARY){
					return true;
				}
			}
			return false;
		}

		/**
		 * @return Index[]
		 */
		public function getIndexes(){
			$a = [];
			foreach($this->getTable()->getIndexes() as $index){
				if($index->searchColumn($this)!==false){
					$a[] = $index;
				}
			}
			return $a;
		}

		/**
		 * @return ForeignKey[]
		 */
		public function getForeignKeys(){
			if(!$this->table){
				return [];
			}
			$a = [];
			foreach($this->getTable()->getForeignKeys() as $fk){
				if($fk->indexOfOrigin($this)!==false){
					$a[] = $fk;
				}
			}
			return $a;
		}


		/**
		 * @return $this
		 */
		public function save(){
			if(!$this->table){
				return false;
			}
			if($this->isNew()){
				if($this->_adapter->addColumn($this)){
					$this->setNew(false);
				}else{

				}
			}elseif($this->isDirty()){
				if($this->_adapter->modifyColumn($this)){
					$this->setDirty(false);
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
				if($this->_adapter->removeColumn($this)){
					$this->setNew(true);
				}else{

				}
			}
		}


		public function __clone(){
			$this->table = null;
		}

		/**
		 * @return array
		 */
		public function toArray(){
			return [
				'name'              => $this->getName(),
				'type'              => $this->getType()->getName(),
				'size'              => $this->getSize(),
				'unsigned'          => $this->isUnsigned(),
				'notnull'           => $this->isNotNull(),
				'default'           => $this->getDefault(),
				'zerofill'          => $this->isZerofill(),
				'auto_increment'    => $this->isAutoIncrement()
			];
		}
	}

}

