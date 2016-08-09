<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.07.2016
 * Time: 20:53
 */
namespace Jungle\User {

	use Jungle\Di\Injectable;

	/**
	 * Class Account
	 * @package Jungle\User
	 */
	class Account extends Injectable implements AccountInterface{

		/** @var  bool  */
		protected $detected = false;

		/** @var  UserInterface */
		protected $user;

		/** @var  bool  */
		protected $saved = true;

		/**
		 * @return UserInterface|null
		 */
		public function getUser(){
			if(!$this->detected){
				$this->detected = true;
				$this->user = $this->_detectUser();
			}
			return $this->user;
		}

		/**
		 * @param UserInterface $user
		 * @return $this
		 */
		public function setUser(UserInterface $user = null){
			$current = $this->getUser();
			if($current !== $user){
				$this->user = $user;
				$this->_defineUser($user);
				$this->detected = true;
			}
			return $this;
		}


		/**
		 * @return UserInterface|null
		 */
		protected function _detectUser(){
			/** @var SessionManagerInterface $sessionManager */
			$sessionManager = $this->session;
			$sess = $sessionManager->readSession();
			if($sess){
				return $sess->getUser();
			}
			return null;
		}

		/**
		 * @param UserInterface $user
		 */
		protected function _defineUser(UserInterface $user = null){
			/** @var SessionManagerInterface $session */
			$session = $this->session;
			$session->requireSession()->setUser($user);
		}


		/**
		 * @return bool
		 */
		public function isAnonymous(){
			return !$this->getUser();
		}

	}
}

