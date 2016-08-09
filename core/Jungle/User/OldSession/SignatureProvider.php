<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.07.2016
 * Time: 2:37
 */
namespace Jungle\User\OldSession {
	
	use Jungle\Di\DiInterface;
	use Jungle\Di\Injectable;
	use Jungle\User\SessionManagerInterface;

	/**
	 * Class SignatureProvider
	 * @package Jungle\User\OldSession
	 */
	abstract class SignatureProvider extends Injectable implements SignatureProviderInterface{

		/** @var  SessionManagerInterface */
		protected $session_manager;

		protected static $_dependency_injector_cacheable = true;

		/**
		 * SignatureProvider constructor.
		 * @param DiInterface $di
		 */
		public function __construct(DiInterface $di){
			$this->setDi($di);
		}

		/**
		 * @return mixed
		 */
		public function generateSignature(){
			return password_hash(uniqid('SSID_',true),PASSWORD_DEFAULT);
		}


		/**
		 * @param SessionManagerInterface $sessionManager
		 * @return $this
		 */
		public function setSessionManager(SessionManagerInterface $sessionManager){
			$this->session_manager = $sessionManager;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getSessionManager(){
			return $this->session_manager;
		}

	}
}

