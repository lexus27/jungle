<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 14.04.2016
 * Time: 18:17
 */


/**
 *
 *
 * Numeric text reaction
 * (Single|Several|Many)
 * 1            Single
 * 2 - 4        Several
 * 5 - 9 & 0    Many
 *
 */

/**
 * @param $numeric
 * @param null $one
 * @param null $several
 * @param null $many
 * @return string
 */
function amount($numeric, $one = null, $several = null, $many = null){
	$numeric = (string)floatval($numeric);
	$last_num = intval(substr($numeric,-1));
	if($last_num === 1){
		return $one;
	}elseif($last_num > 1 && $last_num < 5){
		return $several;
	}else{
		return $many;
	}
}

/**
 * @param $numeric
 * @param null $one
 * @param null $several
 * @param null $many
 * @param string $before
 * @param string $prefix
 * @return string
 */
function wrap_num($numeric, $one = null, $several = null, $many = null, $before = ' ', $prefix = ''){
	return $prefix . $numeric . $before . amount($numeric, $one, $several, $many);
}
$e = new \Exception;
echo wrap_num(190, '', 'а', 'ов',' Баклажан');





