<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.10.2016
 * Time: 13:17
 */
namespace Jungle\Util\Communication\HttpClient;

/** @var Agent $agent */
include_once __DIR__.'/_initializing/agent.php';
include_once __DIR__.'/_initializing/async_bench.php';

echo test_async($agent,500,20,'vk.com');