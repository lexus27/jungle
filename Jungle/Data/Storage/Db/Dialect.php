<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 17.02.2016
 * Time: 4:11
 */
namespace Jungle\Data\Storage\Db {

	use Jungle\Data\Storage\Db;
	use Jungle\Data\Storage\Db\Structure\Column;
	use Jungle\Data\Storage\Db\Structure\Column\ForeignKey;
	use Jungle\Data\Storage\Db\Structure\Column\Index;
	use Jungle\Util\Data\Condition\Condition;

	/**
	 * Class Dialect
	 * @package Jungle\Data\Storage\Db\Structure
	 */
	abstract class Dialect{

		protected static $_sqlServantHelper;

		const JOIN_INNER    = 'INNER';

		const JOIN_LEFT     = "LEFT";

		const JOIN_CROSS    = 'CROSS';

		const JOIN_RIGHT    = 'RIGHT';

		/** @var string  */
		protected $escape_char = '`';

		/** @var string  */
		protected $table_schema_delimiter = '.';


		/**
		 * @return Sql
		 */
		protected static function getSingletoneSqlServant(){
			if(!self::$_sqlServantHelper){
				self::$_sqlServantHelper = new Sql();
			}
			return self::$_sqlServantHelper;
		}


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
				return ($subjectEscaped?$subject:$this->escape($subject)) . ($alias?' AS '. $this->escape($alias):'');
		}

