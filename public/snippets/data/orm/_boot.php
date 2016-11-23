<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.11.2016
 * Time: 20:14
 */

namespace data_orm_test;

use Jungle\Data\Record\Field\Integer;
use Jungle\Data\Record\Model;
use Jungle\Data\Record\Repository;
use Jungle\Data\Record\Schema\Schema;
use Jungle\Data\Storage\Db\Adapter\Pdo\MySQL;

include '../../loader.php';

class MyRepository extends Repository{
	/**
	 * @param $storage
	 * @return mixed
	 * @throws \Exception
	 */
	public function getStorageService($storage){
		if(is_object($storage)){
			return $storage;
		}

		static $d = null;
		if(!$d){
			$mt = microtime(true);
			$mysql = new MySQL([
				'host'      => 'localhost',
				'port'      => '3306',
				'dbname'    => 'doodle',
				'username'  => 'root',
				'password'  => '',
				'attributes' => [
					\PDO::ATTR_PERSISTENT => true
				]
			]);
			$mysql->setDialect( new \Jungle\Data\Storage\Db\Dialect\MySQL());
			$d = [
				'database' => $mysql
			];
			echo '<p>'.sprintf('%.4F',microtime(true) - $mt).'</p>';

		}
		return $d[$storage];
	}


}


Repository::setDefault(new MyRepository());

class AbstractModel extends Model{

	public $id;

	public static function initialize(Schema $schema){
		$schema->setPk('id', true);
		$schema->setField(new Integer('id'));
	}

}
