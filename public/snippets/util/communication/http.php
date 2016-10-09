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
	'Connection'    => 'keep-alive',
	//'Accept'        => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'
	'Accept'        => 'application/json, application/xml, application/*'
]);

/**
 * 3. Это так-же влияет на заголовок Accept-Language по умолчанию (Приоритет в порядке убывания)
 */
$agent->setDesiredLanguages([ 'ru-RU','ru','en-US', 'en' ]);


/**
 * 4. Перейдем непосредственно к запросам
 * Будем производить отправку и отображать Определение Гипертекста для Запроса и Ответа
 * =============================================================================================
 */
//
//
///**
// * 4.1 GET запрос на главную страницу
// */
//$response = $agent->get('jungle');
//render_payloads($response);
//
//
///**
// * 4.2 GET запрос на контроллер авторизации.
// * Создание происходит прямо здесь
// */
//$request = new Request('http://jungle/auth','get');
//$request['Accept'] = 'application/json';
//$response = $agent->execute($request);
//render_payloads($response);
//
//
///**
// * 4.3 Отправляем данные формы с файлом
// */
//$response = $agent->post('jungle',[
//	'param' => 'value'
//],[
//	'document'  => new Memory('aloha.txt','text/plain','Hello world'), // Это виртуальный файл
//	'image'     => new File(realpath('../../../assets/images/icons/Cerulean-24/2.png')) // Здесь ссылаемся на картинку)
//]);
//render_payloads($response);
//
//
///**
// * 4.4 Попробуем реально авторизоваться.
// * -------------------------------------
// * Для примера выбран локальный хост, и демо пользователь,
// * но вы можете подставить свои данные и проверить
// */
//$request = new Request('http://jungle/auth','post');
//$request->setPostParams([
//	'login'     => 'arhimed',
//	'password'  => '12345'
//]);
//$response = $agent->execute($request);
//render_payloads($response);
//
//
//if($response->isRedirect()){
//	/**
//	 * Здесь разберем примерное ручное реагирование на редирект.
//	 * В ответ на Редирект Hypertext не может реагировать автоматически,
//	 * т.к этот компонент работает в контексте только одного документа,
//	 * а реакция на редирект подразумевает еще 1 запрос(итого еще 2 документа(Запрос-Ответ)).
//	 */
//	$url = $response->getRedirectUrl(true);
//	$request = new Request($url,'get');
//	$response = $agent->execute($request);
//	render_payloads($response);
//}


/**
 * 4.1 GET запрос на гугл
 */
$response = $agent->get('www.google.com');
render_payloads($response);
if($response->isRedirect()){
	$url = $response->getRedirectUrl(true);
	$request = new Request($url,'get');
	$response = $agent->execute($request);
	render_payloads($response);
	if($response->isRedirect()){
		$url = $response->getRedirectUrl(true);
		$request = new Request($url,'get');
		$response = $agent->execute($request);
		render_payloads($response);
		if($response->isRedirect()){
			$url = $response->getRedirectUrl(true);
			$request = new Request($url,'get');
			$response = $agent->execute($request);
			render_payloads($response);
			if($response->isRedirect()){
				$url = $response->getRedirectUrl(true);
				$request = new Request($url,'get');
				$response = $agent->execute($request);
				render_payloads($response);
				if($response->isRedirect()){
					$url = $response->getRedirectUrl(true);
					$request = new Request($url,'get');
					$response = $agent->execute($request);
					render_payloads($response);
					if($response->isRedirect()){
						$url = $response->getRedirectUrl(true);
						$request = new Request($url,'get');
						$response = $agent->execute($request);
						render_payloads($response);
					}
				}
			}
		}
	}
}


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