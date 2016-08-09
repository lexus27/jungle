<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.07.2016
 * Time: 0:59
 */
namespace Jungle\User\OldSession {

	use Jungle\User\SessionManagerInterface;

	/**
	 * Interface SignatureProviderInterface
	 * @package Jungle\User
	 */
	interface SignatureProviderInterface{

		/**
		 * @return mixed
		 */
		public function generateSignature();

		/**
		 * @param SessionManagerInterface $sessionManager
		 * @return mixed
		 */
		public function setSessionManager(SessionManagerInterface $sessionManager);

		/**
		 * @return mixed
		 */
		public function getSignature();

		/**
		 * @param $signature
		 * @return $this
		 */
		public function setSignature($signature);

		/**
		 * @return $this
		 */
		public function removeSignature();

	}
}

