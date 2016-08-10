<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.06.2016
 * Time: 22:01
 */
namespace Jungle\Util\Specifications\Http {

	/**
	 * Interface CookieConfigurationInterface
	 * @package Jungle\Util\Specifications\Http
	 */
	interface CookieConfigurationInterface{

		/**
		 * @param null $expires
		 * @return mixed
		 */
		public function setExpires($expires = null);

		/**
		 * @return int
		 */
		public function getExpires();

		/**
		 * @param null $path
		 * @return mixed
		 */
		public function setPath($path = null);

		/**
		 * @return string
		 */
		public function getPath();

		/**
		 * @param $hostname
		 * @return mixed
		 */
		public function setHost($hostname = null);

		/**
		 * @return string|null
		 */
		public function getHost();

		/**
		 * @param null $secure
		 * @return mixed
		 */
		public function setSecure($secure = null);

		/**
		 * @return bool
		 */
		public function isSecure();

		/**
		 * @param null $httpOnly
		 * @return mixed
		 */
		public function setHttpOnly($httpOnly = null);

		/**
		 * @return bool
		 */
		public function isHttpOnly();

	}
}

