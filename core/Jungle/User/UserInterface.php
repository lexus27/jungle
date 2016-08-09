<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.06.2016
 * Time: 0:49
 */
namespace Jungle\User {

	/**
	 * Interface UserInterface
	 * @package Jungle\User
	 */
	interface UserInterface{

		/**
		 * @return mixed
		 */
		public function getId();

		/**
		 * @return mixed
		 */
		public function getUsername();

	}
}

