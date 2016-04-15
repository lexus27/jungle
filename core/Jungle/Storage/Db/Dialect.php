<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 17.02.2016
 * Time: 4:11
 */
namespace Jungle\Storage\Db {

	use Jungle\Storage\Db;
	use Jungle\Storage\Db\Structure\Column;
	use Jungle\Storage\Db\Structure\Column\ForeignKey;
	use Jungle\Storage\Db\Structure\Column\Index;

	/**
	 * Class Dialect
	 * @package Jungle\Storage\Db\Structure
	 */
	abstract class Dialect{

		const JOIN_INNER    = 'INNER';

		const JOIN_LEFT     = "LEFT";

		const JOIN_CROSS    = 'CROSS';

		const JOIN_RIGHT    = 'RIGHT';

		/** @var string  */
		protected $escape_char = '`';

		/** @var string  */
		protected $table_schema_delimiter = '.';



		/**
		 * @param $databaseName
		 * @return string
		 */
		abstract public function createDatabase($databaseName);

		/**
		 * @param $databaseName
		 * @return string
		 */
		abstract public function removeDatabase($databaseName);

		/**
		 * @param $databaseName
		 * @param $newName
		 * @return string
		 */
		abstract public function renameDatabase($databaseName,$newName);

		/**
		 * @param string|array $name
		 * @param array $columns
		 * @param array $indexes
		 * @param array $foreignKeys
		 * @param null $engine
		 * @param null $comment
		 * @param bool $ifNotExists
		 * @return string
		 */
		abstract public function createTable($name,
			array $columns      = [],
			array $indexes      = [],
			array $foreignKeys  = [],
			$ifNotExists        = false,
			$engine             = null,
			$comment            = null
		);

		/**
		 * @param string|array $table
		 * @param $newName
		 * @return string
		 */
		abstract public function renameTable($table,$newName);

		/**
		 * @param string|array $table
		 * @return string
		 */
		abstract public function removeTable($table);

		/**
		 * @param string|array $table
		 * @param $columnDefinition
		 * @return string
		 */
		abstract public function addColumn($table,array $columnDefinition);

		/**
		 * @param string|array $table
		 * @param $columnName
		 * @param $columnDefinition
		 * @return string
		 */
		abstract public function modifyColumn($table, $columnName,array $columnDefinition);

		/**
		 * @param string|array $table
		 * @param $columnName
		 * @param $newName
		 * @return string
		 */
		abstract public function renameColumn($table, $columnName, $newName);

		/**
		 * @param string|array $table
		 * @param $columnName
		 * @return string
		 */
		abstract public function removeColumn($table, $columnName);

		/**
		 * @param string|array $table
		 * @param $indexName
		 * @param array $columnNames
		 * @param $type
		 * @param $algo
		 * @return string
		 */
		abstract public function addIndex($table, $indexName, array $columnNames, $type, $algo);

		/**
		 * @param string|array $table
		 * @param $indexName
		 * @return string
		 */
		abstract public function removeIndex($table, $indexName);

		/**
		 * @param string|array $table
		 * @param $fkName
		 * @param array $originColumns
		 * @param array $referenceColumns
		 * @param string|array $referenceTable
		 * @param string $onDelete
		 * @param string $onUpdate
		 * @return string
		 */
		abstract public function addForeignKey(
			$table, $fkName,
			array $originColumns, array $referenceColumns,
			$referenceTable,
			$onDelete = ForeignKey::R_RESTRICT,
			$onUpdate = ForeignKey::R_RESTRICT
		);

		/**
		 * @param string|array $table
		 * @param $fkName
		 * @return string
		 */
		abstract public function removeForeignKey($table, $fkName);

		/**
		 * @param array|string $table
		 * @param null $alias
		 * @return string
		 */
		public function escapeTableName($table, $alias = null){
			list($table,$database) = $this->exportDelimited($table);
			$s =($database?$this->escape($database).$this->table_schema_delimiter:'').$this->escape($table);
			return $alias?$this->escapeAlias($s,$alias,true):$s;
		}

