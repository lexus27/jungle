<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.07.2016
 * Time: 0:48
 */
namespace Jungle\User {

	/**
	 * Interface SessionManagerInterface
	 * @package Jungle\User
	 */
	interface SessionManagerInterface{

		/**
		 * @return SessionInterface
		 */
		public function requireSession();

		/**
		 * @return SessionInterface|null
		 */
		public function readSession();

		public function set($name, $value);
		public function get($name);
		public function has($name);
		public function remove($name);


		public function __set($name, $value);
		public function __get($name);
		public function __isset($name);
		public function __unset($name);


	}
}

