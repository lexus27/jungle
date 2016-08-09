<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.08.2016
 * Time: 16:49
 */
namespace Jungle\User\Session {
	
	use Jungle\User\SessionInterface;

	/**
	 * Class Exception
	 * @package Jungle\User\Session
	 */
	class Exception extends \Jungle\User\Exception{

		/** @var  string|null */
		protected $signature;

		/** @var  SessionInterface|null */
		protected $session;

		/**
		 * Exception constructor.
		 * @param string $signature
		 * @param SessionInterface|null $session
		 * @param string $message
		 */
		public function __construct($signature, SessionInterface $session = null, $message = ''){
			$this->signature = $signature;
			$this->session = $session;
			parent::__construct($message);
		}

		/**
		 * @return SessionInterface|null
		 */
		public function getSession(){
			return $this->session;
		}

		/**
		 * @return string|null
		 */
		public function getSignature(){
			return $this->signature;
		}

	}
}

