<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.10.2016
 * Time: 13:25
 */

use Jungle\Util\Communication\Hypertext\Header;

include '../../../../loader.php';



/**
 * Low level request
 */
$document = <<<'TXT'
GET / HTTP/1.1
Connection: keep-alive
Host: google.com
Accept: application/json


TXT;

$total = 100;
$concurrent_connections = 5;

$sequences = round($total / $concurrent_connections,0);
for($i=0;$i < $concurrent_connections;$i++){
	$connections[$i] = fsockopen('google.com',80);
}
$connections = [];
$t = microtime(true);
for($i=0;$i < $sequences;$i++){
	foreach($connections as $connection){
		fwrite($connection,$document);
	}
	foreach($connections as $connection){
		//echo '<pre>';
		$result = read($connection);
		//echo htmlspecialchars($result);
		//echo '</pre>';
	}
	$connections = [];
}
foreach($connections as $connection){
	fclose($connection);
}
echo '<h1>'.sprintf('%.4F',microtime(true) - $t).'</h1>';





function read($s){
	$headers_process = true;
	$h = [];
	$doc = '';
	$i = 0;
	while(!($eof = feof($s))){
		$data = fgets($s);
		$doc.=$data;
		$data = trim($data,"\r\n");
		if($data){
			if($data = Header::parseHeaderRow($data)){
				$h[$data[0]] = $data[1];
			}
		}else{
			$headers_process = false;
		}
		if(!$headers_process){
			break;
		}
		$i++;
	}
	$length = null;
	$chunked = isset($h['Transfer-Encoding']) && strcasecmp($h['Transfer-Encoding'],'chunked')!==0;
	if(isset($h['Content-Length'])){
		$length = $h['Content-Length'];
		if($length!==null){
			$length = intval($length);
		}
		($length!==null) && ($length = intval($length));
	}

	$content = '';
	$block_size = 4072;
	while(!feof($s)){
		if($length === null){
			$data = fgets($s);
			$length = hexdec(trim($data));
		}elseif($length > 0){
			$read_length = $length > $block_size ? $block_size : $length;
			$length -= $read_length;
			$data = fread($s,$read_length);
			$content.= $data;
			if ($length <= 0) {
				if($chunked){
					fseek($s,2,SEEK_CUR);
				}
				$length = false;
			}
		}else{
			break;
		}
	}
	$doc.=$content;

	return $doc;
}