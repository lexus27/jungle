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
	 * Interface RequestInterface
	 * @package Jungle\Util\Specifications\Http
	 */
	interface RequestInterface{

		/**
		 * @return ClientInterface
		 */
		public function getClient();

		/**
		 * @return string
		 */
		public function getScheme();

		public function getServerIp();

		/**
		 * @return int
		 */
		public function getServerPort();

		/**
		 * @return string
		 */
		public function getServerHost();

		/**
		 * @return string
		 */
		public function getAuthType();

		/**
		 * @return string|null
		 */
		public function getAuthLogin();

		/**
		 * @return string|null
		 */
		public function getAuthPassword();

		/**
		 * @return string
		 */
		public function getUserAgent();

		/**
		 * @return int
		 */
		public function getTime();

		/**
		 * @return string
		 */
		public function getMethod();

		/**
		 * @return bool
		 */
		public function isHead();

		/**
		 * @return bool
		 */
		public function isDelete();

		/**
		 * @return bool
		 */
		public function isPatch();

		/**
		 * @return bool
		 */
		public function isOptions();

		/**
		 * @return string
		 */
		public function getUri();

		/**
		 * @param $parameter
		 * @return mixed
		 */
		public function getParam($parameter);

		/**
		 * @param $parameter
		 * @return bool
		 */
		public function hasParam($parameter);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getQuery($key);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasQuery($key);

		/**
		 * @return bool
		 */
		public function isGet();

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getPost($key);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasPost($key);

		/**
		 * @return bool
		 */
		public function isPost();

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getPut($key);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasPut($key);

		/**
		 * @return bool
		 */
		public function isPut();

		/**
		 * @return string|null
		 */
		public function getReferrer();

		/**
		 * @param $headerKey
		 * @return mixed
		 */
		public function getHeader($headerKey);

		/**
		 * @param $headerKey
		 * @return bool
		 */
		public function hasHeader($headerKey);

		/**
		 * @return mixed
		 */
		public function getRequestedWith();

		/**
		 * @return bool
		 */
		public function isAjax();

		/**
		 * @return string
		 */
		public function getContentType();

		/**
		 * @return mixed
		 */
		public function getContent();

		/**
		 * @return bool
		 */
		public function hasFiles();

		/**
		 * @return array
		 */
		public function getFiles();

	}
}

