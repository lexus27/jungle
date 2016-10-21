<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.10.2016
 * Time: 13:18
 */

namespace Jungle\Util\Communication\HttpClient;

/** @var Agent $agent */
include __DIR__.'/_initializing/agent.php';
include __DIR__.'/_initializing/async_bench.php';

/**
 * Подбор оптимального количества одновременных запросов для скорости
 * У меня тест показывает что при 20 паралельных запросов достигается оптимальный вариант времени
 * 1.8 сек на 100 запросов по 20 штук за раз
 */
test_concurrent($agent,100, 10,15,1.6);