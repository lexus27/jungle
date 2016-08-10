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
		 * @return bool
		 */
		public function cacheIsEnabled();

		/**
		 * Enables cache in this object context.
		 * @return $this
		 */
		public function cacheOn();

		/**
		 * Disables cache in this object context.
		 * @return $this
		 */
		public function cacheOff();

	}
}