		/**
		 * @param $subject
		 * @param $alias
		 * @param bool $subjectEscaped
		 * @return string
		 */
		public function escapeAlias($subject, $alias = null, $subjectEscaped = false){
			if(is_array($subject)){
				$c = [];
				foreach($subject as $s => $alias){
					$c[] = ($subjectEscaped?$s:$this->escape($s)) . ($alias?' AS '. $this->escape($alias):'');
				}
				return implode(', ',$c);
			}else{
				return ($subjectEscaped?$subject:$this->escape($subject)) . ($alias?' AS '. $this->escape($alias):'');
			}
		}

		/**
		 * @param string|array $identifier
		 * @param null $escapeChar
		 * @return string
		 */
		public function escape($identifier,$escapeChar = null){
			$escapeChar = $escapeChar?:$this->escape_char;
			if(is_string($identifier)){
				$identifier = explode('.',$identifier);
			}
			if(is_array($identifier)){
				foreach($identifier as & $id){
					$id = str_replace($escapeChar,'',trim($id,"$escapeChar\r\n\t "));
					$id = "{$escapeChar}{$id}{$escapeChar}";
				}
				return implode('.',$identifier);
			}else{
				throw new \InvalidArgumentException();
			}
		}

		/**
		 * @param string $identifier escaped
		 * @param null $escapeChar
		 * @return array|string
		 */
		public function unEscape($identifier,$escapeChar = null){
			$escapeChar = $escapeChar?:$this->escape_char;
			$identifier = explode('.',trim($identifier,"\r\n\t\0. "));
			foreach($identifier as & $id){
				$id = trim($id,"\r\n\t\0. $escapeChar");
			}
			return count($identifier)>1?$identifier:$identifier[0];
		}

		/**
		 * @param string $value
		 * @return string
		 */
		public function escapeString($value){
			return '\''.addcslashes($value,'\'').'\'';
		}

		/**
		 * @param $value
		 * @return string
		 */
		public function unEscapeString($value){
			return stripcslashes($value);
		}

		/**
		 * @param $columnNames - [
		 *
		 *      [Alias] => [ColumnName],
		 *      [Alias] => [ColumnName],
		 *      [ColumnName],
		 * ]
		 * @return string
		 *
		 *
		 */
		public function escapeColumns($columnNames){
			if(is_string($columnNames)){
				return $columnNames;
			}
			if(is_array($columnNames)){
				$a = [];
				foreach($columnNames as $columnName => $alias){
					if(is_string($columnName)){
						if($alias){
							$a[] = $this->escapeAlias($columnName,$alias,false);
						}else{
							$a[] = $this->escape($columnName);
						}
					}else{
						$a[] = $this->escape($alias);
					}
				}
				$a = implode(', ',$a);
				return $a;
			}else{
				throw new \InvalidArgumentException('Wrong argument type');
			}
		}

		/**
		 * @param array $columns -
		 *  [Name] => null | [
		 *      'size' => 0,
		 *      'direction' => 'ASC' | 'DESC'
		 *  ]
		 * @return string
		 */
		public function escapeIndexColumns($columns){
			if(is_string($columns)){
				return $columns;
			}
			if(is_array($columns)){
				$c = implode(',',array_map(function($k,$v){
					if(is_string($k)){
						return $this->escape($k).
						       (is_numeric($v)?
							       '('.intval($v).')':
							       (is_array($v)?
								       $this->escape($k).
								       (isset($v['size'])? '('.intval($v['size']).')': '') .
								       (isset($v['direction'])?
									       ' ' . $this->convertDirectionConstant($v['direction']) : ''
								       ):''
							       )
						       );
					}elseif(is_string($v)){
						return $this->escape($v);
					}
				},array_keys($columns),array_values($columns)));
				return $c;
			}else{
				throw new \InvalidArgumentException('Wrong argument type');
			}
		}

