<?php


use Jungle\Application\Dispatcher\Reference;

include(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'loader.php');

execute([
	'module' => 'manager',
	'controller' => 'user.auth',
	'action' => 'error'
],[
	'priority'  => [
		'action'=> Reference::SAFE_STRICT
	],
	'monopoly'  => null,
	'queue'     => ['action','controller','module'],
]);


execute([
	'module' => 'manager',
	'controller' => 'user.auth',
	'action' => 'error'
],[
	'priority'  => [
		'action'=> Reference::SAFE_STRICT
	],
	'monopoly'  => 'controller',
	'queue'     => ['action','controller','module'],
]);


execute([
	'module' => 'manager',
	'controller' => 'user.auth',
	'action' => 'error'
],[
	'priority'  => [
		'action'=> Reference::SAFE_STRICT,
	    'controller' => Reference::SAFE_NAMESPACE
	],
	'monopoly'  => 'controller',
	'queue'     => ['action','controller','module'],
]);





function execute($reference, $config){
	$config['reference'] = '<span style="color:coral">#'.$reference['module'] . '</span>:<span style="color:#00d2b6">' . $reference['controller'] . '</span>:<span style="color:#49bd34">' . $reference['action'] . '</span>';
	echo '<h3>Configuration</h3>';
	echo '<pre>';
	echo var_export($config,true);
	echo '</pre>';

	echo '<h3>Result</h3>';

	$result = Reference::getSequence($reference, $config['priority'], $config['monopoly'], $config['queue']);
	foreach($result as $v){
		echo '<p>
		<span style="color:coral">#'.$v['module'] . '</span>:
		<span style="color:#00d2b6">' . $v['controller'] . '</span>:
		<span style="color:#49bd34">' . $v['action'] . '</span>
	</p>';
	}
}
