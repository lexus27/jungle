<?php
$loader = new \Phalcon\Loader();
$loader->registerNamespaces([
	'Jungle' => dirname(dirname(__DIR__)) .  '/core/Jungle/',
	'App' => dirname(dirname(__DIR__)) .  '/core/App/'
]);
$loader->register();