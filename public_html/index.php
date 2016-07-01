<?php
$t = microtime(true);
include __DIR__ . DIRECTORY_SEPARATOR . 'lab' . DIRECTORY_SEPARATOR . 'loader.php';
$dispatcher = new \Jungle\Application\Dispatcher();
$dispatcher->registerModules([
	'index' => [
		'class' => App\Modules\Index::class
	]
]);

$router = new \Jungle\Application\Dispatcher\Router\HTTP\Router();
$router->removeExtraRight('/');
$router->any('/',[
	'reference' => [
		'controller' 	=> 'index',
		'action' 		=> 'index'
	],
	'modify' => false
])->setName('root');

$router->post('/user/login',[
	'reference' => [
		'controller' 	=> 'user',
		'action' 		=> 'login'
	]
])->setName('user-login');

$router->any('/{id:int.nozero}',[
	'reference' => [
		'controller' 	=> 'user',
		'action' 		=> 'index'
	]
])->setName('user-info-short');

$router->any('/user/{id:int.nozero}',[
	'reference' => [
		'controller' 	=> 'user',
		'action' 		=> 'index'
	]
])->setName('user-info');

$dispatcher->addRouter($router);
$dispatcher->dispatch(\Jungle\Http\Request::fromCurrent());
echo '<br/>'.sprintf('%.4F',microtime(true) - $t);
echo '<pre>',print_r($dispatcher->getModule('index')->getControllerNames(), 1),'</pre>';