<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 14.12.2016
 * Time: 23:03
 */

include '../../loader.php';

error_reporting(E_ALL);


$manager = new \Jungle\FileSystem\Model\Manager();
$adapter = new \Jungle\FileSystem\Adapter\Local\Base(__DIR__ . DIRECTORY_SEPARATOR . 'tests', true);
$manager->setAdapter($adapter);
$manager->setDefaultFilePermissions(0777);
$manager->setDefaultDirPermissions(0777);

$Iterator = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator($loader->getNamespacePath('App'), \RecursiveDirectoryIterator::SKIP_DOTS),
	\RecursiveIteratorIterator::CHILD_FIRST
);
/** @var \SplFileInfo $item */
foreach($Iterator as $item){
	echo '<p>'.$item->getPathname().'</p>';
}

