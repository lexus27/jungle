<?php
ini_set('display_errors','on');
error_reporting(E_ALL);
$t = microtime(true);

define('JUNGLE_DIRNAME', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Jungle');

/**
 * Include loader file
 */
include JUNGLE_DIRNAME . DIRECTORY_SEPARATOR . 'Loader.php';

/**
 * Loader registers
 */
$loader = \Jungle\Loader::getDefault();
$loader->registerNamespaces([
	'Jungle' => JUNGLE_DIRNAME,
	// test app name = App = namespace App
	'App' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'App'
]);
$loader->register();

/**
 * Application instantiate
 */
$app = new \App\Application($loader);
$response = $app->handle(\Jungle\Http\Request::getInstance());
$response->send();