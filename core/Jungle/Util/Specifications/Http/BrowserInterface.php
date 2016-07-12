<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.07.2016
 * Time: 14:25
 */
namespace Jungle\Util\Specifications\Http {

	/**
	 * Interface BrowserInterface
	 * @package Jungle\Util\Specifications\Http
	 */
	interface BrowserInterface{


		/**
		 * @return string
		 */
		public function getName();

		/**
		 * @return string
		 */
		public function getVersion();

		/**
		 * @return bool
		 */
		public function isMobile();

		/**
		 * @return string
		 */
		public function getPlatform();


		/**
		 * @return string
		 */
		public function getUserAgent();

	}
}

