<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.05.2016
 * Time: 0:56
 */
$mt = microtime(true);
$loader = new \Phalcon\Loader();

define('APP_PATH',__DIR__ . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR);
$loader->registerNamespaces([
	'App' => APP_PATH
]);
$loader->register();
$di = new \Phalcon\Di\FactoryDefault();

$di->setShared('router',function(){
	$router = new \Phalcon\Mvc\Router();
	$router->setDefaultNamespace('App\Controller');
	$router->removeExtraSlashes(true);
	$router->add('/hello',[
		'controller' => 'index',
		'action' => 'hello'
	]);
	for($i = 0; $i < 100 ; $i++){
		$router->add('/hello'.$i,[ 'controller' => 'index'.$i, 'action' => 'hello'.$i ]);
	}
	return $router;
});
$di->setShared('view',function(){
	return new \Phalcon\Mvc\View();
});

$application = new \Phalcon\Mvc\Application($di);
$response = $application->handle('/hello');

echo $response->getContent();

echo '<p>Time: '.sprintf('%.4F', microtime(true) - $mt).' sec.</p>';
