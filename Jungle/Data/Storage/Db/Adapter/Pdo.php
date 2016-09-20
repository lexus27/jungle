<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 04.03.2016
 * Time: 12:53
 */
namespace Jungle\Data\Storage\Db\Adapter {

	use Jungle\Data\Storage\Db;
	use Jungle\Data\Storage\Db\Adapter;
	use Jungle\Data\Storage\Db\ResultInterface;

	/**
	 * Class Pdo
	 * @package Jungle\Data\Storage\Db\Adapter
	 *
	 */
	abstract class Pdo extends Adapter {

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
		 *
		 * host
		 * port
		 * dbname
		 *
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

			$className= '\Jungle\Data\Storage\Db\Dialect\\'. $this->dialectType;
			$this->dialect = new $className();

			$this->connection = new \PDO(
				$options['type'].':'.implode(';',$dsn),
				$username,$password,
				array(
					\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL,
					\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
				)
			);
		}

		/**
		 * @return string
		 */
		public function lastCreatedIdentifier($sequenceName = null){
			return $this->connection->lastInsertId($sequenceName);
		}


		/**
		 * @param $sql
		 * @param null|array $binds
		 * @param array|int|null $types
		 * @param bool $internalStatement if true return \PdeStatement(Internal statement type) or false then ResultInterface
		 * @return ResultInterface|\PdoStatement
		 */
		public function query($sql, $binds = null, $types = null, $internalStatement = false){
			$this->statement = $this->connection->prepare($sql);
			if($this->statement){
				if($internalStatement){
					$result = $this->statement;
					Db\Result\Pdo::mountBindings($result, $binds, $types);
				}else{
					$result = new Db\Result\Pdo($this,$this->statement,$binds,$types);
				}
				if($result->execute()){
					return $result;
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
		public function execute($sql, $binds = null, $types = null){
			$this->statement = $sth = $this->connection->prepare($sql);
			if($sth){
				Db\Result\Pdo::mountBindings($this->statement,$binds,$types);
				if($sth->execute()){
					return $sth->rowCount();
				}else{
					return false;
				}
			}
			return false;
		}

		/**
		 * @param string $sql
		 * @return bool
		 */
		public function executeSimple($sql){
			$this->statement = null;
			return $this->connection->exec($sql);
		}




		/**
		 * @return bool
		 */
		public function hasLastError(){
			if($this->statement){
				return !!$this->statement->errorInfo()[1];
			}else{
				return !!$this->connection->errorInfo()[1];
			}
		}

		/**
		 * @return string
		 */
		public function getLastErrorCode(){
			if($this->statement){
				return $this->statement->errorInfo()[1];
			}else{
				return $this->statement->errorInfo()[1];
			}
		}

		/**
		 * @return mixed
		 */
		public function getLastSQLStateCode(){
			if($this->statement){
				return $this->statement->errorCode();
			}else{
				return $this->connection->errorCode();
			}
		}
		/**
		 * Error Description
		 * @return string
		 */
		public function getLastErrorMessage(){
			if($this->statement){
				return $this->statement->errorInfo()[2];
			}else{
				return $this->connection->errorInfo()[2];
			}
		}

		/**
		 * @return array
		 */
		public function getLastErrorInfo(){
			if($this->statement){
				return $this->statement->errorInfo();
			}else{
				return $this->connection->errorInfo();
			}
		}

		/**
		 * @param null $sequenceName
		 * @return string
		 */
		public function getLastInsertId($sequenceName = null){
			return $this->connection->lastInsertId($sequenceName);
		}

		/**
		 * @return object|\PDO
		 */
		public function getInternalAdapter(){
			return $this->connection;
		}
	}
}