		/**
		 * @param string|array $identifier
		 * @param null $escapeChar
		 * @return string
		 */
		public function escape($identifier,$escapeChar = null){
			if($identifier instanceof RawValue){
				return $identifier->getValue();
			}
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
			return '\''.addcslashes($value,'\\\'').'\'';
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
				foreach($columnNames as $alias => $column){
					if(is_string($alias)){
						$a[] = $this->escapeAlias($column,$alias,false);
					}else{
						$a[] = $this->escape($column);
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
					}else{
						return $this->escape($v);
					}
				},array_keys($columns),array_values($columns)));
				return $c;
			}else{
				throw new \InvalidArgumentException('Wrong argument type');
			}
		}

		/**
		 * @param null $database
		 * @param null $like
		 * @return string
		 */
		public function listTables($database = null, $like = null){
			return 'SHOW TABLES'. ($database?' FROM '.$this->escape($database):'').($like?' LIKE '.$this->escapeString($like):'').';';
		}

		/**
		 * @param null $database
		 * @param null $like
		 * @return string
		 */
		public function listViews($database = null, $like = null){
			return 'SHOW VIEWS'. ($database?' FROM '.$this->escape($database):'').($like?' LIKE '.$this->escapeString($like):'').';';
		}

		/**
		 * @param null $like
		 * @return string
		 */
		public function listDatabases($like = null){
			return 'SHOW TABLES'.($like?' LIKE '.$this->escapeString($like):'').';';
		}

		public function describeColumns($table){
			return 'SHOW COLUMNS FROM '.$this->escapeTableName($table).';';
		}

		public function describeIndexes($table){
			return 'SHOW INDEXES FROM '.$this->escapeTableName($table).';';
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
		 * @param $table
		 * @param array $fields
		 * @param array $values
		 * @param array $dataTypes
		 * @param $whereCondition
		 * @param array $options
		 * @return Sql
		 */
		public function update($table, array $fields, array $values,$dataTypes = null, $whereCondition = null, array $options = null){
			$servant = $this->getSingletoneSqlServant();
			$servant->begin('UPDATE '.$this->escapeTableName($table));
			if(isset($options['joins']) && is_array($options['joins'])){
				foreach($options['joins'] as $join){
					$this->addJoin($servant,$join);
				}
			}
			$servant->push('SET',' ');
			foreach($values as $i => $value){
				$type = !$dataTypes || !isset($dataTypes[$i])?null:$dataTypes[$i];
				$servant->push($this->escape($fields[$i]) . ' = ',$i===0?' ':",\r\n\t\t");
				$servant->placeholder($value, $type, $fields[$i], true);
			}
			$this->addWhere($servant,$whereCondition);
			return $servant->complete();
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
		 * @param $table - tableDefinition
		 * @param array|null $fields - columnList
		 * @param array $values - data indexed
		 * @param null $dataTypes - values dataTypes
		 * @param bool $many
		 * @return Sql
		 */
		public function insert($table,array $fields, array $values, $dataTypes = null,$many = false){
			$servant = $this->getSingletoneSqlServant();
			$servant->begin('INSERT INTO '.$this->escapeTableName($table) . ($fields?'('.$this->escapeColumns($fields).')':'') . ' VALUES');
			if($values!==null){
				if($many){
					foreach($values as $i=>$valueList){
						$servant->push('(',($i>0?',':'')."\r\n\t");
						$servant->placeholderList(',',$valueList,$dataTypes,$fields,true);
						$servant->push(')','');
					}
				}else{
					$servant->push('('.implode(', ',array_fill(0, count($fields),'?')).')', "\r\n\t", $values, $dataTypes);
				}
			}
			return $servant->complete();
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
		 * @param $table
		 * @param $whereCondition
		 * @param array $options
		 * @return Sql
		 */
		public function delete($table, $whereCondition = null, array $options = null){
			$servant = $this->getSingletoneSqlServant();
			$servant->begin('DELETE '.$this->escapeTableName($table).' FROM '. $this->escapeTableName($table));
			if(isset($options['joins']) && is_array($options['joins'])){
				foreach($options['joins'] as $join){
					$this->addJoin($servant,$join);
				}
			}
			if($whereCondition){
				$this->addWhere($servant,$whereCondition);
			}
			return $servant->complete();
		}

		/**
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
		 * @return Sql
		 */
		public function select(array $definition){
			$d = array_replace([
				'table'             => null,
				'columns'           => '*',
				'columns_escape'    => true,
				'alias'             => null,
				'joins'             => null,
				'where'             => null,
				'having'            => null,
				'group_by'          => null,
				'order_by'          => null,
				'limit'             => null,
				'offset'            => null,
				'for_update'        => false,
				'lock_in_shared'    => false
			],$definition);

			if(!$d['table']){
				throw new \InvalidArgumentException('Definition must have table');
			}
			if(!$d['columns']){
				throw new \LogicException('Columns is not supplied');
			}
			$servant = $this->getSingletoneSqlServant();
			$servant->begin('SELECT ' . ($d['columns_escape']?$this->escapeColumns($d['columns']):$d['columns']) . ' FROM '.$this->escapeTableName($d['table'],$d['alias']));
			if(is_array($d['joins'])){
				foreach($d['joins'] as $join){
					$this->addJoin($servant,$join);
				}
			}
			if($d['where']) $this->addWhere($servant,$d['where']);
			if($d['having']) $this->addHaving($servant,$d['having']);
			if($d['group_by'])$this->addGroupBy($servant,$d['group_by']);
			if($d['order_by'])$this->addOrderBy($servant,$d['order_by']);
			if(isset($d['limit']) && $d['limit']){
				if(isset($d['offset'])){
					$d['limit'] = [
						intval((is_array($d['limit'])?$d['limit'][0]:$d['limit'])),
						intval($d['offset'])
					];
				}
				$this->addLimit($servant,$d['limit']);
			}
			if($d['for_update']) $this->addForUpdate($servant);
			if($d['lock_in_shared'])$this->addLockInSharedMode($servant);
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
			if(!isset($definition['type'])){
				$definition['type'] = null;
			}
			$table = $this->escapeTableName($definition['table'],isset($definition['alias'])?$definition['alias']:null);
			$servant->push($this->convertJoinType($definition['type']) . ' JOIN '. $table);
			if(isset($definition['on'])){
				$servant->append(' ON ');
				$this->prepareCondition($servant, $definition['on']);
			}
		}

		/**
		 * @param Sql $servant
		 * @param $condition - Condition definition
		 */
		public function addWhere(Sql $servant,$condition){
			if($condition){
				$servant->push('WHERE',' ');
				$this->prepareCondition($servant, $condition);
			}
		}

		/**
		 * @param Sql $servant
		 * @param $condition - Condition definition
		 */
		public function addHaving(Sql $servant, $condition){
			$servant->push('HAVING');
			$this->prepareCondition($servant, $condition);
		}


		/**
		 * @param Sql $servant
		 * @param null $condition
		 * ]
		 */
		public function prepareCondition(Sql $servant, $condition = null){
			if($condition){
				$delimited = true;
				foreach($condition as $key => $c){
					$s = is_string($key);
					$specCondition = $c instanceof ConditionTarget;
					$count = count($c);
					$block = false;
					if(!$s || !$specCondition){
						$block = true;
						foreach($c as $i){
							if(!is_array($i)){
								$block = false;
							}
						}
					}

					if(!$delimited){
						$delimited = true;
						if(!$block && $count === 1){
							$servant->push($c[0],' ');
							continue;
						}else{
							$servant->push('AND',' ');
						}
					}

					if($specCondition){
						/** @var ConditionTarget $c */
						$c->mountIn($this,$servant);
						$delimited = false;
					}elseif($block){
						$servant->append(' (');
						$this->prepareCondition($servant, $c);
						$servant->append(')');
						$delimited = false;
					}elseif($s){
						$operator = null;
						if(strpos($key,':')!==false){
							list($key,$operator) = array_replace([null,$operator],explode(':',$key,2));
						}
						if(!$operator){
							$operator = '=';
						}
						$left = $key;
						$right = $c;
						$this->prepareConditionTarget($servant, $left,$operator,$right);
						$delimited = false;
					}elseif($count === 3 || $count === 2){

						list($left, $operator, $right) = Condition::toList($c,[0,'left'],[1,'operator'],[2,'right']);


						//$left = isset($c[0])?$c[0]:$c['left'];
						//$operator = isset($c[1])?$c[1]:$c['operator'];
						//$right = isset($c[2])?$c[2]:$c['right'];
						$this->prepareConditionTarget($servant, $left,$operator,$right);
						$delimited = false;
					}
				}
			}
		}

		/**
		 * @param $servant
		 * @param $identifier
		 * @param $operator
		 * @param null $wanted
		 */
		public function prepareConditionTarget(Sql $servant, $identifier, $operator, $wanted = null){
			$escaped = $this->escape($identifier);
			$binds = [];
			$types = [];
			if(isset($wanted)){
				if($wanted instanceof RawValue){
					$servant->push($escaped.' '.$operator.' '.$wanted,' ');
				}elseif(is_array($wanted)){
					if(isset($wanted['identifier'])){
						$servant->push($escaped.' '.$operator.' '.$this->escape($wanted['identifier']),' ');
					}else{
						$valueIds = [];
						foreach($wanted as $i => $v){
							$valueIds[] = $vID = $servant->placeholderName($identifier, true);
							$binds[$vID] = $v;
							$types[$vID] = Adapter::getBestDbColumnType($v);
						}
						$servant->push(
							$escaped.' '.$operator.' ('.(implode(', ', $valueIds)).')',' ',
							$binds, $types
						);
					}
				}else{
					$vID = $servant->placeholderName($identifier, true);
					$binds[$vID] = $wanted;
					$types[$vID] = Adapter::getBestDbColumnType($wanted);
					$servant->push($escaped.' '.$operator.' '.$vID,' ',
						$binds, $types
					);
				}
			}else{
				$servant->push($escaped.' '.$operator,' ');
			}
		}

		/**
		 * @param int $limit
		 * @return bool
		 */
		public static function isInfinityLimit($limit = -1){
			return $limit === -1 || $limit === null || $limit === 0;
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
			if(!self::isInfinityLimit($limit)){
				$servant->push("LIMIT  " .($offset?intval($offset).', ':'') . intval($limit),"\r\n\t");
			}
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
		 * @param $table
		 * @param $value
		 * @return string
		 */
		public function setAutoIncrement($table, $value){
			return 'ALTER TABLE '.$this->escapeTableName($table) . ' AUTO_INCREMENT = '.intval($value).';';
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
			switch(strtoupper($direction)){
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
			if(is_string($type))$type = strtoupper($type);
			if(in_array($type,[self::JOIN_CROSS,'CROSS',true])){
				return 'CROSS';
			}
			if(in_array($type,[self::JOIN_INNER,'INNER',true])){
				return 'INNER';
			}
			if(in_array($type,[self::JOIN_LEFT,'LEFT',true])){
				return 'LEFT';
			}
			if(in_array($type,[self::JOIN_RIGHT,'RIGHT',true])){
				return 'RIGHT';
			}
			return 'INNER';
		}


	}
}

