<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 27.10.2016
 * Time: 11:42
 */
namespace Jungle\User\Verification {

	use App\Model\VerificationToken;

	/**
	 * Class MyTokenStorage
	 * @package Jungle\User\Verification
	 */
	class MyTokenStorage implements TokenStorageInterface{


		/**
		 * @param $user_id
		 * @param $session_id
		 * @param $scope_key
		 * @param $verification_key
		 * @return TokenInterface|null
		 */
		public function loadToken($user_id, $session_id, $scope_key, $verification_key){
			$criteria = [
				'scope_key'         => $scope_key,
				'verification_key'  => $verification_key
			];
			if($user_id || $session_id){
				if($user_id){
					$criteria['user_id'] = $user_id;
				}
				if($session_id){
					$criteria['session_id'] = $session_id;
				}
			}
			return VerificationToken::findFirst($criteria);
		}

		/**
		 * @param string $scope_key
		 * @param string $verification_key
		 * @return TokenInterface
		 */
		public function factoryToken($scope_key, $verification_key){
			$token = new VerificationToken();
			$token->setScopeKey($scope_key);
			$token->setVerificationKey($verification_key);
			return $token;
		}

		/**
		 * @param TokenInterface|VerificationToken $token
		 */
		public function createToken(TokenInterface $token){
			$token->save();
		}

		/**
		 * @param TokenInterface|VerificationToken $token
		 * @return TokenInterface
		 */
		public function saveToken(TokenInterface $token){
			$token->save();
		}

		/**
		 * @param TokenInterface|VerificationToken $token
		 */
		public function removeToken(TokenInterface $token){
			$token->delete();
		}

	}
}

