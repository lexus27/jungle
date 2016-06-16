<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.06.2016
 * Time: 19:13
 */

use Jungle\Data\Foundation\Record;
use Jungle\Data\Storage\Db;


include dirname(__DIR__) . DIRECTORY_SEPARATOR . 'loader.php';
/** ----------------------------------------------------------
 * -----------------   @DataMap-Tests-Concrete-Use   ----------
 * --------------------------------------------------------------*/
$t = microtime(true);
$mysqlConnection = new Db\Adapter\Pdo\MySQL([
	'host' => 'localhost',
	'port' => '3306',
	'dbname' => 'jungle',
	'username' => 'root',
	'password' => ''
]);
$mysqlConnection->setDialect(new Jungle\Data\Storage\Db\Dialect\MySQL());

$manager = new Record\Head\SchemaManager();

$manager->addSchema($schemaU = new Record\Head\Schema('user'));
$collectionU = $schemaU->getCollection();
$schemaU->setSource('ex_user');
$schemaU->setStorage($mysqlConnection);
$schemaU->setSchemaManager($manager);

$schemaU->addField((new Record\Head\Field('id','integer')));
$schemaU->addField((new Record\Head\Field('username','string')));
$schemaU->addField((new Record\Head\Field('password','string'))->setOriginalKey('password_hash'));
$schemaU->addField((new Record\Head\Field\Relation('profile',''))->hasOne('profile',['id'],['id']));
$schemaU->addField((new Record\Head\Field\Relation('notes',''))->hasMany('user_note',['id'],['user_id']));
$schemaU->addField((new Record\Head\Field\Relation('memberIn',''))->hasManyThrough('user_group_member','user_group',['id'],['user_id'],['group_id'],['id']));

$manager->addSchema($schemaP = new Record\Head\Schema('profile'));
$collectionP = $schemaP->getCollection();
$schemaP->setSource('ex_user_profile');
$schemaP->setStorage($mysqlConnection);
$schemaP->setSchemaManager($manager);

$schemaP->addField((new Record\Head\Field('id','integer')));
$schemaP->addField((new Record\Head\Field('first_name','string')));
$schemaP->addField((new Record\Head\Field('last_name','string')));
$schemaP->addField((new Record\Head\Field('city','string')));
$schemaP->addField((new Record\Head\Field('state','string')));
$schemaP->addField((new Record\Head\Field\Relation('user','string'))->belongsTo('user',['id'],['id'],null,['onDelete' => Record\Head\Field\Relation::ACTION_CASCADE, 'onDeleteVirtual' => false]));

$manager->addSchema($schemaN = new Record\Head\Schema('user_note'));
$collectionN = $schemaN->getCollection();
$schemaN->setSource('ex_user_note');
$schemaN->setStorage($mysqlConnection);
$schemaN->setSchemaManager($manager);

$schemaN->addField((new Record\Head\Field('id','integer')));
$schemaN->addField((new Record\Head\Field('user_id','integer'))->internal());
$schemaN->addField((new Record\Head\Field('header','string')));
$schemaN->addField((new Record\Head\Field('body','string')));
$schemaN->addField((new Record\Head\Field\Relation('user','string'))->belongsTo('user',['user_id'],['id'],null,['onDelete' => Record\Head\Field\Relation::ACTION_CASCADE, 'onDeleteVirtual' => false]));



$manager->addSchema($schemaG = new Record\Head\Schema('user_group'));
$collectionG = $schemaG->getCollection();
$schemaG->setSource('ex_user_group');
$schemaG->setStorage($mysqlConnection);
$schemaG->setSchemaManager($manager);

$schemaG->addField((new Record\Head\Field('id','integer')));
$schemaG->addField((new Record\Head\Field('title','string')));
$schemaG->addField((new Record\Head\Field('rank','integer')));
$schemaG->addField((new Record\Head\Field\Relation('members',''))->hasManyThrough('user_group_member','user',['id'],['group_id'],['user_id'],['id']));


$manager->addSchema($schemaGM = new Record\Head\Schema('user_group_member'));
$collectionGM = $schemaGM->getCollection();
$schemaGM->setSource('ex_user_group_member');
$schemaGM->setStorage($mysqlConnection);
$schemaGM->setSchemaManager($manager);

$schemaGM->addField((new Record\Head\Field('id','integer')));
$schemaGM->addField((new Record\Head\Field('user_id','integer'))->internal());
$schemaGM->addField((new Record\Head\Field('group_id','integer'))->internal());
$schemaGM->addField((new Record\Head\Field\Relation('user','string'))->belongsTo('user',['user_id'],['id']));
$schemaGM->addField((new Record\Head\Field\Relation('group','string'))->belongsTo('user_group',['group_id'],['id']));

/*
// Test: Create
$newUser = new Record\DataMap($schemaU);
$newUser->username = uniqid('username_');
$newUser->password = uniqid('username_');

for($i = 0 ; $i < 10; $i++){
	$newNote = new Record\DataMap($schemaN);
	$newNote->header = 'Head body';
	$newNote->body = 'Test body';

	// Direct
	$newUser->notes->add($newNote);
	// Back
	$newNote->user = $newUser;

}

// Test: Create & Use Through

foreach($schemaG->load(null,5) as $group){
	// Direct ADD
	$newUser->memberIn->add($group);
	// Back ADD
	//$group->members->add($newUser);
}

$newUser->save();
*/


