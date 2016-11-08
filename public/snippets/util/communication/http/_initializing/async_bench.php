<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.10.2016
 * Time: 13:04
 */

namespace Jungle\Util\Communication\HttpClient;
include_once __DIR__.'/../../../../loader.php';

/**
 * @param Agent $agent
 * @param int $total
 * @param int $concurrent
 * @param string $url
 * @return string
 */
function test_async(Agent $agent, $total = 500, $concurrent = 10, $url = 'vk.com'){
	$t = microtime(true);
	$parallel = [];

	$segments = $total / $concurrent;


	for($ii=0;$ii<$segments;$ii++){
		for($i = 0;$i < $concurrent; $i++){
			$request = $agent->createGet($url);
			$agent->push($request);
			$parallel[] = $request;
		}
		foreach($parallel as $request){
			$agent->pull($request);
		}
		$parallel = [];
	}
	return sprintf('%.4F',microtime(true) - $t);
}

/**
 * @param Agent $agent
 * @param int $total
 * @param string $url
 * @return string
 */
function test_sync(Agent $agent, $total = 500, $url = 'vk.com'){
	$t = microtime(true);
	for($ii=0;$ii<$total;$ii++){
		$request = $agent->createGet($url);
		$agent->execute($request);
	}
	return sprintf('%.4F',microtime(true) - $t);
}

/**
 * @param Agent $agent
 * @param int $total
 * @param int $start
 * @param int $tests
 * @param float $coefficient
 * @param string $url
 */
function test_concurrent(Agent $agent, $total = 500,$start = 6,$tests = 10, $coefficient = 1.4, $url = 'vk.com'){

	$s = $start + $tests;
	for($i = $start; $i <= $s; $i++){
		$concurrent = floor($i * $coefficient);
		echo '<p>Одновременных запросов: ',$concurrent,' : ',test_async($agent,$total,$concurrent,$url),'</p>';
	}
}
