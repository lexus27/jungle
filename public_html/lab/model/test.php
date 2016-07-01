<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.06.2016
 * Time: 19:13
 */

use Jungle\Data\Record;
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

class AbstractTestComplex implements TestCollectionInterface{

	/** @var  Test[]  */
	protected $tests = [];

	/**
	 * @param $name
	 * @param int $priority
	 * @return TestBlock
	 */
	public function addBlock($name,$priority = 0){
		return $this->tests[$name] = new TestBlock($priority);
	}

	/**
	 * @param Closure $closure
	 * @param int $priority
	 * @param bool $active
	 * @param null $alias
	 * @return $this
	 */
	public function addTest(\Closure $closure, $priority = 0,$active = false, $alias = null){
		if(!$alias){
			$alias = count($this->tests);
		}
		$this->tests[$alias] = new Test($closure, $priority, $active);
		return $this;
	}

	/**
	 * @param Closure $closure
	 * @param int $priority
	 * @param bool $active
	 * @param null $alias
	 * @return $this
	 */
	public function addAliasedTest($alias, \Closure $closure, $priority = 0,$active = false){
		if(!$alias){
			$alias = count($this->tests);
		}
		$this->tests[$alias] = new Test($closure, $priority, $active);
		return $this;
	}


	/**
	 * @param $testName
	 * @return $this
	 */
	public function disable($testName){
		if(isset($this->tests[$testName])){
			$this->tests[$testName]->setActive(false);
		}
		return $this;
	}

	/**
	 * @param $testName
	 * @return $this
	 */
	public function activate($testName){
		if(isset($this->tests[$testName])){
			$this->tests[$testName]->setActive(true);
		}
		return $this;
	}

	public function getTest($testName){
		if(isset($this->tests[$testName])){
			return $this->tests[$testName];
		}
		return null;
	}


	/**
	 * @return mixed
	 */
	public function getTests(){
		return $this->tests;
	}

}
/**
 * Class TestManager
 */
class TestManager extends AbstractTestComplex{

	/**
	 * @param $testName
	 * @param int $priority
	 * @return $this
	 */
	public function setPriority($testName, $priority = 0){
		if(isset($this->tests[$testName])){
			$this->tests[$testName]->setPriority($priority);
		}
		return $this;
	}

	/**
	 * @param $array
	 * @return TestInterface[]
	 */
	public static function sort($array){
		static $fn;
		if(!$fn){
			$fn = function(Test $a,Test $b){
				$a = $a->getPriority();
				$b = $b->getPriority();
				if($a === $b){
					return 0;
				}
				return $a > $b? 1 : -1;
			};
		}
		usort($array,$fn);
		return $array;
	}

	/**
	 * @param null $testName
	 * @param array $arguments
	 */
	public function run($testName = null, array $arguments = []){
		if($testName){
			if(isset($this->tests[$testName])){
				$this->tests[$testName]->run($this,$arguments);
			}
		}else{
			$this->aggregate($this);
		}
	}

	/**
	 * @param TestCollectionInterface|TestInterface $test
	 */
	public function aggregate(TestCollectionInterface $test,array $arguments = []){
		foreach($this->sort($test->getTests()) as $test){
			if($test->isActive()){
				$test->run($this,$arguments);
			}
		}
	}

}

interface TestCollectionInterface{

	/**
	 * @return TestInterface[]
	 */
	public function getTests();

}

/**
 * Interface TestInterface
 */
interface TestInterface{

	/**
	 * @param $active
	 * @return $this
	 */
	public function setActive($active);

	/**
	 * @return bool
	 */
	public function isActive();

	/**
	 * @param int $priority
	 * @return $this
	 */
	public function setPriority($priority = 0);

	/**
	 * @return int
	 */
	public function getPriority();

	/**
	 * @param TestManager $manager
	 * @param array $arguments
	 * @return
	 */
	public function run(TestManager $manager,array $arguments = []);
}



/**
 * Class Test
 */
class Test implements TestInterface{
	/** @var bool  */
	protected $active = true;
	/** @var int  */
	protected $priority = 0;
	/** @var Closure  */
	protected $closure;

	protected $start_time = 0;

	protected $last_execute_time;

	/**
	 * Test constructor.
	 * @param Closure $closure
	 * @param int $priority
	 * @param bool $active
	 */
	public function __construct(\Closure $closure, $priority = 0, $active = false){
		$this->closure = $closure;
		$this->priority = $priority;
		$this->active = $active;
	}

