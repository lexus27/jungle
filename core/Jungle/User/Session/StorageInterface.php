<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.08.2016
 * Time: 20:52
 */
namespace Jungle\User\Session {
	
	use Jungle\User\SessionInterface;
	use Jungle\User\UserInterface;

	/**
	 * Interface StorageInterface
	 * @package Jungle\User\Session
	 */
	interface StorageInterface{

		/**
		 * @param UserInterface|mixed $user
		 * @return SessionInterface[]
		 */
		public function getUserSessions($user);

		/**
		 * @param UserInterface|mixed $user
		 * @return $this
		 */
		public function closeUserSessions($user);

		/**
		 * @param $ip
		 * @return SessionInterface[]
		 */
		public function getIpSessions($ip);

		/**
		 * @param $ip
		 * @return $this
		 */
		public function closeIpSessions($ip);

		/**
		 * @param $id
		 * @return $this
		 */
		public function removeSession($id);

		/**
		 * @param $id
		 * @return SessionInterface|null
		 */
		public function getSession($id);

		/**
		 * @param $lifetime
		 * @return void
		 */
		public function clean($lifetime);

		/**
		 * @param SessionInterface $session
		 * @return $this
		 */
		public function save(SessionInterface $session);


		/**
		 * @return SessionInterface
		 */
		public function factorySession();
	}
}

