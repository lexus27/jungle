<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.10.2016
 * Time: 13:10
 */

namespace Jungle\Util\Communication\HttpClient;
ob_implicit_flush(true);
include_once __DIR__.'/../../../../loader.php';
include_once __DIR__.'/helpers.php';

/**
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
