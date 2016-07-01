<?php
$loader = new \Phalcon\Loader();
$loader->registerNamespaces([
	'Jungle' => dirname(dirname(dirname(dirname(__DIR__)))) . DIRECTORY_SEPARATOR . 'core' .DIRECTORY_SEPARATOR . 'Jungle' . DIRECTORY_SEPARATOR
]);
$loader->register();


$strategy = new \Jungle\XPlate\WebStrategy();
$store = new \Jungle\Util\Smart\Keyword\Storage\Files([
	'directory' => __DIR__ . DIRECTORY_SEPARATOR . '_tmp' . DIRECTORY_SEPARATOR
]);
$strategy->setTagManager(new \Jungle\XPlate\Services\TagPool($store));
$strategy->setAttributeManager(new \Jungle\XPlate\Services\AttributePool($store));
$strategy->setStyleManager(new \Jungle\XPlate\Services\StylePool($store));
$strategy->lock();
$document = new Jungle\XPlate\HTML\Document($strategy);

