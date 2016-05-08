<?php
include __DIR__ . DIRECTORY_SEPARATOR . 'test' . DIRECTORY_SEPARATOR . 'loader.php';

$t = microtime(true);

$dispatcher = new \Jungle\Application\Dispatcher();
$dispatcher->setControllerNamespace('App\\Controllers');


$router = new \Jungle\Application\Dispatcher\Router\HTTP\Router();
$router->removeExtraRight('/');
$router->any('',[
	'reference' => [
		'controller' 	=> 'index',
		'action' 		=> 'index'
	]
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


$dispatcher->setRouter($router);

$dispatcher->dispatch(\Jungle\HTTPFoundation\Request::fromCurrent());


echo '<br/>'.sprintf('%.4F',microtime(true) - $t);

















