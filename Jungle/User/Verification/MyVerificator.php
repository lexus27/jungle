<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 27.10.2016
 * Time: 11:41
 */
namespace Jungle\User\Verification {

	use App\Model\VerificationToken;
	use Jungle\Di\DiLocatorInterface;
	use Jungle\User\Account;
	use Jungle\User\SessionManager;

	/**
	 * Class MyVerificator
	 * @package Jungle\User\Verification
	 */
	class MyVerificator extends Verificator{


		/**
		 * MyVerificator constructor.
		 * @param DiLocatorInterface $di
		 */
		public function __construct(DiLocatorInterface $di){
			$this->di = $di;
		}

		/**
		 * @param string $scope_key
		 * @param string $verification_key
		 * @return TokenInterface
		 */
		public function createToken($scope_key, $verification_key){
			$token = new VerificationToken();
			$token->setScopeKey($scope_key);
			$token->setVerificationKey($verification_key);
			return $token;
		}

		/**
		 * @return mixed
		 */
		public function getUserId(){
			/** @var Account $a */
			$a = $this->di->getShared('account');
			return $a->getUserId();
		}

		/**
		 * @return mixed
		 */
		public function getSessionId(){
			/** @var SessionManager $s */
			$s = $this->di->getShared('session');
			return $s->requireSession()->getSessionId();
		}

	}
}

