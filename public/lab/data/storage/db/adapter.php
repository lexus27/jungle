<?php

/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.05.2016
 * Time: 2:55
 */
include dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'loader.php';
use Jungle\Storage\Db;


$dialect = new Db\Dialect\MySQL();
$adapter = new Db\Adapter\Pdo\MySQL([
	'host' => 'localhost',
	'port' => '3306',
	'dbname' => 'jungle',
	'username' => 'root',
	'password' => ''
]);
$adapter->setDialect($dialect);



foreach($adapter->listTables() as $name){

	$columns = $adapter->describeColumns($name);
	$indexes = $adapter->describeIndexes($name);
	$references = $adapter->describeReferences($name);

	echo '<div style="padding:5px;margin:5px;border:1px solid;">';
	echo '<h1>'.$name.'</h1>';
	$r = false;
	echo '<table>';
	echo '<caption>Columns</caption>';
	foreach($columns as $column){
		if(!$r){
			echo '<tr>';
			foreach($column as $key => $value){
				echo '<th>';
				echo $key;
				echo '</th>';
			}
			echo '</tr>';
			$r = true;
		}
		echo '<tr>';
		foreach($column as $key => $value){
			echo '<td>';
			echo is_array($value)?implode(', ',$value):$value;
			echo '</td>';
		}
		echo '</tr>';
	}
	echo '</table>';

	$r = false;
	echo '<table>';
	echo '<caption>Indexes</caption>';
	foreach($indexes as $index){
		if(!$r){
			echo '<tr>';
			foreach($index as $key => $value){
				echo '<th>';
				echo $key;
				echo '</th>';
			}
			echo '</tr>';
			$r = true;
		}
		echo '<tr>';
		foreach($index as $key => $value){
			echo '<td>';
			echo is_array($value)?implode(', ',$value):$value;
			echo '</td>';
		}
		echo '</tr>';
	}
	echo '</table>';


	$r = false;
	echo '<table>';
	echo '<caption>References</caption>';
	foreach($references as $reference){
		if(!$r){
			echo '<tr>';
			foreach($reference as $key => $value){
				echo '<th>';
				echo $key;
				echo '</th>';
			}
			echo '</tr>';
			$r = true;
		}
		echo '<tr>';
		foreach($reference as $key => $value){
			echo '<td>';
			echo is_array($value)?implode(', ',$value):$value;
			echo '</td>';
		}
		echo '</tr>';
	}
	echo '</table>';
	echo '</div>';
}

echo '<pre>';var_dump($adapter->listTables());echo '</pre>';
echo '<pre>';var_dump($adapter->describeColumns('ex_user'));echo '</pre>';
echo '<pre>';var_dump($adapter->describeReferences('ex_user_profile'));echo '</pre>';




//$records = $mysqlConnection->fetchAll([
	//'table' => 'ex_user',
	//'columns' => '*',
//	'where' => [
//		'condition' => [
//			['id','IN',[1,2]],'OR',
//			['id','IN',[3,4]],
//		],
//		'extra' => true
//	]
//]);
//echo '<pre>',print_r($records, 1),'</pre>';


