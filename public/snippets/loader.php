<?php
namespace Jungle;

include dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR .  'Jungle' . DIRECTORY_SEPARATOR . 'Loader.php';

$loader = Loader::getDefault();
$loader->registerNamespaces([
	'Jungle' => dirname(dirname(__DIR__)) .  '/Jungle',
	'App' => dirname(dirname(__DIR__)) .  '/App'
]);
$loader->register();