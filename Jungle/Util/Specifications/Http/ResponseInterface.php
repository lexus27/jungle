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

	use Jungle\Util\Specifications\Hypertext\HeaderRegistryReadInterface;

	/**
	 * Interface ResponseInterface
	 * @package Jungle\Util\Specifications\Http
	 */
	interface ResponseInterface extends HeaderRegistryReadInterface{

		/**
		 * @return RequestInterface
		 */
		public function getRequest();

		/**
		 * @return ServerInterface
		 */
		public function getServer();


		/**
		 * @param $code
		 * @return mixed
		 */
		public function setCode($code);

		/**
		 * @return mixed
		 */
		public function getCode();

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getCookie($key);

		/**
		 * @return mixed
		 */
		public function getContent();

		/**
		 * @return mixed
		 */
		public function getContentType();

		/**
		 * @return mixed
		 */
		public function getContentDisposition();

		/**
		 * @return bool
		 */
		public function isRedirect();

		/**
		 * @return null|string
		 */
		public function getRedirectUrl();


		/**
		 * @return mixed
		 */
		public function isContentLocated();

		/**
		 * @return mixed
		 */
		public function getContentLocationUrl();

	}
}

