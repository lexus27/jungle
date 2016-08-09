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
	use Jungle\Util\Value\Time;

	/**
	 * Class Session
	 * @package Jungle\User\Session\Provider
	 */
	class Session extends Provider{

		/** @var   */
		protected $signature_lifetime;

		/**
		 * @param $time
		 * @return $this
		 */
		public function setSignatureLifetime($time){
			$this->signature_lifetime = $time;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getSignatureLifetime(){
			return $this->signature_lifetime;
		}

		/**
		 * @param Overdue $exception
		 * @param bool|false $readAccess
		 * @return mixed
		 * @throws Overdue
		 */
		public function catchOverdue(Overdue $exception, $readAccess = false){
			if($readAccess){
				throw $exception;
			}else{
				$session = $exception->getSession();
				$signature = $this->signature_inspector->generateSignature();
				$session = $this->resetSession($session);
				$this->signature_inspector->setSignature($signature, $this->signature_lifetime);
				return $session;
			}
		}

		/**
		 * @param NotFound $exception
		 * @param bool|false $readAccess
		 * @return mixed
		 * @throws NotFound
		 */
		public function catchNotFound(NotFound $exception, $readAccess = false){
			if($readAccess){
				throw $exception;
			}else{
				return $this->initializeSession();
			}
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
		public function onSuccess($signature, SessionInterface $session){
			if(Time::isReached($session->getModifyTime(), $this->signature_lifetime)){
				$this->refreshSession($session);
			}
		}
	}
}

