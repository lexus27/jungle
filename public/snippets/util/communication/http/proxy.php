<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 13.10.2016
 * Time: 14:22
 */
include '../../../loader.php';
use Jungle\Util\Specifications\Hypertext\Header;

$socks_servers = [
	[ '188.120.236.14', 80 , ['4','5'] ],
	[ '92.255.238.84', 1080, ['4'] ]
];

$stream = socks5('127.0.0.1',1081,'jungle',80);
fwrite($stream,<<<HTTP
GET / HTTP/1.1
Accept: application/json
Host: jungle


HTTP
);
echo '<pre>';
echo htmlspecialchars(read($stream));
echo '</pre>';

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


function socks5($ip,$port, $to_host,$to_port){

	$socks = fsockopen($ip,$port);

	//Initiate the SOCKS handshake sequence.

	//Write our version an method to the server.
	//Version 5, 1 authentication method, no authentication. (For now)
	fwrite($socks, pack("C3", 0x05, 0x01, 0x00) );

	//Wait for a reply from the SOCKS server.
	$status = fread($socks,8192);

	//Check if server status is okay.
	if ( $status == pack("C2", 0x05, 0x00) ) {
		$status_code = 2;
	} else {
		//Throw error if required.
		throw new \Exception(
			"SOCKS Server does not support this version and/or authentication method of SOCKS."
		);
	}

	//At this stage, our SOCKS socket should be open and ready to make its remote connection.
	//Send the connection request.
	fwrite( $socks, pack("C5", 0x05 , 0x01 , 0x00 , 0x03, strlen($to_host) ) . $to_host . pack("n", $to_port) );

	//Wait for a reply from the SOCKS server.
	$buffer = fread($socks,8192);


	$state = substr($buffer,0,4);

	if($state == pack("C4", 0x05, 0x00, 0x00, 0x01)){
		$status_code = 3;
	}else {
		//Connection failed.
		throw new \Exception(
			"The SOCKS server failed to connect to the specificed host and port. ( ".$to_host.":".$to_port." )"
		);
	}

	//Our socket is now ready to write data.
	if ($status_code == 3) {
		return $socks;
	}
	return null;
}



/*
if( $ch = curl_init())
{
	curl_setopt ($ch, CURLOPT_URL, 'http://jungle');
	curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
	curl_setopt ($ch, CURLOPT_PROXY, "127.0.0.1:1081");
	curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt ($ch, CURLOPT_FAILONERROR, true);
	curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
	$result = curl_exec($ch);
	print curl_errno($ch);
	print curl_error($ch);
	//print $result;
	echo $result;
	curl_close ($ch);
}
*/