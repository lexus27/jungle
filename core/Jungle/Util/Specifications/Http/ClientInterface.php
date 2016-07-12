<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.07.2016
 * Time: 16:53
 */
namespace Jungle\Util\Specifications\Http {

	/**
	 * Interface ClientInterface
	 * @package Jungle\Util\Specifications\Http
	 */
	interface ClientInterface{

		/**
		 * @return string
		 */
		public function getIp();

		/**
		 * @return int
		 */
		public function getPort();

		/**
		 * @return string
		 */
		public function getHost();

		/**
		 * @return string
		 */
		public function getBestLanguage();

		/**
		 * @return string[]
		 */
		public function getLanguages();

		/**
		 * @return bool
		 */
		public function isProxied();

		/**
		 * @return ProxyInterface
		 */
		public function getProxy();

	}
}

