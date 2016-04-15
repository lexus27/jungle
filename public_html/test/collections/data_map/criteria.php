<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 06.04.2016
 * Time: 20:36
 */
namespace DataMap {


	/** МАССИВ ПРАВИЛ
	 * Поддерживает блоки
	 */
	$criteria = [
		['field_name', ':OPERATOR:', 'wanted_value'],
		':DELIMITER:',
		[
			['field_name', ':OPERATOR:', 'wanted_value'],
			':DELIMITER:',
			['field_name', ':OPERATOR:', 'wanted_value'],
		],
		':DELIMITER:',
		['field_name', ':OPERATOR:', 'wanted_value'],
	];

	/**
	 * АССОЦИАТИВНЫЙ МАССИВ ПРАВИЛ
	 * Не полный эффект критерий
	 */
	$criteria = [
		'field_name1:OPERATOR' => 'wanted_value:DELIMITER',
		'field_name2:OPERATOR' => 'wanted_value:DELIMITER',
		'field_name3:OPERATOR' => 'wanted_value:DELIMITER',
	];

	/** СТРОКА
	 * Поддерживает блоки
	 */
	$criteria = '
		field_name :OPERATOR: wanted_value #DELIMITER# (
			field_name :OPERATOR: wanted_value #DELIMITER#
			field_name :OPERATOR: wanted_value
		) #DELIMITER# field_name :OPERATOR: wanted_value'
	;

}