/*
// Test: Update
$user = $schemaU->loadFirst(81);

// Add Profile (One relation)
//$profile = new Record\DataMap($schemaP);
//$profile->first_name = 'Alexeyi';
//$profile->last_name = 'Kutuz27';
//$profile->city = 'Khabarovskiy kray';
//$user->profile = $profile;




// ADD to Many
//$newNote = new Record\DataMap($schemaN);
//$newNote->header = 'Head body 2 ';
//$newNote->body = 'Test body 2 ';
//$user->notes->add($newNote);

// Remove From Many
//$user->notes->remove(['header'=>'Head body 2 ']);

// Remove Through
//$user->memberIn = [];

// ADD after all remove
//$newGroup = $schemaG->initializeRecord();
//$newGroup->title    = uniqid('group2_');
//$newGroup->rank     = rand(5,20);
//$user->memberIn->add($newGroup);


$user->save();

*/

// Test: Remove
$user = $schemaU->loadFirst(81);
foreach($user->notes as $note){
	echo '<p>'.$note->getIdentifierValue().'</p>';
}
$user->remove();
foreach($user->notes as $note){
	echo '<p>'.$note->getIdentifierValue().'</p>';
}



/*
$result = $schemaU->find(['id' => 52 ]);
echo '<pre>Count ';var_dump($result->count());echo '</pre>';
foreach($result as $record){
	echo $record->username.'</br>';
}
*/


//
// В случае когда такой объект был загружен ранее, данных запрос вернет все тот же объект,
// при этом не загрузив посредника, т.к данная запись была отфильтрована контролем присутствия (NOT IN existing_ids)
//
//$collection = $schemaU->findThrough('profile',['id'=>'id'],['first_name' => 'Alexey'],null);
//
//$collection = $schemaU->findThrough('profile',['id'=>'id'],null,['username'=>'Anutik']);


/*
echo '</br><h2>Users '.$collectionU->count().'</h2></br>';
foreach($collectionU as $record){
	echo $record->getIdentifierValue().' - '.$record->username.'</br>';
}
echo '</br><h2>Profiles '.$collectionP->count().'</h2></br>';
foreach($collectionP as $record){
	echo $record->getIdentifierValue().' - '.$record->first_name.' '.$record->last_name.'</br>';
}
*/
/*
$user = $schemaU->loadFirst(1);

var_dump($user->username);

echo '<h4>User</h4>';
foreach($user as $k => $v){
	echo '<p>'.$k.' : '.$v.'</p>';
}
echo '<h4>Profile</h4>';
foreach($user->profile as $k => $v){
	echo '<p>'.$k.' : '.$v.'</p>';
}
echo '<h4>Notes('.$user->notes->count().' items)</h4>';
foreach($user->notes as $note){
	var_dump($note->user === $user); // такая фишка займет очень много времени, т.к каждый раз происходит выборка из БД
	// Решение: реализовать Relationship collection = Когда произойдет выборка коллекции $user->notes,
	// в каждый note при итерации внутри загрузчика Collection , будет происходить выставление user,
	// и при таком обращении $note->user - значение просто отдастся из стека свойств для связаного поля
	echo '<p>Note('.$note->id.')</p>';
	foreach($note as $k => $v){
		echo '<p>'.$k.' : '.$v.'</p>';
	}
}
*/
/*
// Добавление НОВОГО объекта в MANY
$noter = new Record\DataMap($schemaN);
$noter->header = 'Записка 4';
$noter->body = 'Текст 4';

$user->notes[] = $noter;*/
//$user->save();


/*
$newUser = new Record\DataMap($schemaU);
$newUser->username = uniqid('username_');
$newUser->password = uniqid('username_');

echo '<h4>Member In(' . $user->memberIn->count() . ' items)</h4>';
foreach($user->memberIn as $group){
	echo '<p>Group('.$group->id.')</p>';
	foreach($group as $k => $v){
		echo '<p>'.$k.' : '.$v.'</p>';
	}
	$newUser->memberIn->add($group);
}
var_dump($newUser->save());
*/

//$user->notes->removeItem($schemaN->loadFirst(10));

//$a = $schemaG->findFirst(1);
//$user->memberIn->remove($a);


/*
$newGroup = new Record\DataMap($schemaG);
$newGroup->title    = uniqid('group_');
$newGroup->rank     = rand(5,20);
$user->memberIn->add($newGroup);*/

//var_dump($user->save());

/*
$object = $schemaU->loadFirst(51);
$notes = $object->notes;
echo '<p>Count '.$notes->count().'</p>';
foreach($notes as $n){
	echo '<p>'.$n->getIdentifierValue().'</p>';
}
$object->remove();
echo '<p>Count '.$notes->count().'</p>';
foreach($notes as $n){
	echo '<p>'.$n->getIdentifierValue().'</p>';
}

*/
/*
$notes = $newUser->notes;
for($i = 0;$i<15;$i++){
	$newNote = new Record\DataMap($schemaN);
	$newNote->header = uniqid('note_');
	$newNote->body = uniqid('note_');
	$notes->add($newNote);
}
echo sprintf('%.4F',microtime(true) - $t);
var_dump($newUser->save());
*/

echo '<p/>Loaded: '.$manager->getStatusRecordsLoadedCount();
echo '<p/>Instantiated: '.$manager->getStatusRecordsInstantiatedCount();