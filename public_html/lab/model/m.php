<?php

/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.06.2016
 * Time: 22:49
 */
use Jungle\Data\Storage\Db;

include dirname(__DIR__) . DIRECTORY_SEPARATOR . 'loader.php';

$di = new \Jungle\Di();
$di->setShared('schema',\Jungle\Data\Foundation\Record\Head\SchemaManager::getDefault());
$di->container('database')
	->setOverlapFrom('mysql',function($di){
		$adapter = new Db\Adapter\Pdo\MySQL([
			'host'      => 'localhost',
			'port'      => '3306',
			'dbname'    => 'jungle',
			'username'  => 'root',
			'password'  => ''
		]);
		$adapter->setDialect(new Jungle\Data\Storage\Db\Dialect\MySQL());
		return $adapter;
	});
$users = \App\Model\User::find();
foreach($users as $record){
	if(($profile = $record->profile)){
		echo '<p>'.$profile->first_name.'</p>';
	}else{
		echo '<p>'.$record->username.'</p>';
	}
	foreach($record->notes as $note){
		echo '<p>'.$note->title.'</p>';
	}
}
