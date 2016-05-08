<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.04.2016
 * Time: 21:19
 */
namespace Jungle\HTTPFoundation {

	use Jungle\Communication\URL;

	/**
	 * Interface RequestInterface
	 * @package Jungle\HTTPFoundation
	 */
	interface RequestInterface{

		/**
		 * @return string
		 */
		public function getMethod();

		/**
		 * @return string
		 */
		public function getScheme();

		/**
		 * @return int
		 */
		public function getPort();

		/**
		 * @return string
		 */
		public function getHostname();

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
		public function getUri();

		/**
		 * @param $parameter
		 * @return mixed
		 */
		public function getParameter($parameter);

		/**
		 * @param $parameter
		 * @return bool
		 */
		public function hasParameter($parameter);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getQueryParameter($key);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasQueryParameter($key);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getPostParameter($key);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasPostParameter($key);

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
		 * @return ClientInterface
		 */
		public function getClient();

		/**
		 * @return ContentInterface
		 */
		public function getContent();

		/**
		 * @return string
		 */
		public function getContentType();

	}
}

