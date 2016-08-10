<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 12.04.2016
 * Time: 11:46
 */

/**
 * Testing
 * @param $pattern
 * @param $subject
 * @param bool $all
 */
function rgx_tester($pattern , $subject, $all = false){
	if($all){
		$matched = preg_match_all('@'.addcslashes($pattern,'@').'@',$subject,$m, PREG_OFFSET_CAPTURE);
	}else{
		$matched = preg_match('@'.addcslashes($pattern,'@').'@',$subject,$m, PREG_OFFSET_CAPTURE);
	}
	block('Matches',[
		'pattern'   => $pattern,
		'subject'   => $subject,
		'count'     => $matched,
		'matches'   => $m,
	],false);
}

require '../_test_helper.php';
require '../loader.php';


/**
 * Парсинг в кавычках
 */
$pattern = '"([^"\\\\]|(\\\\(")?))*"';
$pattern = '(["\'])((?:\\\\\1|.)*?)\1';
$subject = '"ст\\\\"р\\\'о\'ка\\\"';
rgx_tester($pattern,$subject);
/**
 *
 * Рекурсивные блоки
 *
 */

$pattern = '\{((?:(?>[^{}]+)|(?R))*)\}';
$subject = 'Hello friend "{username{meganame + " 10"}hello}"';
rgx_tester($pattern,$subject);


/**
 * FLOAT
 */
$pattern = '[+-]?\d+\.?\d*';
$subject = '4.3';
rgx_tester($pattern,$subject);


/**
 * INT
 */
$pattern = '[+-]?\d+';
$subject = '43';
rgx_tester($pattern,$subject);

$pattern = '(.)*';
$pattern = '(.)+';
$subject = 'abcd . dfg';
rgx_tester($pattern,$subject);


/**
 * Path recursive
 */

$pattern = '(?>[^,]+)(?:,(?R))*';// with recursion
$pattern = '(?>[^,]+)(?:,(?>[^,]+))*';// without recursion
$subject = 'ab,ac,ad,af,fr,,ag'; // matches: ab,ac,ad,af,fr
rgx_tester($pattern,$subject);

$pattern = '(?>[^\.]+)(?>\.)*(?R)*'; // with recursion
$pattern = '(?>[^\.]+)(?>\.)*(?>[^\.]+)*'; // without recursion
$subject = 'user.name.length..lo'; // matches: user.name.length..lo
$subject = 'user.name.length.lo'; // matches: user.name.length.lo
rgx_tester($pattern,$subject);


$pattern = '@(?<bracket>\{(?:[^\{\}]+|(?P>bracket))*\})@'; // concrete recursive block bracket
$pattern = '@\{(?:[^\{\}]+|(?R))*\}@'; // Full recursive bracket


$pattern = '.+';

/**
 * Escaping
 *
 * TODO
 */
$pattern = '(?!\\\\\\\\)"';
$subject = '\\\\"';
rgx_tester($pattern,$subject);