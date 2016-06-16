<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 2:21
 */
namespace modelX ;
use Jungle\Data\Storage\Db;
use Jungle\Data\Storage\Db\Adapter\Pdo\MySQL;

include __DIR__ . DIRECTORY_SEPARATOR . 'index.php';
/** ----------------------------------------------------------
 * -----------------   @DataMap-Tests-Concrete-Use   ----------
 * --------------------------------------------------------------*/

$mysqlConnection = new MySQL([
	'host' => 'localhost',
    'port' => '3306',
    'dbname' => 'jungle',
    'username' => 'root',
    'password' => ''
]);
$mysqlConnection->setDialect(new Db\Dialect\MySQL());

class SimpleRecordSchema extends RecordSchema{

}
class SimpleRecordField extends RecordField{

	public function setDefault($default){
		$this->default = $default;
		return $this;
	}

	public function setNullable($nullable){
		$this->nullable = $nullable;
		return $this;
	}

}
class SimpleIndex extends Index{

}

/**
 * Class SimpleRelationField
 * @package modelX
 */
class SimpleRelationField extends RelationRecordField{

	public function setRelation($type, $self_fields, $reference_schema_name, $reference_fields){
		$this->relation_type = $type;
		$this->self_fields = $self_fields;
		$this->reference_schema = $reference_schema_name;
		$this->reference_fields = $reference_fields;
		return $this;
	}

}

class SimpleSchemaManager extends SchemaManager{
	/**
	 * @param $schemaName
	 * @return ModelSchema
	 */
	public function getSchema($schemaName){
		foreach($this->schemas as $schema){
			if($schema->getName() === $schemaName){
				return $schema;
			}
		}
		return null;
	}

	public function addSchema(RecordSchema $schema){
		$this->schemas[] = $schema;
		return $this;
	}


}

$sourceManager = (new DataBaseSourceManager())->setAdapter($mysqlConnection);

$profile = new SimpleRecordSchema();
$profile->setName('profile');
$profile->addField(new SimpleRecordField('id','integer'));
$profile->addField(new SimpleRecordField('first_name','string'));
$profile->addField((new SimpleRecordField('last_name','string')));
$profile->addField((new SimpleRecordField('city','string')));
$profile->addField((new SimpleRecordField('state','string')));
$profile->addField((new SimpleRecordField('mobilephone','string')));
$profile->addField((new SimpleRelationField('user'))->setRelation(SimpleRelationField::TYPE_BELONGS,['id'],'user',['id']));



$profile->addIndex((new SimpleIndex())->setName('id_primary')->addField('id')->setType(SimpleIndex::TYPE_PRIMARY));
$profile->addIndex((new SimpleIndex())->setName('id_unique')->addField('id')->setType(SimpleIndex::TYPE_UNIQUE));

$profile->setDefaultSourceManager($sourceManager);
$profile->setDefaultSource('ex_user_profile');



$user = new SimpleRecordSchema();
$user->setName('user');
$user->addField(new SimpleRecordField('id','integer'));
$user->addField(new SimpleRecordField('username','string'));
$user->addField((new SimpleRecordField('password','string'))->setOriginalKey('password_hash'));

$user->addField((new SimpleRelationField('profile'))->setRelation(SimpleRelationField::TYPE_ONE,['id'],'profile',['id']));

$user->addIndex((new SimpleIndex())->setName('id_primary')->addField('id')->setType(SimpleIndex::TYPE_PRIMARY));
$user->addIndex((new SimpleIndex())->setName('id_unique')->addField('id')->setType(SimpleIndex::TYPE_UNIQUE));
$user->addIndex((new SimpleIndex())->setName('username_unique')->addField('username')->setType(SimpleIndex::TYPE_UNIQUE));

$user->setDefaultSourceManager($sourceManager);
$user->setDefaultSource('ex_user');


$schemaManager = new SimpleSchemaManager();
$schemaManager->addSchema($user);$user->setSchemaManager($schemaManager);
$schemaManager->addSchema($profile);$profile->setSchemaManager($schemaManager);

$u = $user->load([['id','=',1]]);
foreach($u as $us){

	$dataMap = new DataMap($user, $us);

	echo '<pre>';var_dump($dataMap->profile);echo '</pre>';
}


__halt_compiler();


echo '<table>';
echo '<tr>';
foreach($schema->getFieldNames() as $name){
	echo '<th>';
	echo $name;
	echo '</th>';
}
echo '</tr>';

$dataMap = new DataMap($schema);
$dataMap->username = 'alexey 2';
$dataMap->password = 'Ihejsnwb';
$dataMap->save();
*//*
foreach(DataMap::loadCollection($schema,null) as $item){
	//$item->username = '7777777';
	//$item->password_hash = '888888';
	//$item->save();



	echo '<tr>';
	foreach($item as $v){
		echo '<td>';
		echo $v;
		echo '</td>';
	}
	echo '</tr>';

	//$item->remove();

}
echo '</table>';

//$mysqlConnection->delete('ex_user',null);
//var_dump($mysqlConnection->setAutoIncrement('ex_user',0));



/*
echo '<pre>Кол во строк ';
var_dump($mysqlConnection->insert('ex_user',[
	'username','password_hash'
],[
	['kutuz27','antiboss'],
	['kutuz27_Administrator2','antiboss'],
	['kutuz27_Administrat2or','antiboss'],
	['kutuz27_Administrator','antiboss'],
	['kutuz27_Administ2rator','antiboss'],
	['kutuz27_Administ2rat3or','antiboss'],
	['kutuz27_3Administrator','antiboss'],
	['kutuz27_Administr2ator','antiboss'],
	['kutuz27_Adm2inist3rator','antiboss'],
	['kutuz27_Admini4strator','antiboss'],
	['kutuz27_4Administra3tor','antiboss']
],null,true));
echo '</pre>';
*/

if($mysqlConnection->hasLastError()){
	echo '<pre>',print_r($mysqlConnection->getLastErrorCode(), 1),'</pre>';
	echo '<pre>',print_r($mysqlConnection->getLastErrorInfo(), 1),'</pre>';
}

$t = microtime(true);
$records = $mysqlConnection->fetchAll([
	'table' => 'ex_user',
	'columns' => '*',
	'where' => [
		'condition' => [
			['id','IN',[1,2]],'OR',
			['id','IN',[3,4]],
		],
		'extra' => true
	]
]);
echo sprintf('%.4F',microtime(true) - $t);


echo '<pre>',print_r($records, 1),'</pre>';

/**
 * @Storage
 * Нужно получить набор записей
 *
 * Нужна прослойка которая создает структуру источников для записей.
 * Поддержка Коллекции для результирующих наборов записей.
 * Структура множества источников и типов записей - это поможет для определения связей между типами записей
 *
 *
 */

/**
 * @Relation
 * Нужно иметь доступ к связаным записям в системе
 */




