<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 04.08.2016
 * Time: 13:46
 */
namespace Jungle\User {

	/**
	 * Interface AccountInterface
	 * @package Jungle\User
	 */
	interface AccountInterface{

		/**
		 * @return mixed
		 */
		public function getUserId();

		/**
		 * @return UserInterface|null
		 */
		public function getUser();

		/**
		 * @param UserInterface $user
		 * @return $this
		 */
		public function setUser(UserInterface $user);


	}
}

