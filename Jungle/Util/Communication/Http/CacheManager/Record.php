<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 12.10.2016
 * Time: 22:59
 */
namespace Jungle\Util\Communication\Http\CacheManager {

	/**
	 * Class Record
	 * @package Jungle\Util\Communication\Http\CacheManager
	 */
	class Record{

		public $time;

		public $max_age;

		public $expired;

		public $tag;

		public $last_modified;

		public $must_revalidate = false;

		public $no_cache = true;

		public $private = false;

		public $response;

	}
}

