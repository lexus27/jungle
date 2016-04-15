<?php

$loader = new \Phalcon\Loader();
$loader->registerNamespaces([
	'Jungle' => dirname(dirname(__DIR__)) .  '/core/Jungle/'
]);
$loader->register();

define('TEST_DIR', __DIR__  . DIRECTORY_SEPARATOR . 'filesystem' . DIRECTORY_SEPARATOR);

echo '<pre>';
$b  = TEST_DIR . 'testfolder1' . DIRECTORY_SEPARATOR;
$b2 = TEST_DIR . 'testfolder2' . DIRECTORY_SEPARATOR;
$f  = $b . 'jscode.js';
print_r(pathinfo($f));
print_r(realpath('test/'));
echo '<br>';
print_r(mime_content_type($f));
$tz = new DateTimeZone('Europe/Moscow');
print_r($tz->getOffset(new DateTime('now',new DateTimeZone('Asia/Vladivostok'))));
echo '</pre>';
echo '<p>',date('Y-m-d (H:i:s)',filectime($b.'jscode.js')),'</p>';
echo '<p>',filetype($b.'jscode.js'),'</p>';
echo '<p>',filetype($b),'</p>';
echo '<p>',disk_free_space('E:/'),'</p>';

$manager = new \Jungle\FileSystem\Model\Manager();
$adapter = new \Jungle\FileSystem\Model\Manager\Adapter\Local\Base(TEST_DIR);
$manager->setAdapter($adapter);





$test = $manager->get('testfolder2');

if($test->hasNode('clone_testing')){
	$clone_testing = $test['clone_testing'];
	$mst = microtime(true);
	$total = $clone_testing->countContains(true);
	$mt = microtime(true);
	$clone_testing->delete(true);
	$m = microtime(true);
	echo '<p>Deleted total: '.$total.', counting contains time: "'.sprintf('%.4F',$m - $mst).'" time: "'.sprintf('%.4F',$m - $mt).'"</p>';
}else{

	$mt = microtime(true);

	$clone_testing = $test->newDir('clone_testing');
	$dtp = $test->getNode('directory_test_phantom');
	/** @var \Jungle\FileSystem\Model\Directory $prevClone */
	$prevClone = null;
	for($i=0, $ii=0,$dI = 0;$i<=20;$i++){
		if($ii === 0){
			$dI++;
			$d = $clone_testing->newDir('dir-'.$dI);
		}


		$clone = clone $dtp;
		$clone->setBasename($dtp->getBasename() . '-clone-'.$i);
		$d->addNode($clone);$ii++;
		if($ii===20){
			$ii=0;
			$d->refresh();
		}
	}
	$m = microtime(true);
	echo '<p>Copy COUNT:'.($i).'  Copied total: '.$clone_testing->countContains(true).', time: "'.sprintf('%.4F',$m - $mt).'"</p>';
}






/*
foreach($test->expand(true) as $n){

	if($n->isDir()){
		$n->setPermissions($manager->getDefaultDirPermissions());
	}else{
		$n->setPermissions($manager->getDefaultFilePermissions());
	}
	echo '<p>',$n->getPermissions(true),'</p>';
}*/


/*
$root = $manager->get('E:/Music');


foreach($root->expand(true) as $node){
	if($node instanceof Directory){
		$node->remove();
	}else{
		if(!$root->hasNode($node)){
			$root->addNode($node);
		}
	}
}
function norm(Directory $root,& $num = 0){
	/** @var Directory $chunk *//*
	$chunk = null;
	foreach($root->getChildren() as $node){
		if(!$chunk || $chunk->count() === 5){
			$num++;
			$chunk = $root->newDir('d-'.$num);
		}
		$chunk->addNode($node);
	}
}
$num = 0;
while($root->count() > 5){
	norm($root,$num);
}
function renameAll(Directory $root){
	$i = 0;
	foreach($root->getDirectories() as $node){
		$i++;
		$node->setBasename('chunk-'.($i));
		renameAll($node);
	}
}
renameAll($root);
*/


/*

$manager = new \Jungle\FileSystem\Model\Manager();
$adapter = new \Jungle\FileSystem\Model\Manager\Adapter\Local();
$manager->setAdapter($adapter);
$node = $manager->get($b2);

$t = $node->detachNode('directory_test_phantom');
$t->removeNode('message.txt');
$tC = $node->detachNode('directory_test_phantom(COPY)');
$t->addNode($manager->file('log.txt'));

$t->addNode($tC);


*/

//var_dump($node);





//\Jungle\FileSystem::remove(TEST_DIR.'testfolder2');
//\Jungle\FileSystem::copyNode($b,'testfolder2');
/*
$model = new \Jungle\FileSystem\OldModel\Directory('directory_test_phantom');
$model->addChild(new \Jungle\FileSystem\OldModel\File('message.txt'));

$existing = \Jungle\FileSystem\OldModel::getExisting($b2);
if(!$existing->getChild($model)){
	$existing->addChild($model);
}

$existing->setName('testfolder2');

print_r($existing);

*/
/*


$child = $existing->getChild('test1');
echo '<p>test1 ', 'decoct(',$child->getPermissions(),') === `decimal` ',decoct($child->getPermissions()), ' > octdec(',octdec(decoct($child->getPermissions())),')';

$child = $existing->getChild('test2');
$child1 = $existing->getChild('test2');
echo '<p>test2 ', 'decoct(',$child->getPermissions(),') === `decimal` ',decoct($child->getPermissions()), ' > octdec(',octdec(decoct($child->getPermissions())),')';

$child = $existing->getChild('test3');
echo '<p>test3 ', 'decoct(',$child->getPermissions(),') === `decimal` ',decoct($child->getPermissions()), ' > octdec(',octdec(decoct($child->getPermissions())),')';

$child = $existing->getChild('test5');
echo '<p>test5 ', 'decoct(',$child->getPermissions(),') === `decimal` ',decoct($child->getPermissions()), ' > octdec(',octdec(decoct($child->getPermissions())),')';

$child = $existing->getChild('test6');
echo '<p>test6 ', 'decoct(',$child->getPermissions(),') === `decimal` ',decoct($child->getPermissions()), ' > octdec(',octdec(decoct($child->getPermissions())),')';

$perms = $child->getPermissions();
echo '<br>';


var_dump(decoct(0777 & $perms));
echo '<br>';


var_dump(octdec(decoct($perms & 0777)));
var_dump(decoct($child1->getPermissions() & 0777));

var_dump(decoct($perms ^ 0222));
var_dump(decoct($perms | 0777));
var_dump(decoct(octdec('777')));
var_dump(intval('0777'));
var_dump(intval('0777'));

echo '<br><br><br><br><br><br>';


$d = 040705;

$p = 0540;


$error = ($d & $p);

var_dump(decoct($d ));

echo '<br><br><br><br><br><br>';


echo '</pre>';*/