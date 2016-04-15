<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 02.03.2016
 * Time: 21:47
 */
namespace Jungle\Storage\Db\Dialect {

	use Jungle\Storage\Db\Dialect;
	use Jungle\Storage\Db\Structure\Column;
	use Jungle\Storage\Db\Structure\Column\ForeignKey;
	use Jungle\Storage\Db\Structure\Column\Index;

	/**
	 * Class MySQL
	 * @package Jungle\Storage\Db\Dialect
	 *
	 *
	 *
	 */
	class MySQL extends Dialect{

		/**
		 * @param $databaseName
		 * @return string
		 */
		public function createDatabase($databaseName){
			return 'CREATE DATABASE `'.$databaseName.'`;';
		}

		/**
		 * @param $databaseName
		 * @param $newName
		 * @return string
		 */
		public function renameDatabase($databaseName, $newName){
			return 'ALTER DATABASE '.$this->escape($databaseName). 'RENAME TO '.$this->escape($newName);
		}

		/**
		 * @param $databaseName
		 * @return string
		 */
		public function removeDatabase($databaseName){
			return 'REMOVE DATABASE `'.$databaseName.'`;';
		}

		/**
		 * @param array|string $name
		 * @param array $columns
		 * @param array $indexes
		 * @param array $foreignKeys
		 * @param null $engine
		 * @param null $comment
		 * @param bool $ifNotExists
		 * @return string
		 */
		public function createTable(
			$name,
			array $columns = [],
			array $indexes = [],
			array $foreignKeys = [],
			$ifNotExists = false,
			$engine = null,
			$comment = null
		){
			if(!$engine){
				$engine = 'InnoDB';
			}
			$str = "CREATE TABLE".($ifNotExists?' IF NOT EXISTS':'')." {$this->escapeTableName($this->exportDelimited($name))}(";
			$c = [];
			foreach($columns as $column){
				$c[] = "\r\n\t" . $this->prepareColumnDefinition(
						$column['name'],
						$column['type'],
						$column['size'],
						isset($column['unsigned'])?$column['unsigned']:false,
						isset($column['notnull'])?$column['notnull']:false,
						isset($column['default'])?$column['default']:null,
						isset($column['zerofill'])?$column['zerofill']:false
					);
				if(isset($column['primary']) && $column['primary']){
					$indexes[] = [
						'type' => Index::T_PRIMARY,
						'columns' => [$column['name']],
						'name' => "primary_{$column['name']}"
					];
				}
				if(isset($column['unique']) && $column['unique']){
					$indexes[] = [
						'type' => Index::T_UNIQUE,
						'columns' => [$column['name']],
						'name' => "unique_{$column['name']}"
					];
				}
			}
			$addedIndexesNames = [];
			foreach($indexes as $index){
				if(
					isset($index['name']) && $index['name'] &&
				   in_array($index['name'],$addedIndexesNames,true)
				){
					continue;
				}
				if(!isset($index['type']) || !$index['type']){
					$index['type'] = Index::T_KEY;
				}
				$s = "\r\n\t".$this->convertIndexTypeConstant($index['type']);
				if($index['type']!==Index::T_PRIMARY){
					if(!isset($index['name']) || !$index['name']){
						throw new \LogicException('Error index Name is not passed');
					}
					$s.=" {$this->escape($index['name'])}";
				}
				if(isset($index['algo']) && $index['algo']){
					$using = ' USING '.$this->convertIndexAlgoConstant($index['algo']);
				}else{
					$using = '';
				}
				if(!isset($index['columns']) || !$index['columns']){
					throw new \LogicException('Error index columns is not passed');
				}
				$c[] = $s . "(".$this->escapeIndexColumns($index['columns']).')'.$using;
				$addedIndexesNames[] = $index['name'];
			}
			foreach($foreignKeys as $key){
				$onDelete = ' ON DELETE ' . $this->convertForeignKeyConstant(isset($key['on_update']) && $key['on_update']?$key['on_update']:ForeignKey::R_RESTRICT);
				$onUpdate = ' ON UPDATE ' . $this->convertForeignKeyConstant(isset($key['on_update']) && $key['on_update']?$key['on_update']:ForeignKey::R_RESTRICT);
				$c[] = 'CONSTRAINT `'.$key['name'].'` FOREIGN KEY('.
				     $this->escapeColumns($key['columns']) . ') REFERENCES ' .
				     $this->escapeTableName($this->exportDelimited($key['reference_table'])).
				     '('.$this->escapeColumns($key['reference_columns']).')'.
				     $onUpdate . $onDelete;
			}
			$str.= implode(",",$c)."\r\n)";

			$str.= ' ENGINE='.$this->escapeString($engine);
			if($comment){
				$str.= "\r\n\tCOMMENT=".$this->escapeString($engine);
			}
			$str.=';';
			return $str;
		}

		/**
		 * @param $table
		 * @param $newName
		 * @return string
		 */
		public function renameTable($table, $newName){
			return 'ALTER TABLE '.$this->escapeTableName($this->exportDelimited($table)) . ' RENAME TO '.$this->escape($newName).';';
		}

