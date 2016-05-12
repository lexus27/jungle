<?php

include dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'Jungle' . DIRECTORY_SEPARATOR . 'Loader.php';

$loader = \Jungle\Loader::getDefault();
$loader->registerNamespaces([
	'Jungle' => dirname(dirname(__DIR__)) .  '/core/Jungle',
	'App' => dirname(dirname(__DIR__)) .  '/core/App'
]);
$loader->register();