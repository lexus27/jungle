<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 22:26
 */

namespace Jungle\Util\Communication\Http;

use Jungle\Util\Communication\Connection\Stream;
use Jungle\Util\Communication\URL;
use Jungle\Util\Specifications\Hypertext\Header;

include '../../loader.php';


echo <<<'HTML'
<style>
body,html{
	position:relative;
	display:block;
	width: 100%;
}
.block{
	margin:0 auto 20px auto;
	display: inline-block;
	padding:10px;
	color:#171737;
	background: #eae9f0;
	font-family: Consolas, sans-serif;
}
.container{
	display: flex;
	justify-content: center;
	justify-items: center;
	flex-direction: column;
}
</style>
HTML;
echo '<section class="container">';

/**
 * Будем отсылать запросы....
 *
 * 1. Подготовим агента
 */
$agent = new Agent('Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.101 Safari/537.36 OPR/40.0.2308.62');


/**
 * 2. Выставим заголовки по умолчанию, которые будут добавляться на каждый отосланный запрос через данного агента.
 */
$agent->setDefaultHeaders([
	'Connection'                => 'keep-alive',
	'Pragma'                    => 'no-cache',
	'Upgrade-Insecure-Requests' => 1,
	//'Accept'                    => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'
	'Accept'                    => 'application/json, application/xml, application/*'
]);

/**
 * 3. Это так-же влияет на заголовок Accept-Language по умолчанию (Приоритет в порядке убывания)
 */
$agent->setDesiredLanguages([ 'ru-RU','ru','en-US', 'en' ]);

$agent->setCacheManager(New CacheManager());
///**
// * @param Agent $agent
// * @return string
// */
//function test_async_600(Agent $agent){
//	$t = microtime(true);
//	$test_requests = [];
//
//	for($ii=0;$ii<20;$ii++){
//		for($i = 0;$i < 30; $i++){
//			$request = $agent->get('vk.com');
//			$agent->push($request);
//			$test_requests[] = $request;
//		}
//		foreach($test_requests as $request){
//			$response = $agent->pull($request);
//		}
//		$test_requests = [];
//	}
//	return sprintf('%.4F',microtime(true) - $t);
//}
//
///**
// * @param Agent $agent
// * @return string
// */
//function test_sync_600(Agent $agent){
//	$t = microtime(true);
//	for($ii=0;$ii<20;$ii++){
//		for($i = 0;$i < 30; $i++){
//			$request = $agent->get('vk.com');
//			$agent->push($request);
//			$response = $agent->pull($request);
//		}
//	}
//	return sprintf('%.4F',microtime(true) - $t);
//}
//
///**
// * 4. Перейдем непосредственно к запросам
// * Будем производить отправку и отображать Определение Гипертекста для Запроса и Ответа
// * =============================================================================================
// */
//$requests = [];
///**
// * 4.1 GET запрос на главную страницу
// */
//$request = $agent->get('jungle');
//$agent->push($request);
//$requests[] = $request;
//
///**
// * 4.2 GET запрос на контроллер авторизации.
// * Создание происходит прямо здесь
// */
//$request = new Request($agent, 'http://jungle/auth','get');
//$request['Accept'] = 'application/json';
//$agent->push($request);
//$requests[] = $request;
//
///**
// * 4.3 Отправляем данные формы с файлами
// */
//$request = $agent->post('jungle',[
//	'param'     => 'value',
//	'document'  => new Memory('aloha.txt','text/plain','Hello world'), // Это виртуальный файл
//	'image'     => new File(realpath('../../../assets/images/icons/Cerulean-24/2.png')) // Здесь ссылаемся на картинку)
//]);
//$agent->push($request);
//$requests[] = $request;
//
//
//
///**
// * 4.4 Попробуем реально авторизоваться.
// * -------------------------------------
// * Для примера выбран локальный хост, и демо пользователь,
// * но вы можете подставить свои данные и проверить
// */
//$request = $agent->post('http://jungle/auth',[
//	'login'     => 'arhimed',
//	'password'  => '12345'
//]);
//$agent->push($request);
//$requests[] = $request;
//
//
//
//$next_generation = [];
//foreach($requests as $i => $request){
//	$response = $agent->pull($request);
//	echo "<h3># 4.".($i+1)."</h3>";
//	render_payloads($response);
//
//	if($response->isRedirect()){
//		$url = $response->getRedirectUrl(true);
//		$request = $agent->get($url);
//		$agent->push($request);
//		$next_generation[$i] = $request;
//	}
//
//}
//foreach($next_generation as $i => $request){
//	$response = $agent->pull($request);
//	echo "<h3># 4.".($i+1).".1</h3>";
//	render_payloads($response);
//}
//
///**
// * 4.6 GET запрос на гугл
// */
//$request = $agent->get('www.google.com');
//$agent->push($request);
//$response = $agent->pull($request);
//echo '<h3># 4.6</h3>';
//render_payloads($response);
//if($response->isRedirect()){
//	$url = $response->getRedirectUrl(true);
//	$request = new Request($agent, $url,'get');
//	$response = $agent->execute($request);
//	echo '<h3># 4.7</h3>';
//	render_payloads($response);
//	if($response->isRedirect()){
//		$url = $response->getRedirectUrl(true);
//		$request = new Request($agent, $url,'get');
//		$response = $agent->execute($request);
//		echo '<h3># 4.8</h3>';
//		render_payloads($response);
//	}
//}


