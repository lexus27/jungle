<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.07.2016
 * Time: 15:59
 */
namespace Jungle\Util\Specifications\Http {

	/**
	 * Interface ResponseSettableInterface
	 * @package Jungle\Util\Specifications\Http
	 */
	interface ResponseSettableInterface{

		/**
		 * @param RequestInterface $request
		 * @return mixed
		 */
		public function setRequest(RequestInterface $request);

		/**
		 * @param ServerInterface $server
		 * @return mixed
		 */
		public function setServer(ServerInterface $server);


		/**
		 * @param array|string $key
		 * @param $value
		 * @param int $expires
		 * @param string $path
		 * @param null $secure
		 * @param null $httpOnly
		 * @param null $domain
		 * @return mixed
		 */
		public function setCookie($key, $value = null, $expires = null, $path = null, $secure = null, $httpOnly = null, $domain = null);

		/**
		 * @param $key
		 * @param $value
		 * @return mixed
		 */
		public function setHeader($key, $value);

		/**
		 * @param $content
		 * @return mixed
		 */
		public function setContent($content);

		/**
		 * @param $type
		 * @return mixed
		 */
		public function setContentType($type);

		/**
		 * @param $disposition
		 * @return mixed
		 */
		public function setContentDisposition($disposition);

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

