<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 11.10.2016
 * Time: 19:31
 */
namespace Jungle\Util\Specifications\Hypertext {
	
	interface HeaderRegistryWriteInterface{

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
		 * @param array $headers
		 * @param bool $merge
		 * @param bool $pairs
		 * @return $this
		 */
		public function setHeaders(array $headers, $merge = false, $pairs = false);

	}
}