		/**
		 * @param string|array $table -
		 * ['tableName','databaseName'] | 'tableName.DatabaseName' | 'tableName'
		 * @return array
		 */
		public function exportDelimited($table){
			if(is_array($table)){
				return $table;
			}elseif(strstr($table,'.')){
				return explode('.',$table,1);
			}else{
				return [$table,null];
			}
		}


		/**
		 * @param Sql $servant
		 * @param $table
		 * @param array $values
		 * @param array $fields
		 * @param array $dataTypes
		 * @param $whereCondition
		 * @return string
		 */
		public function update(Sql $servant, $table, array $values, array $fields,$dataTypes = null, $whereCondition = null){
			$servant->push('UPDATE '.$this->escapeTableName($table));
			$set = [];
			foreach($values as $i => $value){
				$set[] = $this->escape($fields[$i]) . ' = ?';
			}
			$servant->push('SET' . implode(",\r\n\t\t",$set),"\r\n\t",$values,$dataTypes);
			$this->addWhere($servant,$whereCondition);
			return $servant;
		}

		/**
		 * @param Sql $servant
		 */
		public function addForUpdate(Sql $servant){
			$servant->push('FOR UPDATE',"\r\n\t");
		}

		/**
		 * @param Sql $servant
		 */
		public function addLockInSharedMode(Sql $servant){
			$servant->push('LOCK IN SHARE MODE ',"\r\n\t");
		}

		/**
		 * @param Sql $servant
		 * @param $table - tableDefinition
		 * @param $values - data indexed
		 * @param array|null $fields - columnList
		 * @param null $dataTypes - values dataTypes
		 * @return Sql
		 */
		public function insert(Sql $servant, $table,array $values = null,array $fields = null, $dataTypes = null){
			$servant->push('INSERT INTO '.$this->escapeTableName($table) . ($fields?'('.$this->escapeColumns($fields).')':'') . ' VALUES');
			if($values!==null){
				$servant->push('('.array_fill(0,count($fields),'?').')',"\r\n\t",$values,$dataTypes);
			}
			return $servant;
		}

		/**
		 * @param Sql $servant
		 * @param array|null $values
		 */
		public function addInlineInsertValues(Sql $servant, array $values = null){
			$servant->push('('.implode(',',array_map(function($v){
				return is_string($v)?$this->escapeString($v):$v;
			},$values)).')');
		}


		/**
		 * @param Sql $servant
		 * @param $table
		 * @param $whereCondition
		 * @return Sql
		 */
		public function delete(Sql $servant, $table, $whereCondition = null){
			$servant->begin('DELETE FROM '. $this->escapeTableName($table));
			if($whereCondition){
				$this->addWhere($servant,$whereCondition);
			}
			return $servant->complete();
		}

