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
	 * Class Storage
	 * @package Jungle\User\Session
	 */
	abstract class Storage implements StorageInterface{

		/**
		 * @param UserInterface|mixed $user
		 * @return SessionInterface[]
		 */
		public function getUserSessions($user){
			if($user instanceof UserInterface){
				return $this->_getUserSessionsById($user->getId());
			}else{
				return $this->_getUserSessionsById($user);
			}
		}


		/**
		 * @param UserInterface|mixed $user
		 * @return $this
		 */
		public function closeUserSessions($user){
			if($user instanceof UserInterface){
				$this->_closeUserSessionsById($user->getId());
			}else{
				$this->_closeUserSessionsById($user);
			}
			return $this;
		}

		/**
		 * @param $id
		 * @return void
		 */
		abstract protected function _closeUserSessionsById($id);

		/**
		 * @param $id
		 * @return SessionInterface[]
		 */
		abstract protected function _getUserSessionsById($id);

	}
}

