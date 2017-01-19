<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.01.2017
 * Time: 4:19
 */


use Jungle\Data\Record\Repository;
use Jungle\FileSystem\Adapter\Local\Base;

include '../../loader.php';


/**
 * disk_manager initialize
 */

$application_base_dirname = $loader->getNamespacePath('App');
// {app_base_dirname}/../../ -> D:\OpenServer\domains\Jungle\core\App\..\..\
$application_base_dirname = dirname($application_base_dirname);

$adapter = new Base($application_base_dirname, false, 'windows-1251');

$manager = new \Jungle\FileSystem\Model\Manager();
$manager->setAdapter($adapter);

/**
 * Schema base repository initialize directories for RelationFileSystem
 */
$public = $manager->get('/public');
$public->tag = 'public';

Repository::getDefault()
	->setDirectory('public',$public);



$condition = new \Jungle\Data\Record\Condition\Condition();


$condition->prepare(App\Model\User::getModelSchema());