$request = $agent->get('http://jungle/assets/images/icons/Cerulean-24/2.png');
$response = $agent->execute($request);
echo '<h3># 4.7</h3>';
$response->setHeader('Content-Transfer-Encoding','base64');
render_payloads($response);



$request = $agent->get('http://jungle/assets/images/icons/Cerulean-24/2.png');
$response = $agent->execute($request);
echo '<h3># 4.7</h3>';
$response->setHeader('Content-Transfer-Encoding','base64');
render_payloads($response);

echo '<div>
<img style="margin:0 auto;display:inline-block;" src="'."data:{$response->getHeader('Content-Type')};base64,".base64_encode($response->getContent())."".'">
</div>
';


/**
 * Выводы
 * =============================================================================================
 * Как мы видим в некоторых примерах запросы могут инициироваться автоматически через агента,
 * но мы так-же можем кастомно задать объект запроса.
 */

//
///**
// * Low level request
// */
//$document = <<<'TXT'
//GET / HTTP/1.1
//Connection: keep-alive
//Host: google.com
//Accept: application/json
//
//
//TXT;
//
//$connections = [];
//$concurrent_connections = 5;
//
//for($i=0;$i<=$concurrent_connections;$i++){
//	$connections[$i] = fsockopen('google.com',80);
//}
//$t = microtime(true);
//for($i=0;$i<=100;$i++){
//	foreach($connections as $s){
//		fwrite($s,$document);
//	}
//	foreach($connections as $s){
//		echo '<pre>'.microtime(true)."\r\n";
//		$result = read($s);
//		echo htmlspecialchars($result);
//		echo '</pre>';
//	}
//}
//foreach($connections as $connection){
//	fclose($connection);
//}
//echo '<h1>'.sprintf('%.4F',microtime(true) - $t).'</h1>';
//
//
//
//
//
//function read($s){
//	$headers_process = true;
//	$h = [];
//	$doc = '';
//	$i = 0;
//	while(!($eof = feof($s))){
//		$data = fgets($s);
//		$doc.=$data;
//		$data = trim($data,"\r\n");
//		if($data){
//			if($data = Header::parseHeaderRow($data)){
//				$h[$data[0]] = $data[1];
//			}
//		}else{
//			$headers_process = false;
//		}
//		if(!$headers_process){
//			break;
//		}
//		$i++;
//	}
//	$length = null;
//	$chunked = isset($h['Transfer-Encoding']) && strcasecmp($h['Transfer-Encoding'],'chunked')!==0;
//	if(isset($h['Content-Length'])){
//		$length = $h['Content-Length'];
//		if($length!==null){
//			$length = intval($length);
//		}
//		($length!==null) && ($length = intval($length));
//	}
//
//	$content = '';
//	$block_size = 4072;
//	while(!feof($s)){
//		if($length === null){
//			$data = fgets($s);
//			$length = hexdec(trim($data));
//		}elseif($length > 0){
//			$read_length = $length > $block_size ? $block_size : $length;
//			$length -= $read_length;
//			$data = fread($s,$read_length);
//			$content.= $data;
//			if ($length <= 0) {
//				if($chunked){
//					fseek($s,2,SEEK_CUR);
//				}
//				$length = false;
//			}
//		}else{
//			break;
//		}
//	}
//	$doc.=$content;
//
//	return $doc;
//}


echo '</section>';

function render_payloads(Response $response){
	echo '<section class="block">';

	echo '<pre>';
	echo htmlspecialchars((string)$response->getRequest());
	echo '</pre>';

	echo '<hr/>';

	echo '<pre>';
	echo htmlspecialchars((string)$response);
	echo '</pre>';

	echo '</section>';
}