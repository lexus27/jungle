<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.07.2016
 * Time: 1:18
 */
namespace Jungle\User {

	/**
	 * Interface AccessTokenInterface
	 * @package Jungle\User
	 */
	interface AccessTokenInterface{

		/**
		 * @return mixed
		 */
		public function getToken();

		/**
		 * @return mixed
		 */
		public function getUser();

	}
}

