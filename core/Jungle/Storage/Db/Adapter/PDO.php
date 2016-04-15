<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 04.03.2016
 * Time: 12:53
 */
namespace Jungle\Storage\Db\Adapter {

	use Jungle\Storage\Db;
	use Jungle\Storage\Db\Adapter;

	/**
	 * Class Pdo
	 * @package Jungle\Storage\Db\Adapter
	 */
	class Pdo extends Adapter {

		/**
		 * @var \PDO
		 */
		protected $connection;

		/**
		 * @var \PDOStatement
		 */
		protected $statement;

		/**
		 * @param array $options
		 */
		public function __construct(array $options = []){
			$options['type'] = $this->driverType;
			if(!isset($options['type'])){
				throw new \LogicException('PDO Type not passed');
			}

			$dsn = [];
			if(!isset($options['host']) || !$options['host']){
				$options['host'] = 'localhost';
			}
			$dsn[] = 'host='.$options['host'];

			if(!isset($options['port'])){
				throw new \LogicException('Port not passed');
			}
			$dsn[] = 'port='.$options['port'];

			if(!isset($options['dbname']) || !$options['dbname']){
				throw new \LogicException('Database name not passed');
			}
			$dsn[] = 'dbname='.$options['dbname'];

			if(!isset($options['username']) || !$options['username']){
				throw new \LogicException('Username not passed');
			}
			$username = $options['username'];

			if(isset($options['password']) && $options['password']){
				$password = $options['password'];
			}else{
				$password = '';
			}

			$this->descriptor = $options;

			$className= '\Jungle\Storage\Db\Dialect\\'. $this->dialectType;
			$this->dialect = new $className();

			$this->connection = new \PDO(
				$options['type'].':'.implode(';',$dsn),
				$username,$password
			);
		}

		/**
		 * @param $sql
		 * @param int $className
		 * @param null $bindParams
		 * @param null $bindTypes
		 */
		public function fetchObjects($sql, $className, $bindParams = null, $bindTypes = null){

		}

		/**
		 * @param $sql
		 * @param int|null $fetchMode
		 * @param null $bindParams
		 * @param null $bindTypes
		 * @return mixed
		 */
		public function fetchAll($sql, $fetchMode = Db::FETCH_ASSOC, $bindParams = null, $bindTypes = null){
			$stmt = $this->query($sql,$bindParams,$bindTypes);
			$stmt->setFetchMode($fetchMode);
			if($stmt){
				return $stmt->fetchAll($fetchMode);
			}else{
				return false;
			}
		}

		/**
		 * @param $sql
		 * @param int|null $fetchMode
		 * @param null $bindParams
		 * @param null $bindTypes
		 * @return mixed
		 */
		public function fetchOne($sql, $fetchMode = Db::FETCH_ASSOC, $bindParams = null, $bindTypes = null){
			$stmt = $this->query($sql,$bindParams,$bindTypes);
			$stmt->setFetchMode($fetchMode);
			if($stmt){
				return $stmt->fetch();
			}else{
				return false;
			}
		}


		/**
		 * @param $sql
		 * @param null $bindings
		 * @param null $types
		 * @param int $column
		 * @return mixed
		 */
		public function fetchColumn($sql, $bindings = null, $types = null, $column = 0){
			$stmt = $this->query($sql,$bindings);
			if($stmt){
				return $stmt->fetchColumn($column);
			}else{
				return false;
			}
		}

		/**
		 * @param $sql
		 * @param null|array $binds
		 * @param null|int|array $types
		 * @return \PDOStatement|bool
		 */
		public function query($sql, $binds = null, $types = Db\Structure\Column::BIND_PARAM_STR){
			$this->statement = $this->connection->query($sql);
			if($this->statement){
				if(is_array($binds)){
					foreach($binds as $key => $value){
						$type = null;
						if(is_array($types)){
							$type = $types[$key];
						}else{
							$type = $types;
						}
						if(!$type){
							$type = Db\Structure\Column::BIND_PARAM_STR;
						}
						if(is_numeric($key)) $key = $key+1;
						$this->statement->bindValue($key,$value,$type);
					}
				}
				if($this->statement->execute()){
					return $this->statement;
				}else{
					return false;
				}
			}
			return false;
		}


		/**
		 * @param $sql
		 * @param null $binds
		 * @param array|int|null $types
		 * @return bool
		 */
		public function execute($sql, $binds = null, $types = Db\Structure\Column::BIND_PARAM_STR){
			$this->statement = $this->connection->prepare($sql);
			if($this->statement){
				if(is_array($binds)){
					foreach($binds as $key => $value){
						$type = null;
						if(is_array($types)){
							$type = $types[$key];
						}else{
							$type = $types;
						}
						if(!$type){
							$type = Db\Structure\Column::BIND_PARAM_STR;
						}
						if(is_numeric($key)) $key = $key+1;
						$this->statement->bindValue($key,$value,$type);
					}
				}
				if($this->statement->execute()){
					return true;
				}else{
					return false;
				}
			}
			return false;
		}

		/**
		 * @return array
		 */
		public function getLastErrorInfo(){
			return $this->connection->errorInfo();
		}

		/**
		 * @return string
		 */
		public function getLastErrorCode(){
			return $this->connection->errorCode();
		}

		/**
		 * @param null $sequenceName
		 * @return string
		 */
		public function getLastInsertId($sequenceName = null){
			return $this->connection->lastInsertId($sequenceName);
		}

		/**
		 * @return object
		 */
		public function getInternalAdapter(){
			return $this->connection;
		}
	}
}

