<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 04.03.2016
 * Time: 12:53
 */
namespace Jungle\Data\Storage\Db\Adapter\Pdo {

	use Jungle\Data\Storage\Db\Adapter\Pdo;
	use Jungle\Data\Storage\Exception\DuplicateEntry;
	use Jungle\Data\Storage\Exception\Operation;

	/**
	 * Class MySQL
	 * @package Jungle\Data\Storage\Db\Adapter\Pdo
	 */
	class MySQL extends Pdo{

		/** @var string */
		protected $driverType = 'mysql';

		/** @var string */
		protected $dialectType = 'MySQL';

		public function __construct(array $options = [ ]){
			parent::__construct(array_replace_recursive([
				'attributes' => [
					\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
				]
			],$options));
		}


		/**
		 * @param null $type
		 * @param \Exception $e
		 * @throws DuplicateEntry
		 * @throws Operation
		 * @throws \Exception
		 */
		public function _handleOperationException($type = null,  \Exception $e = null){
			if($e instanceof \PDOException){


				// TODO Здесь требуется комплексный подход к определению большинства типичных ошибок операции

				$code = $this->getLastErrorCode();
				$message = $this->getLastErrorMessage();

				//$code = $e->getCode();
				//$message = $e->getMessage();

				switch($code){

					case 1062://Duplicate entry
						if(preg_match('@Duplicate entry \'(.+)\' for key \'(.+)\'@smi',$message, $m)){
							throw new DuplicateEntry($m[2], $m[1],$message, $code, $e);
						}else{
							throw new \Exception('Invalid parse message "'.$message.'" from PDO Exception for code '.$code.' (Operation: "'.$type.'")',$code, $e);
						}
						break;
				}
				throw new Operation($e->getMessage(),$e->getCode(),$e);
			}
			throw $e;
		}

	}
}

