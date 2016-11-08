<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.10.2016
 * Time: 13:08
 */
namespace Jungle\Util\Communication\HttpClient;

use Jungle\Util\Communication\Connection\Stream;
use Jungle\Util\Communication\HttpClient\CacheManager\CacheManager;
use Jungle\Util\Communication\Hypertext\Header;
use Jungle\Util\Communication\URL;

/** @var Agent $agent */
include __DIR__.'/_initializing/agent.php';

$agent->setCacheManager(New CacheManager());

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