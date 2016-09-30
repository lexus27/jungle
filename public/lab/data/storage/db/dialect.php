<?php


$loader = new \Phalcon\Loader();
$loader->registerNamespaces([
	'Jungle' => dirname(dirname(dirname(dirname(__DIR__)))) .  '/core/Jungle/'
]);
$loader->register();


$dialect = new \Jungle\Storage\Db\Dialect\MySQL();

echo "<pre>Select: \r\n\r\n",$dialect->select([
	'table'     => ['notes','doodle'],
	'alias'     => 'a',
	'columns'   => ['a.id','a.title'],
	'limit'     => [10,0],
	'order_by'  => [
		'title' => 'ASC'
	],
	'group_by'  => 'a.title',
	'lock_in_shared' => true
]);
echo '</pre>';



echo "<pre>CreateTable: \r\n\r\n",$dialect->createTable('doodle.notes',[
	[
		'name'              => 'id',
		'type'              => 'int',
		'unsigned'          => true,
		'notnull'           => true,
		'size'              => 11,
		'auto_increment'    => true,
		'primary'           => true,
		'unique'            => true
	],[
		'name'  => 'title',
		'type'  => 'varchar',
		'notnull'   => true,
		'size'      => 255,
	],[
		'name'      => 'content',
		'notnull'   => false,
		'type'      => 'text',
	]
],[],[],true);
echo '</pre>';