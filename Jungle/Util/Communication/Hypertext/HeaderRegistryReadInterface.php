<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 11.10.2016
 * Time: 19:30
 */
namespace Jungle\Util\Communication\Hypertext {
	
	interface HeaderRegistryReadInterface{

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
		 * @return array
		 */
		public function getHeaders();

		/**
		 * @return array
		 */
		public function getHeaderPairs();


	}
}

