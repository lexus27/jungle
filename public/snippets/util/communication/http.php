<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 22:26
 */

namespace Jungle\Util\Communication\HttpClient;

use Jungle\Util\Communication\HttpClient\CacheManager\CacheManager;
use Jungle\Util\Communication\Hypertext\Header;
use Jungle\Util\Communication\URL;
use Jungle\Util\Contents\File;
use Jungle\Util\Contents\Memory;

include '../../loader.php';

/**
 * Будем отсылать запросы....
 *
 * 1. Подготовим агента
 */
$agent = new Agent('Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.101 Safari/537.36 OPR/40.0.2308.62');

/**
 * 1.1 Указываем кеш-менеджер
 */
$agent->setCacheManager(New CacheManager());


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


ob_implicit_flush(true);



/**
 * 4. Перейдем непосредственно к запросам
 * Будем производить отправку и отображать Определение Гипертекста для Запроса и Ответа
 * =============================================================================================
 */
$requests = [];
/**
 * 4.1 GET запрос на главную страницу
 */
$request = $agent->createGet('jungle');
$agent->push($request);
$requests[] = $request;

/**
 * 4.2 GET запрос на контроллер авторизации.
 * Создание происходит прямо здесь
 */
$request = new Request($agent, 'http://jungle/auth','get');
$request['Accept'] = 'application/json';
$agent->push($request);
$requests[] = $request;

/**
 * 4.3 Отправляем данные формы с файлами
 */
$request = $agent->createPost('jungle',[
	'param'     => 'value',
	'document'  => new Memory('aloha.txt','text/plain','Hello world'), // Это виртуальный файл
	'image'     => new File(realpath('../../../assets/images/icons/Cerulean-24/2.png')) // Здесь ссылаемся на картинку)
]);
$agent->push($request);
$requests[] = $request;



/**
 * 4.4 Попробуем реально авторизоваться.
 * -------------------------------------
 * Для примера выбран локальный хост, и демо пользователь,
 * но вы можете подставить свои данные и проверить
 */
$request = $agent->createPost('http://jungle/auth',[
	'login'     => 'arhimed',
	'password'  => '12345'
]);
$agent->push($request);
$requests[] = $request;



$next_generation = [];
foreach($requests as $i => $request){
	$response = $agent->pull($request);
	echo "<h3># 4.".($i+1)."</h3>";
	render_payloads($response);

	if($response->isRedirect()){
		$url = $response->getRedirectUrl(true);
		$request = $agent->createGet($url);
		$agent->push($request);
		$next_generation[$i] = $request;
	}

}
foreach($next_generation as $i => $request){
	$response = $agent->pull($request);
	echo "<h3># 4.".($i+1).".1</h3>";
	render_payloads($response);
}


/**
 * 4.6 GET запрос на гугл
 */
$request = $agent->createGet('www.google.ru');
$agent->push($request);
$response = $agent->pull($request);
echo '<h3># 4.6</h3>';
render_payloads($response);
if($response->isRedirect()){
	$url = $response->getRedirectUrl(true);
	$request = new Request($agent, $url,'get');
	$response = $agent->execute($request);
	echo '<h3># 4.7</h3>';
	render_payloads($response);
	if($response->isRedirect()){
		$url = $response->getRedirectUrl(true);
		$request = new Request($agent, $url,'get');
		$response = $agent->execute($request);
		echo '<h3># 4.8</h3>';
		render_payloads($response);
	}
}



$request = $agent->createGet('http://jungle/assets/images/icons/Cerulean-24/2.png');
$response = $agent->execute($request);
echo '<h3># 4.7</h3>';
$response->setHeader('Content-Transfer-Encoding','base64');
render_payloads($response);



$request = $agent->createGet('http://jungle/assets/images/icons/Cerulean-24/2.png');
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