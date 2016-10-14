<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 13.10.2016
 * Time: 22:54
 */

$q = isset($_GET['query'])?$_GET['query']:'max_age';
switch($q){
	case 'max_age':
		header("Cache-Control: max-age=15");
		echo 'Content';
		break;
	case 'etag':
		header("ETag: \"sdfdsf4523sdfweDWasa\"");
		echo 'Content';
		break;
	case 'expired':
		$datetime = date(DATE_RFC2822, time() + 15);
		header("Last-Modified: {$datetime}");
		echo 'Content';
		break;
	case 'last_modified':
		$datetime = date(DATE_RFC2822, time() + 15);
		header("Last-Modified: {$datetime}");
		echo 'Content';
		break;


	default:

		echo 'Use the query parameter in url with key "query" (example: query=max_age) <br/>allowed values:<br/> [max_age || etag || last_modified || expired]';
		break;
}