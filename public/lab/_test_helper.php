<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 12.04.2016
 * Time: 11:50
 */
/**
 * @param null $title
 * @param null $php_value
 * @param bool $dump
 */
function block($title = null , $php_value = null, $dump = true){
	echo "<div><h3>$title</h3><pre>";
	ob_start();
	$dump?var_dump($php_value):print_r($php_value);
	$c = ob_get_clean();
	echo htmlspecialchars($c);
	echo '</pre></div>';
}

/**
 * @param $title
 */
function head($title){
	echo "<h1>$title</h1>";
}

/**
 * @param $string
 */
function view($string){
	echo "<p>$string</p>";
}
