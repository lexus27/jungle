<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.06.2016
 * Time: 13:34
 */
namespace Jungle\Util\Specifications\Http {

	/**
	 * Interface ServerInterface
	 * @package Jungle\Util\Specifications\Http
	 */
	interface ServerInterface{

		/**
		 * @return string
		 */
		public function getIp();

		/**
		 * @return string
		 */
		public function getHost();

		/**
		 * @return int
		 */
		public function getPort();

		/**
		 * @return string
		 */
		public function getGateway();

		/**
		 * @return string
		 */
		public function getSoftware();

		/**
		 * @return string
		 */
		public function getEngine();

		/**
		 * @return string
		 */
		public function getProtocol();

		/**
		 * @return string
		 */
		public function getTimeZone();
	}
}