		/**
		 * @param Sql $servant Beginner - Completing
		 *
		 * @param array $definition -
		 *      array|string     [table]    - TableDefinition
		 *      array|string     [columns]  - column list
		 *      string           [alias]    - table alias
		 *      array[]          [joins]    - JoinDefinition Collections
		 *      string|array     [where]    - Condition definition
		 *      string|array     [having]   - Condition definition
		 *      int[]|int        [limit]    - Limit , Offset
		 *      int              [offset]   - Offset
		 *      string[]         [group_by] - Column list | string list
		 *      array[]|string[] [order_by] - Column Direction list | string list
		 *      bool             [for_update] - Adding for update
		 *
		 * @return array [SQL, Bindings, DataTypes]
		 */
		public function select(Sql $servant, array $definition){
			if(!isset($definition['table'])){
				throw new \InvalidArgumentException('Definition must have table');
			}
			$table = $definition['table'];
			if(!isset($definition['columns'])){
				throw new \InvalidArgumentException('Definition must have columns');
			}
			$columns = $definition['columns'];
			$alias = isset($definition['alias'])?$definition['alias']:null;
			$servant->begin('SELECT ' . $this->escapeColumns($columns) . ' FROM '.$this->escapeTableName($table,$alias));

			if(isset($definition['joins']) && is_array($definition['joins'])){
				foreach($definition['joins'] as $join){
					$this->addJoin($servant,$join);
				}
			}
			if(isset($definition['where'])){
				$this->addWhere($servant,$definition['where']);
			}
			if(isset($definition['having'])){
				$this->addHaving($servant,$definition['having']);
			}
			if(isset($definition['group_by'])){
				$this->addGroupBy($servant,$definition['group_by']);
			}
			if(isset($definition['order_by'])){
				$this->addOrderBy($servant,$definition['order_by']);
			}
			if(isset($definition['limit'])){
				if(isset($definition['offset'])){
					$definition['limit'] = [
						intval((is_array($definition['limit'])?$definition['limit'][0]:$definition['limit'])),
						intval($definition['offset'])
					];
				}
				$this->addLimit($servant,$definition['limit']);
			}
			if(isset($definition['for_update']) && $definition['for_update']){
				$this->addForUpdate($servant);
			}
			if(isset($definition['lock_in_shared']) && $definition['lock_in_shared']){
				$this->addLockInSharedMode($servant);
			}

			return $servant->complete();
		}



		/**
		 * @param Sql $servant
		 * @param array $definition -
		 *      string|array     [table]    - TableDefinition
		 *      string|null      [alias]    - Aliased
		 *      string|array     [on]       - ConditionDefinition
		 *      string|int       [type]     - JOIN_* Constants range
		 */
		public function addJoin(Sql $servant,array $definition){
			$table = $this->escapeTableName($definition['table'],isset($definition['alias'])?$definition['alias']:null);
			list($condition, $binds, $types) = $this->prepareCondition($definition['on']);
			$sql = $this->convertJoinType($definition['type']) . ' JOIN '. $table . ' ON ' . $condition;
			$servant->push($sql,"\r\n\t",$binds,$types);
		}

		/**
		 * @param Sql $servant
		 * @param $condition - Condition definition
		 */
		public function addWhere(Sql $servant,$condition){
			if($condition){
				list($condition,$binds,$types) = $this->prepareCondition($condition);
				$servant->push('WHERE ' . $condition,"\r\n\t",$binds,$types);
			}
		}

		/**
		 * @param Sql $servant
		 * @param $condition - Condition definition
		 */
		public function addHaving(Sql $servant, $condition){
			list($condition,$binds,$types) = $this->prepareCondition($condition);
			$servant->push('HAVING ' . $condition,"\r\n\t",$binds,$types);
		}



		/**
		 * @param null $condition
		 * @return array [
		 *      0 => (string) $condition,
		 *      1 => (array|null) $binds,
		 *      2 => (array|null)$types
		 * ]
		 */
		public function prepareCondition($condition = null){
			if($condition){
				if(is_array($condition)){
					$c      = isset($condition['condition'])?$condition['condition']:(isset($condition[0])?$condition[0]:'');
					$binds  = (array) isset($condition['binds'])?$condition['binds']:(isset($condition[1])?$condition[1]:'');
					$types  = (array) isset($condition['types'])?$condition['types']:(isset($condition[2])?$condition[2]:'');
				}elseif(is_string($condition)){
					$c      = $condition;
					$binds  = [];
					$types  = [];
				}else{
					throw new \LogicException('Condition is invalid');
				}
				return [$c,$binds,$types];
			}
			return null;
		}

		/**
		 * @param Sql $servant
		 * @param int[]|int $number - [{LIMIT}, {OFFSET}] | {LIMIT}
		 */
		public function addLimit(Sql $servant, $number){
			if(is_array($number)){
				list($limit,$offset) = $number;
			}else{
				$limit = $number;
				$offset = null;
			}
			$servant->push("LIMIT  " .($offset!==null?intval($offset).', ':'') . intval($limit),"\r\n\t");
		}

