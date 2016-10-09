<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.10.2016
 * Time: 0:06
 */
namespace Jungle\Util\Specifications\Hypertext {

	/**
	 * Interface HeaderRegistryInterface
	 * @package Jungle\Util\Specifications\Hypertext
	 */
	interface HeaderRegistryInterface{

		/**
		 * @param $key
		 * @param $value
		 * @param bool|false $reset
		 * @return $this
		 */
		public function setHeader($key, $value, $reset = false);

		/**
		 * @param $key
		 * @param $value
		 * @param bool $reset
		 * @return $this
		 */
		public function mergeHeader($key, array $value, $reset = false);

		/**
		 * @param $key
		 * @param $value
		 * @return $this
		 */
		public function appendHeader($key, $value);

		/**
		 * @param $key
		 * @param $value
		 * @return $this
		 */
		public function prependHeader($key, $value);

		/**
		 * @param $key
		 * @param null $default
		 * @return mixed|null
		 */
		public function getHeader($key, $default = null);

		/**
		 * @param $key
		 * @param $value
		 * @param $inCollection
		 * @return mixed
		 */
		public function checkHeader($key, $value, $inCollection = false);

		/**
		 * @param $key
		 * @param $value
		 * @param $inCollection
		 * @return mixed
		 */
		public function haveHeader($key, $value, $inCollection = false);

		/**
		 * @param $key
		 * @return array
		 */
		public function getHeaderCollection($key);

		/**
		 * @param $key
		 * @return bool
		 */
		public function hasHeader($key);

		/**
		 * @param $key
		 * @return int
		 */
		public function countHeader($key);

		/**
		 * @param $key
		 * @return $this
		 */
		public function removeHeader($key);

		/**
		 * @param $key
		 * @param null $default
		 * @return mixed
		 */
		public function shiftHeader($key, $default = null);

		/**
		 * @param $key
		 * @param null $default
		 * @return mixed
		 */
		public function popHeader($key, $default = null);

		/**
		 * @param $key
		 * @param null $default
		 * @return mixed
		 */
		public function getHeaderFirst($key, $default = null);

		/**
		 * @param $key
		 * @param null $default
		 * @return mixed
		 */
		public function getHeaderLast($key, $default = null);

		/**
		 * @param array $headers
		 * @param bool $merge
		 * @param bool $pairs
		 * @return $this
		 */
		public function setHeaders(array $headers, $merge = false, $pairs = false);

		/**
		 * @return array
		 */
		public function getHeaders();

		/**
		 * @return array
		 */
		public function getHeaderPairs();

	}
}

