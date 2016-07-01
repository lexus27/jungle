<?php

/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 27.03.2016
 * Time: 7:31
 */


/**
 *
 * Request Headers
 *
 * GET /test/client.php HTTP/1.1
 * Host: jungle
 * Connection: keep-alive
 * Pragma: no-cache
 * Cache-Control: no-cache
 * Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*;q=0.8
 * Upgrade-Insecure-Requests: 1
 * User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.75 Safari/537.36 OPR/36.0.2130.32
 * Accept-Encoding: gzip, deflate, lzma, sdch
 * Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4
 *
 * @see getallheaders();
 *
 */

/**
 *
 * Нету нужных функций получения точных данных отправляемых заголовков в PHP
 *
 * Response Headers
 *
 * HTTP/1.1 200 OK
 * Connection: Keep-Alive
 * Content-Length: 3525
 * Content-Type: text/html; charset=UTF-8
 * Date: Sat, 26 Mar 2016 21:38:21 GMT
 * Keep-Alive: timeout=10, max=100
 * Server: Apache/2.4.10 (Win32)
 *
 */

/**
 * Class Request
 */
class Request{

	protected $host;

	protected $method;

	protected $requested_with;

	protected $content_type;

	protected $content_charset;

	protected $accept_language;

	protected $accept_encoding;

	protected $body;



}

setcookie('cookie_name','my_value',$_SERVER['REQUEST_TIME'] + 87001,'/',$_SERVER['HTTP_HOST'],false,false);
//setrawcookie();
//header();
//headers_sent();
//header_remove();
//headers_list();
//header_register_callback();

//$list= getallheaders();
ob_start();
$list=apache_request_headers();
//$list = headers_list();
echo '<pre>Request headers ';
var_dump($list);
echo '</pre>';
echo '<pre>Request ';
var_dump($_REQUEST);
echo '</pre>';
echo '<pre>Cookie ';
var_dump($_COOKIE);
echo '</pre>';
$response = headers_list();

echo '<pre>Headers to sent ';
var_dump(headers_sent(),$response);
echo '</pre>';

header_register_callback(function(){});

echo '<pre>Server and ENV';

var_dump($_SERVER,$_ENV);

echo '</pre>';

ob_end_flush();

$value = file_get_contents('php://input');
$r = $value;
$_FILES;
echo '<form method="post" enctype="multipart/form-data">

	<input type="hidden" name="property_1" value="value_hidden_field">

	<input type="text" name="property_2">

	<input type="file" name="img1"/>

	<input type="file" name="img2"/>

	<input type="submit" value="Отправить">

</form>';