		/**
		 * @param $name
		 * @param $type
		 * @param $size
		 * @param $unsigned
		 * @param $notnull
		 * @param null $default
		 * @param bool|false $zerofill
		 * @return string
		 */
		protected function prepareColumnDefinition(
			$name,$type,$size,$unsigned = false,$notnull = false,$default = null,$zerofill=false
		){

			if($size){
				if(!is_array($size)){
					$size = (string)$size;
				}
				if(is_string($size)){
					$size = preg_split('@[,\.]+@',$size);
				}
				$size = array_map('intval',$size);
				$size = '('.implode(',',$size).')';
			}

			$unsigned = $unsigned?' UNSIGNED':'';
			$notnull = $notnull?' NOT NULL':'';

			if($default !== null){
				$default = is_string($default)?$this->escapeString($default):$default;
			}

			if(!$notnull && $default===null){
				$default = 'NULL';
			}
			$default    = $default?   ' DEFAULT ' . $default : '';
			$zerofill   = $zerofill?  ' ZEROFILL':'';

			return $this->escape($name)." {$type}{$size}{$unsigned}{$notnull}{$default}{$zerofill}";
		}

		/**
		 * @param $table
		 * @return string
		 */
		public function removeTable($table){
			return 'REMOVE TABLE '.$this->escapeTableName($this->exportDelimited($table)).';';
		}

		/**
		 * @param $table
		 * @param $columnDefinition
		 * @return string
		 */
		public function addColumn($table, array $columnDefinition){
			$table = $this->exportDelimited($table);
			return 'ALTER TABLE '.$this->escapeTableName($table).' ADD COLUMN ' . $this->prepareColumnDefinition(
				$columnDefinition['name'],
				$columnDefinition['type'],
				$columnDefinition['size'],
				isset($columnDefinition['unsigned'])?$columnDefinition['unsigned']:false,
				isset($columnDefinition['notnull'])?$columnDefinition['notnull']:true,
				isset($columnDefinition['default'])?$columnDefinition['default']:null,
				isset($columnDefinition['zerofill'])?$columnDefinition['zerofill']:false
			).';';
		}

		/**
		 * @param $table
		 * @param $columnName
		 * @param $columnDefinition
		 * @return string
		 */
		public function modifyColumn($table, $columnName, array $columnDefinition){
			$columnDefinition['name'] = $columnName;
			return 'ALTER TABLE '.$this->escapeTableName($this->exportDelimited($table)).' MODIFY '. $this->prepareColumnDefinition(
				$columnDefinition['name'],
				$columnDefinition['type'],
				$columnDefinition['size'],
				isset($columnDefinition['unsigned'])?$columnDefinition['unsigned']:false,
				isset($columnDefinition['notnull'])?$columnDefinition['notnull']:true,
				isset($columnDefinition['default'])?$columnDefinition['default']:null,
				isset($columnDefinition['zerofill'])?$columnDefinition['zerofill']:false
			).';';
		}

		/**
		 * @param $table
		 * @param $columnName
		 * @param $newName
		 * @return string
		 */
		public function renameColumn($table, $columnName, $newName){
			return 'ALTER TABLE '.$this->escapeTableName($this->exportDelimited($table)).' CHANGE COLUMN ' .
			       $this->escape($columnName) .
			       ' TO '.
			       $this->escape($newName).
			';';
		}

		/**
		 * @param $table
		 * @param $columnName
		 * @return string
		 */
		public function removeColumn($table, $columnName){
			return 'ALTER TABLE '.$this->escapeTableName($this->exportDelimited($table)).' REMOVE COLUMN ' .
			$this->escape($columnName).';';
		}

		/**
		 * @param string|string[] $table
		 * @param $indexName
		 * @param array $columns
		 * @param $type
		 * @param $algo
		 * @return string
		 */
		public function addIndex($table, $indexName, array $columns, $type, $algo = null){
			return 'CREATE '.($type===Index::T_KEY?'':$type).' INDEX '.$this->escape($indexName).' '.$this->convertIndexAlgoConstant($algo).' ON '.$this->escapeTableName($this->exportDelimited($table)).'('.$this->escapeIndexColumns($columns).');';
		}


		/**
		 * @param string|string[] $table
		 * @param $indexName
		 * @return string
		 */
		public function removeIndex($table, $indexName){
			return 'DROP INDEX '.$this->escape($indexName) . ' FROM ' . $this->escapeTableName($this->exportDelimited($table)).';';
		}

		/**
		 * @param string|array $table
		 * @param $fkName
		 * @param array $originColumns
		 * @param array $referenceColumns
		 * @param $referenceTable
		 * @param string $onDelete
		 * @param string $onUpdate
		 * @return string
		 */
		public function addForeignKey(
			$table, $fkName,
			array $originColumns, array $referenceColumns,
			$referenceTable,
			$onDelete = ForeignKey::R_RESTRICT,
			$onUpdate = ForeignKey::R_RESTRICT
		){
			return 'ALTER TABLE '.$this->escapeTableName($this->exportDelimited($table)).' ADD FOREIGN KEY '.$this->escape($fkName).' ('.$this->escapeColumns($originColumns).') REFERENCES '.$this->escapeTableName($this->exportDelimited($referenceTable)).'('.$this->escapeColumns($referenceColumns).') ON DELETE '.$this->convertForeignKeyConstant($onDelete).' ON UPDATE '.$this->convertForeignKeyConstant($onUpdate).';';
		}

		/**
		 * @param string|array $table
		 * @param $fkName
		 * @return string
		 */
		public function removeForeignKey($table, $fkName){
			return 'ALTER TABLE '.$this->escapeTableName($this->exportDelimited($table)).' DROP FOREIGN KEY '.$this->escape($fkName) . ';';
		}

	}
}

