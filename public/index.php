<?php
ini_set('dispay_errors','on');
error_reporting(E_ALL);
$t = microtime(true);
include dirname(__DIR__) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Jungle' . DIRECTORY_SEPARATOR . 'Loader.php';

$loader = \Jungle\Loader::getDefault();
$loader->registerNamespaces([
	'Jungle' => dirname(__DIR__) .  '/core/Jungle',
	'App' => dirname(__DIR__) .  '/core/App'
]);
$loader->register();
$app = new \App\Application($loader);

$response = $app->handle(\Jungle\Http\Request::getInstance());
$response->send();