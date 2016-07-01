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
	 * Interface ResponseInterface
	 * @package Jungle\Util\Specifications\Http
	 */
	interface ResponseInterface{

		/**
		 * @param $key
		 * @param $value
		 * @return mixed
		 */
		public function setHeader($key, $value);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getHeader($key);

		/**
		 * @param $key
		 * @param $value
		 * @param int $expire
		 * @param string $path
		 * @param null $secure
		 * @param null $httpOnly
		 * @param null $domain
		 * @return mixed
		 */
		public function setCookie($key, $value, $expire = null, $path = null, $secure = null, $httpOnly = null, $domain = null);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getCookie($key);

		/**
		 * @param $content
		 * @return mixed
		 */
		public function setContent($content);

		/**
		 * @return mixed
		 */
		public function getContent();

		/**
		 * @param $type
		 * @return mixed
		 */
		public function setContentType($type);

		/**
		 * @return mixed
		 */
		public function getContentType();

		/**
		 * @param $disposition
		 * @return mixed
		 */
		public function setContentDisposition($disposition);

		/**
		 * @return mixed
		 */
		public function getContentDisposition();

		/**
		 * @return bool
		 */
		public function isSent();

		/**
		 * @return void
		 */
		public function send();

	}
}

