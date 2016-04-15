<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 12.04.2016
 * Time: 14:41
 */

namespace router;

class Pattern{

	protected $definition;

}


/**
 * Location matcher
 */

$example = '/user/'; // folder | directory
$example = '/user/10'; // concrete

$example = '/user?id=10&sort_field=name&dir=asc'; // pass parameters default
$example = '/user/id=10/sort_field=name/dir=asc'; // pass parameters in path
$example = '/user/10/name/asc'; // parameters for function arguments (not named) (ID, Sort_Field, Direction)

$pattern = '/user/{parameters ... (\w+)\.(.+) ... / }'; // many params Property = Key - pairs


$pattern = '/user/(user_id & int & (\d+))';
$pattern = '/user/{user_id:int:\d+}';

$data = [
	'user_id' => 0
];


$pattern = '/:controller/:action';
$data = [
	'controller'    => '',
	'action'        => '',
];
$pattern = '/user/(\d+)';
$data = [
	1 => 0
];


/**
 * Depth control matcher
 */
$example = '/user/settings';

$data = [

	'path' => ['user','settings'],

	'root' => [
		'name' => 'user',
		'children' => [
			'name' => 'settings'
		]
	]

];
/**
 * @Router-Configured
 *  user
 *      settings
 *          ui
 *          account
 *          profile
 *      messages
 *          create
 *          edit
 *  users
 *      view{int; (\d+); user_id; !!$recognized}
 *          write_message
 *  news
 *  about
 *  account
 *      registration{;;;!$di->user}
 *      activation{;;;!$di->user}
 *      login{;;;!$di->user}
 *      logout{;;;!!$di->user}
 *
 *
 *
 */
$RouterConfigured = [

	'routes' => [

		[

			'name' => 'user',

			'children' => [[
				'name' => 'settings',
				'children' => [[
					'name' => 'ui'
				],[
					'name' => 'account'
				],[
					'name' => 'profile'
				]]
			],[
				'name' => 'messages',
				'children' => [[
					'name' => 'create'
				],[
					'name' => 'edit'
				]]
			]]

		],[
			'name' => 'users',
			'children' => [[
				'name'          => 'view',
				'pattern'       => '\d+',
				'type'          => 'int',
				'as'            => 'user_id',
				'before'        => function($di, $recognized){return !!$recognized;},
				'children'      => [[
					'name' => 'write_message'
				]]
			]]
		],[
			'name' => 'news'
		],[
			'name' => 'about'
		],[
			'name' => 'account',
			'children' => [[
				'name' => 'registration',
				'before' => function($di, $recognized){return !$di->user;}
			],[
				'name' => 'auth',
				'before' => function($di, $recognized){return !$di->user;}
			],[
				'name' => 'activation',
				'before' => function($di, $recognized){return !$di->user;}
			],[
				'name' => 'logout',
				'before' => function($di, $recognized){return !!$di->user;}
			]]
		]

	]

];