	/**
	 * @param $active
	 * @return $this
	 */
	public function setActive($active){
		$this->active = $active;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isActive(){
		return $this->active;
	}

	/**
	 * @param int $priority
	 * @return $this
	 */
	public function setPriority($priority = 0){
		$this->priority = $priority;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPriority(){
		return $this->priority;
	}

	/**
	 * @param TestManager $manager
	 * @param array $argumetns
	 */
	public function run(TestManager $manager, array $argumetns = []){
		$this->_beforeRun();
		$this->_run($manager,$argumetns);

		$this->_afterRun();
	}

	/**
	 * @param TestManager $manager
	 * @param array $arguments
	 */
	protected function _run(TestManager $manager, array $arguments = []){
		call_user_func_array($this->closure,$arguments);
	}

	/**
	 *
	 */
	protected function _beforeRun(){
		$this->start_time = microtime(true);
	}

	/**
	 *
	 */
	protected function _afterRun(){
		$this->last_execute_time = sprintf('%.4F',microtime(true) - $this->start_time);
	}

}

/**
 * Class TestBlock
 */
class TestBlock extends AbstractTestComplex implements TestInterface{

	/** @var  TestInterface[]  */
	protected $tests = [];

	/** @var bool  */
	protected $active = true;
	/** @var int  */
	protected $priority = 0;

	protected $start_time = 0;

	protected $last_execute_time;

	public function __construct($priority = 0){
		$this->setPriority($priority);
	}

	/**
	 * @param TestManager $manager
	 * @param array $arguments
	 */
	public function run(TestManager $manager, array $arguments = []){
		$this->_beforeRun();
		$this->_run($manager,$arguments);
		$this->_afterRun();
	}


	/**
	 * @param TestManager $manager
	 */
	protected function _run(TestManager $manager, array $arguments = []){
		$manager->aggregate($this,$arguments);
	}

	/**
	 *
	 */
	protected function _beforeRun(){
		$this->start_time = microtime(true);
	}

	/**
	 *
	 */
	protected function _afterRun(){
		$this->last_execute_time = sprintf('%.4F',microtime(true) - $this->start_time);
	}

	/**
	 * @return mixed
	 */
	public function getTests(){
		return $this->tests;
	}

	/**
	 * @param $active
	 * @return $this
	 */
	public function setActive($active){
		$this->active = $active;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isActive(){
		return $this->active;
	}

	/**
	 * @param int $priority
	 * @return $this
	 */
	public function setPriority($priority = 0){
		$this->priority = $priority;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPriority(){
		return $this->priority;
	}
}

$test = new TestManager();

$test->addAliasedTest('create',function() use($schemaU,$schemaN, $schemaG){
	// Test: Create
	$user = new Record\DataMap($schemaU);
	$user->username = uniqid('username_');
	$user->password = uniqid('username_');

	for($i = 0 ; $i < 5; $i++){
		$newNote = new Record\DataMap($schemaN);
		$newNote->header = 'Head body';
		$newNote->body = 'Test body';

		// Direct
		$user->notes->add($newNote);
		// Back
		//$newNote->user = $user;

	}

	// Test: Create & Use Through
	foreach($schemaG->load(null,5) as $group){
		// Direct ADD
		$user->memberIn->add($group);
		// Back ADD
		//$group->members->add($user);
	}

	$user->save();

});

$test->addAliasedTest('update',function($id) use($schemaU,$schemaN, $schemaG,$schemaP){
	// Test: Update
	$user = $schemaU->loadFirst($id);

	// Add Profile (One relation)
	$profile = new Record\DataMap($schemaP);
	$profile->first_name = 'Alexeyi';
	$profile->last_name = 'Kutuz27';
	$profile->city = 'Khabarovskiy kray';
	$user->profile = $profile;




	// ADD to Many
	$newNote = new Record\DataMap($schemaN);
	$newNote->header = 'Head body 2 ';
	$newNote->body = 'Test body 2 ';
	$user->notes->add($newNote);

	// Remove From Many
	//$user->notes->remove(['header'=>'Head body 2 ']);

	// Remove Through
	//$user->memberIn = [];

	// ADD after all remove
	$newGroup = $schemaG->initializeRecord();
	$newGroup->title    = uniqid('group2_');
	$newGroup->rank     = rand(5,20);
	$user->memberIn->add($newGroup);

	$user->save();
});


$test->addAliasedTest('remove',function($id) use($schemaU,$schemaN, $schemaG,$schemaP){
	// Test: Remove
	$user = $schemaU->loadFirst($id);
	echo '<p>Count '.$user->notes->count().'</p>';
	foreach($user->notes as $note){
		echo '<p>'.$note->getIdentifierValue().'</p>';
	}
	$user->remove();
	echo '<p>Count '.$user->notes->count().'</p>';
	foreach($user->notes as $note){
		echo '<p>'.$note->getIdentifierValue().'</p>';
	}
});


$test->addAliasedTest('sorting',function() use($schemaU,$schemaN, $schemaG,$schemaP){
	$collection = $schemaG->load(null,null,null,
		(new Record\Collection\Sorter())->setSortFields(['title' => 'DESC'])
	);

	foreach($collection as $object){
		echo $object->title.'<br/>';
	}

});


$test->run('sorting');


echo '<p/>Loaded: '.$manager->getStatusRecordsLoadedCount();
echo '<p/>Instantiated: '.$manager->getStatusRecordsInstantiatedCount();