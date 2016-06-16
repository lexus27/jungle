<?php


use Jungle\Data\Storage\Db\Lexer\Sign;
use Jungle\Data\Storage\Db\Lexer\SignGroup;

require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'loader.php';


$manager = new \Jungle\Data\Storage\Db\Lexer\SignManager(\Jungle\Smart\Keyword\Storage::getDummy());
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
	->setName('primary_column_constraint')
	->setRecognizer('PRIMARY KEY|PRIMARY')
);
$signPool->push( (new Sign())
	->setName('unique_column_constraint')
	->setRecognizer('UNIQUE KEY|UNIQUE')
);
$signPool->push( (new Sign())
	->setName('column_notnull')
	->setRecognizer('NOT NULL')
);
$signPool->push( (new Sign())
	->setName('column_default')
	->setRecognizer('(DEFAULT ([\w]+|\'.+?\'|NULL)|NULL)')
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