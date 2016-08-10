<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.08.2016
 * Time: 14:03
 */
namespace Jungle\User\Session\Provider {
	
	use Jungle\User\Session\Exception\NotFound;
	use Jungle\User\Session\Exception\NotSupplied;
	use Jungle\User\Session\Exception\Overdue;
	use Jungle\User\Session\Provider;
	use Jungle\User\SessionInterface;

	/**
	 * Class Token
	 * @package Jungle\User\Session\Provider
	 * TODO Forbid on logout[setUser(null)] from permanent session
	 */
	class Token extends Provider{

		/**
		 * @return SessionInterface
		 */
		protected function initializeSession(){
			$session = parent::initializeSession();
			$session->setToken(true);
			$session->setPermanent(false);
			return $session;
		}

		/**
		 * @param Overdue $exception
		 * @param bool|false $readAccess
		 * @return mixed
		 * @throws Overdue
		 */
		public function catchOverdue(Overdue $exception, $readAccess = false){
			throw $exception;
		}

		/**
		 * @param NotFound $exception
		 * @param bool|false $readAccess
		 * @return mixed
		 * @throws NotFound
		 */
		public function catchNotFound(NotFound $exception, $readAccess = false){
			throw $exception;
		}

		/**
		 * @param NotSupplied $exception
		 * @param bool|false $readAccess
		 * @return mixed
		 * @throws NotSupplied
		 */
		public function catchNotSupplied(NotSupplied $exception, $readAccess = false){
			if($readAccess){
				throw $exception;
			}else{
				return $this->initializeSession();
			}
		}

		/**
		 * @param $signature
		 * @param SessionInterface $session
		 * @return mixed
		 */
		public function onSuccess($signature, SessionInterface $session){}

	}
}

