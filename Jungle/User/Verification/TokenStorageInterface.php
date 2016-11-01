<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.10.2016
 * Time: 14:17
 */
namespace Jungle\User\Verification {

	/**
	 * Class TokenStorageInterface
	 * @package Jungle\User\ActivationVerification
	 */
	interface TokenStorageInterface{


		/**
		 * @param $user_id
		 * @param $session_id
		 * @param $scope_key
		 * @param $verification_key
		 * @return TokenInterface|null
		 */
		public function loadToken($user_id, $session_id, $scope_key, $verification_key);


		/**
		 * @param $scope
		 * @param $key
		 * @return TokenInterface
		 */
		public function factoryToken($scope, $key);

		/**
		 * @param TokenInterface $token
		 */
		public function createToken(TokenInterface $token);

		/**
		 * @param TokenInterface $token
		 * @return TokenInterface
		 */
		public function saveToken(TokenInterface $token);

		/**
		 * @param TokenInterface $token
		 */
		public function removeToken(TokenInterface $token);


	}
}

