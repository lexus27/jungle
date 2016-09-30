<?php


use Jungle\Data\Storage\Db\Lexer\Sign;
use Jungle\Data\Storage\Db\Lexer\SignGroup;

$loader = new \Phalcon\Loader();
$loader->registerNamespaces([
	'Jungle' => dirname(__DIR__) .  '/core/Jungle/'
]);
$loader->register();


$dialect = new \Jungle\Data\Storage\Db\Dialect\MySQL();

$servant = new \Jungle\Data\Storage\Db\Sql();/*
echo '<pre>',$dialect->select($servant,[
	'table'     => ['notes','doodle'],
	'alias'     => 'a',
	'columns'   => ['a.id','a.title'],
	'limit'     => [10,0],
	'order_by'  => [
		'title' => 'ASC'
	],
	'group_by'  => 'a.title',
	'lock_in_shared' => true
]);*/

/*
echo '<pre>',$dialect->createTable('doodle.notes',[
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

*/

$manager = new \Jungle\Data\Storage\Db\Lexer\SignManager(\Jungle\Util\Smart\Keyword\Storage::getDummy());
$lexer = new \Jungle\Data\Storage\Db\Lexer();
$lexer->setSignManager($manager);

$signPool = $manager->getPool('SignPool');

$signPool->push( (new Sign())
	->setName('create_table')
	->setBeginner(true)
	->setRecognizer('CREATE TABLE')
	->setAfterSequence([
		[null, 'if_not_exists'],
		'table_name',
		new SignGroup('columns',[
			'column_definition'
		])
	])
);

$signPool->push( (new Sign())
	->setName('column_definition')
	->setRecognizer('(?:[\w`]+[\w\d`]+)')
	->setAfterSequence([
		'column_type',
		['column_size',null],
		['column_unsigned',null],
		['column_notnull',null],
		['column_default',null],
		['column_auto_increment',null]
	])
);

$signPool->push( (new Sign())
	->setName('column_type')
	->setRecognizer('[\w]+')
);
$signPool->push( (new Sign())
	->setName('column_size')
	->setRecognizer('\(\d+(?:\s*,\s*\d+s*)?\)')
);
$signPool->push( (new Sign())
	->setName('column_unsigned')
	->setRecognizer('UNSIGNED')
);
$signPool->push( (new Sign())
	->setName('column_notnull')
	->setRecognizer('NOT NULL')
);
$signPool->push( (new Sign())
	->setName('column_default')
	->setRecognizer('DEFAULT ([\w]+|\'.+?\'|NULL)')
);
$signPool->push( (new Sign())
	->setName('column_auto_increment')
	->setRecognizer('AUTO_INCREMENT')
);

$signPool->push( (new Sign())
	->setName('if_not_exists')
	->setRecognizer('IF NOT EXISTS')
);

$signPool->push( (new Sign())
	->setName('table_name')
	->setRecognizer('[\w`\.]+[\.\d\w`]+')
);
echo '<pre>';
$sql = 'CREATE TABLE IF NOT EXISTS `doodle`.`notes` (
	`id` int(11) unsigned not null,
	`title` varchar(255) not null
)';

/**
 * @param \Jungle\Data\Storage\Db\Lexer\Token[]|\Jungle\Data\Storage\Db\Lexer\Token $holders
 * @return string
 */
function printHolders($holders){
	$html = '';
	if(is_array($holders)){
		$html= '<div style="font-size:26px;">';
		foreach($holders as $holder){
			$html.= printHolders($holder);
		}
		$html.= '</div>';
	}elseif($holders instanceof \Jungle\Data\Storage\Db\Lexer\Token){
		$html = '<div style="font-size:26px;padding:7px 7px;margin:5px 5px;border: solid 1px;">';

		$html.= '<p>Recognized: <span style="color:darkcyan;font-size:32px;font-weight:bold;">'.$holders->getRecognized().'</span></p>';
		$html.= '<p>TokenName: <span style="color:red;font-size:28px;font-weight:bold;">'.$holders->getSign()->getName().'</span></p>';
		$combinations = $holders->getAfterSequence();
		if($combinations){
			$html.= '<div style="margin-left:10px">';
			$html.= '<p>Contains: </p>';
			$html.= printHolders($combinations);
			$html.= '</div>';
		}


		$html.='</div>';
	}elseif($holders instanceof \Jungle\Data\Storage\Db\Lexer\TokenGroup){
		$html= '<div><span style="font-size:38px;font-weight:bold;">'.($holders->getName()?' Group `'.$holders->getName().'`:</span>':'');
		foreach($holders->getTokens() as $holder){
			$html.= printHolders($holder);
		}
		$html.= '</div>';
	}





	return $html;
}

$recognized = $lexer->recognize($sql);
if($recognized){
	echo '<span style="font-size:48px;color:orangered;">'.$sql.'</span>';
	echo printHolders($recognized);
}
exit(1);





$host = "127.0.0.1";
$user = "root";
$pass = "";
$db = "doodle";

try
{
	$dbh = new PDO("mysql:host=$host;port=3306;dbname=$db",$user,$pass,[
		\PDO::ATTR_CURSOR => \PDO::ATTR_CURSOR_NAME,
		\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
	]);

	$statement = $dbh->prepare('SELECT * FROM `notes`');
	$statement = $dbh->query('SELECT * FROM `notes`');

	$statement->setFetchMode(\PDO::FETCH_CLASS);

	$statement->execute();
	echo '<pre>';

	echo '<h1>', $statement->queryString  ,'</h1>';
	echo '<h1>',var_export($statement->columnCount(),true)  ,'</h1>';

	class OBJ{

		protected $title;

		protected $id;

		public function getTitle(){
			return $this->title;
		}
	}
	while(($row = $statement->fetch(\PDO::FETCH_NUM,\PDO::FETCH_ORI_ABS,5))){

		echo "\r\n".'ROW: ';

		print_r($row);
	}

	echo '</pre>';
}
catch (Exception $e)
{
	echo "Unable to connect: " . $e->getMessage() ."<p>";
}
/*
$db = new \Phalcon\Db\Adapter\Pdo\Mysql([
	'dbname' => 'doodle',
	'host' => 'localhost',
	'port' => '3306',
	'username' => 'root',
	'password' => '',
]);
echo '<pre>';
//var_dump($db->insert('notes',['NewHello3'],['title'],[\Phalcon\Db\Column::BIND_PARAM_STR]));
var_dump($db->lastCreatedIdentifier());
*/


/*
echo '<pre>';
print_r(preg_split('@\s*(AND|OR)\s*@i','id = ? and name = ? or (title LIKE %Time% and created_at > ?)',-1,PREG_SPLIT_DELIM_CAPTURE));
*//*
echo '<pre>'.$dialect->select([
		'tables' => [
			'notes',
			[
				'table' => 'table2',
				'addJoin' => 'INNER'
			]
		],
		'columns' => '*'
	]);

*/
//$query = $db->fetchAll();

//$query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
//print_r($query->fetchAll());