<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 06.01.2017
 * Time: 19:58
 */
namespace orm_2{

	use App\Model\Hierarchical;
	use App\Model\User;
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






	$schema = User::getModelSchema();
	$name = $schema->getName();

	echo '<h1>Reverse Path from referenced schema!</h1>';

	$_ = [
		'profile.last_name',
		'profile.user',
		'notes.comments' // check to Collection reverse
	];
	$a = [];
	foreach($_ as $u){
		$path = $schema->analyzePath($u);
		$a[$name . '.' . $u] = $path;
	}
	echo '<pre>';
	print_r($a);
	echo '</pre>';










	$schema = Hierarchical::getModelSchema();
	$name = $schema->getName();

	echo '<h1>Reverse Path from referenced schema!</h1>';

	$_ = [
		'parent',
		'children',
	];
	$a = [];
	foreach($_ as $u){
		$path = $schema->analyzePath($u);
		$a[$name . '.' . $u] = $path;
	}
	echo '<pre>';
	print_r($a);
	echo '</pre>';
}


