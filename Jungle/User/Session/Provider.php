<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.08.2016
 * Time: 14:02
 */
namespace Jungle\User\Session {

	use Jungle\Di\Injectable;
	use Jungle\User\Session\Exception\NotFound;
	use Jungle\User\Session\Exception\NotSupplied;
	use Jungle\User\Session\Exception\Overdue;
	use Jungle\User\SessionInterface;
	use Jungle\Util\Value\Time;

	/**
	 * Class Provider
	 * @package Jungle\User\Session
	 */
	abstract class Provider extends Injectable implements ProviderInterface{

		/** @var  SignatureInspectorInterface */
		protected $signature_inspector;

		/** @var  int */
		protected $lifetime;

		/** @var  StorageInterface */
		protected $storage;

		/**
		 * @param SignatureInspectorInterface $inspector
		 * @return $this
		 */
		public function setSignatureInspector(SignatureInspectorInterface $inspector){
			if($inspector instanceof Injectable)$inspector->setDi($this->_dependency_injector);
			$this->signature_inspector = $inspector;
			return $this;
		}

		/**
		 * @return SignatureInspectorInterface
		 */
		public function getSignatureInspector(){
			return $this->signature_inspector;
		}

		/**
		 * @return boolean
		 */
		public function hasSignal(){
			return $this->signature_inspector->hasSignal();
		}

		/**
		 * @param $signature
		 * @return mixed
		 */
		public function storeSignature($signature){
			return md5($signature);
		}

		/**
		 * @return mixed
		 */
		public function getLifetime(){
			return $this->lifetime;
		}



		/**
		 * @return SessionInterface
		 * @throws NotFound
		 * @throws NotSupplied
		 * @throws Overdue
		 */
		protected function _treatment(){
			if(!($signature = $this->signature_inspector->getSignature())){
				throw new NotSupplied(null, null);
			}
			$storeSignature = $this->storeSignature($signature);
			if(!($session = $this->storage->getSession($storeSignature))){
				throw new NotFound($signature, null);
			}
			if(!$session->isPermanent() && Time::isOverdue($session->getModifyTime(), $this->getSessionLifetime())){
				throw new Overdue($signature, $session);
			}
			return [$signature, $session];
		}

		/**
		 * @param StorageInterface $storage
		 * @param bool $readAccess
		 * @return SessionInterface|mixed
		 * @throws \Exception
		 */
		public function requireSession(StorageInterface $storage, $readAccess = false){
			$this->storage = $storage;
			try{
				list($signature, $session) = $this->_treatment();
				if($session){
					$this->onSuccess($signature, $session);
				}
				return $session;
			}catch(NotSupplied $e){
				return $this->catchNotSupplied($e,$readAccess);
			}catch(NotFound $e){
				return $this->catchNotFound($e,$readAccess);
			}catch(Overdue $e){
				return $this->catchOverdue($e,$readAccess);
			}catch(\Exception $e){
				throw $e;
			}finally{
				$this->storage = null;
			}
		}

		/**
		 * @return int
		 */
		public function getSessionLifetime(){
			return $this->lifetime;
		}

		/**
		 * @return int
		 */
		public function getRefreshLifetime(){
			return $this->lifetime;
		}

		/**
		 * @return SessionInterface
		 */
		protected function initializeSession(){
			$signature = $this->signature_inspector->generateSignature();
			$session = $this->storage->factorySession();
			$session->setSessionId($this->storeSignature($signature));
			$session->setCreateTime(time());
			$this->signature_inspector->setSignature($signature, $this->getRefreshLifetime());
			return $session;
		}

		/**
		 * @param SessionInterface $session
		 * @return SessionInterface
		 */
		protected function resetSession(SessionInterface $session){
			$signature = $this->signature_inspector->generateSignature();
			$session->setSessionId($this->storeSignature($signature));
			$session->setData([]);
			$session->setUser(null);
			$session->setPermissions(null);
			$this->signature_inspector->setSignature($signature, $this->lifetime);
			return $session;
		}

		/**
		 * @param SessionInterface $session
		 * @return SessionInterface
		 */
		protected function refreshSession(SessionInterface $session){
			$signature = $this->signature_inspector->generateSignature();
			$session->setSessionId($this->storeSignature($signature));
			$this->signature_inspector->setSignature($signature, $this->lifetime);
			return $session;
		}



	}
}

