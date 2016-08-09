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

	use Jungle\Util\Communication\URL;

	/**
	 * Interface ResponseInterface
	 * @package Jungle\Util\Specifications\Http
	 */
	interface ResponseInterface{

		/**
		 * @return RequestInterface
		 */
		public function getRequest();

		/**
		 * @return ServerInterface
		 */
		public function getServer();

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getHeader($key);

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
		 * @return bool
		 */
		public function isTemporalRedirect();

		/**
		 * @return null|URL|string
		 */
		public function getRedirectUrl();

		/**
		 * @param $code
		 * @return mixed
		 */
		public function setCode($code);

		/**
		 * @return mixed
		 */
		public function getCode();

	}
}

