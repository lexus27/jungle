<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.10.2016
 * Time: 13:12
 */

namespace Jungle\Util\Communication\HttpClient;
echo <<<'HTML'
<style>
body,html{
	position:relative;
	display:block;
	padding:0;
	margin:0;
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
.container, body{
	display: flex;
	justify-content: center;
	justify-items: center;
	flex-direction: column;
}
</style>
HTML;
function render_payloads(Response $response){
	echo '<section class="block">';

	echo '<pre>';
	$request = (string)$response->getRequest();
	echo htmlspecialchars($request);
	echo '</pre>';

	echo '<hr/>';

	echo '<pre>';
	$response = (string)$response;
	echo htmlspecialchars($response);
	echo '</pre>';

	echo '</section>';
}