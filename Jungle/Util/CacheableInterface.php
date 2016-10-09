<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.07.2016
 * Time: 2:35
 */
namespace Jungle\Util {

	/**
	 * Interface CacheableInterface
	 * @package Jungle\Util
	 */
	interface CacheableInterface{

		/**
		 * Cache this object clear.
		 * @return $this
		 */
		public function cacheClear();

		/**
		 * Enables cache in this object context.
		 * @param bool $cacheable
		 * @return $this
		 */
		public function setCacheable($cacheable = true);

		/**
		 * @return bool
		 */
		public function isCacheable();

	}
}