		/**
		 * @param Sql $servant
		 * @param string[]|string $columns
		 */
		public function addGroupBy(Sql $servant, $columns){
			if(is_string($columns)){
				$columns = explode(',',$columns);
			}
			if(is_array($columns)){
				$groupBy = [];
				foreach($columns as $name){
					$groupBy[] = $this->escape($name) ;
				}
				$servant->push("GROUP BY " . implode(', ',$groupBy),"\r\n\t");
			}else{
				throw new \InvalidArgumentException('addGroupBy: Wrong argument type $columns');
			}
		}

		/**
		 * @param Sql $servant
		 * @param $columns -
		 *      [COLUMN_NAME] => DIRECTION
		 */
		public function addOrderBy(Sql $servant,$columns){
			if(is_string($columns)){
				$c = explode(',',$columns);
				$columns = [];
				foreach($c as $column){
					list($column,$direction) = explode(' ',$column);
					$columns[$column] = $direction;
				}
				$this->addOrderBy($servant, $columns);
				return;
			}elseif(is_array($columns)){
				$orders = [];
				foreach($columns as $name => $direction){
					if(is_string($name)){
						$orders[] = $this->escape($name) . ' ' . $this->convertDirectionConstant($direction);
					}else{
						$orders[] = $this->escape($direction) . ' ASC';
					}
				}
				$servant->push('ORDER BY ' . implode(', ',$orders),"\r\n\t");
			}else{
				throw new \InvalidArgumentException('Wrong argument type $columns');
			}
		}

		/**
		 * @param $columns
		 * @return string
		 */
		public function distinct($columns){
			return 'DISTINCT ' . $this->escapeColumns($columns);
		}




		/**
		 * @param $algo
		 * @return string
		 */
		protected function convertIndexAlgoConstant($algo){
			switch($algo){
				case Index::A_BTREE:    return 'BTREE';
				case Index::A_HASH:     return 'HASH';
				case Index::A_RTREE:    return 'RTREE';
				default: return 'BTREE';
			}
		}

		/**
		 * @param $direction
		 * @return string
		 */
		protected function convertDirectionConstant($direction){
			switch($direction){
				case Index::DIRECTION_ASC:    return 'ASC';
				case Index::DIRECTION_DESC:   return 'DESC';
				default: return 'ASC';
			}
		}

		/**
		 * @param $type
		 * @return string
		 */
		protected function convertIndexTypeConstant($type){
			switch($type){
				case Index::T_PRIMARY:  return 'PRIMARY KEY';
				case Index::T_KEY:      return 'KEY';
				case Index::T_UNIQUE:   return 'UNIQUE KEY';
				case Index::T_FULLTEXT: return 'FULLTEXT KEY';
				case Index::T_SPATIAL:  return 'SPATIAL KEY';
				default: return 'KEY';
			}
		}

		/**
		 * @param $reaction
		 * @return string
		 */
		protected function convertForeignKeyConstant($reaction){
			switch($reaction){
				case ForeignKey::R_CASCADE:     return 'CASCADE';
				case ForeignKey::R_NOACTION:    return 'NOACTION';
				case ForeignKey::R_RESTRICT:    return 'RESTRICT';
				case ForeignKey::R_SETNULL:     return 'SET NULL';
				default:     return 'RESTRICT';
			}
		}

		/**
		 * @param $type
		 * @return string
		 */
		protected function convertJoinType($type){
			switch($type){
				case self::JOIN_CROSS: return 'CROSS';
				case self::JOIN_INNER: return 'INNER';
				case self::JOIN_LEFT: return 'LEFT';
				case self::JOIN_RIGHT: return 'RIGHT';
				default: return 'INNER';
			}
		}


	}
}

