<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.04.2016
 * Time: 20:43
 */

$prototype = [

	'id'            => 20,
	'name'          => 'Fedor',
	'family'        => 'Semenov',
	'cash'          => [
		'operations'    => [
			[
				'id' => 10,
				'amount' => 5,
				'time' => 213214,
				'info' => [
					'r_i' => 'Seven',
					'rupi' => 'Gurea'
				]
			], [
				'id' => 11,
				'amount' => 2,
				'time' => 213214,
				'info' => [
					'r_i' => 'Erty',
					'rupi' => 'Nuil'
				]
			], [
				'id' => 12,
				'amount' => 3,
				'time' => 213214,
				'info' => [
					'r_i' => 'Fguj',
					'rupi' => 'Poil'
				]
			]
		],
	],
	'children' => [
		[
			'id'            => 21,
			'name'          => 'Fedor2',
			'family'        => 'Semenov2',
			'cash'          => [
				'operations'    => [
					[
						'id' => 10,
						'amount' => 5,
						'time' => 213214,
						'info' => [
							'r_i' => 'Seven',
							'rupi' => 'Gurea'
						]
					], [
						'id' => 11,
						'amount' => 2,
						'time' => 213214,
						'info' => [
							'r_i' => 'Erty',
							'rupi' => 'Nuil'
						]
					], [
						'id' => 12,
						'amount' => 3,
						'time' => 213214,
						'info' => [
							'r_i' => 'Fguj',
							'rupi' => 'Poil'
						]
					]
				],
			],
			'children' => []
		]
	]

];
/**
 * Вариант с указанием полных путей
 */
$data_interface_1 = [
	'id',
	'name',
	'family',
	'cash',
	'cash.operations[]',
	'cash.operations[]id',
	'cash.operations[]amount',
	'cash.operations[]time',
	'cash.operations[]info',
	'cash.operations[]info.r_i',
	'cash.operations[]info.rupi',
	'children[]',
	'children[]:#',

];
/**
 * Ваниант массива
 */
$data_interface_2 = [
	'id',
	'name',
	'family',
	'cash' => [
		'operations[]' => [
			'id',
			'amount',
			'time',
			'info' => [
				'r_i',
				'rupi'
			]
		]
	],
	'children'
];

/**
 * Синтаксический вариант
 */
$data_interface_3  = '
	id			:int
	parent_id	:int
	name		:string
	family		:string
	cash
		operations[]
			id		:int
			amount	:int
			time	:int
			info
				r_i		:string
				rupi	:string
	children[]	:#
';


/**
 * Типизация
 */
$type_definition = 'field_name:datamap'; //assoc array or datamap
$type_definition = 'field_name:type_name';
$type_definition = 'field_name[]:type_name';
$type_definition = 'field_name[]:#'; // :# recursion to base

$type_definition = 'field_name[]:int'; // array (list) integers [1,2,3,4,5,6]