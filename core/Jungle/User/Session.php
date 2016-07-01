<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.06.2016
 * Time: 0:50
 */
namespace Jungle\User {

	/**
	 * Class Session
	 * @package Jungle\User
	 */
	class Session{

		public function initialize(){

		}

		/**
		 * @param $name
		 * @return null
		 */
		public function __get($name){
			return isset($_SESSION[$name])?$_SESSION[$name]:null;
		}

		/**
		 * @param $name
		 * @param $value
		 */
		public function __set($name, $value){
			$_SESSION[$name] = $value;
		}

		/**
		 * @param $name
		 * @return bool
		 */
		public function __isset($name){
			return isset($_SESSION[$name]);
		}

		/**
		 * @param $name
		 */
		public function __unset($name){
			unset($_SESSION[$name]);
		}

	}
}

