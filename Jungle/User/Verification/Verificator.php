<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.10.2016
 * Time: 13:57
 */
namespace Jungle\User\Verification {

	/**
	 * Class Verificator
	 * @package Jungle\User\ActivationVerification
	 */
	abstract class Verificator{

		/** @var VerificationInterface[]  */
		protected $verifications = [];

		/** @var  TokenStorageInterface */
		protected $token_storage;

		/**
		 * @param $key
		 * @param VerificationInterface $verification
		 * @return $this
		 */
		public function setVerification($key, VerificationInterface $verification){
			$this->verifications[$key] = $verification;
			return $this;
		}

		/**
		 * @param $key
		 * @return Verification
		 */
		public function getVerification($key){
			return isset($this->verifications[$key])?$this->verifications[$key]:null;
		}


		/**
		 * @param TokenStorageInterface $storage
		 * @return $this
		 */
		public function setTokenStorage(TokenStorageInterface $storage){
			$this->token_storage = $storage;
			return $this;
		}

		/**
		 * @return TokenStorageInterface
		 */
		public function getTokenStorage(){
			return $this->token_storage;
		}


		/**
		 * @param $verification_key
		 * @param $scope_key
		 * @return TokenInterface|true
		 */
		public function verify($verification_key, $scope_key){
			$verification = $this->getVerification($verification_key);
			if($verification){
				return $verification->verify($scope_key, $this->token_storage, $this);
			}
			return true;
		}

		/**
		 * @param $verification_key
		 * @param $scope_key
		 */
		public function clean($verification_key, $scope_key){
			$verification = $this->getVerification($verification_key);
			if($verification){
				$verification->clean($scope_key, $this->token_storage, $this);
			}
		}

		/**
		 * @param $verification_key
		 * @param $scope_key
		 * @param array $params
		 * @return bool
		 */
		public function satisfy($verification_key, $scope_key,array $params){
			$verification = $this->getVerification($verification_key);
			if($verification){
				return $verification->satisfy($scope_key, $this->token_storage, $this);
			}
			return true;
		}

		/**
		 * @param string $scope_key
		 * @param string $verification_key
		 * @return TokenInterface
		 */
		abstract public function createToken($scope_key, $verification_key);

		/**
		 * @return mixed
		 */
		abstract public function getUserId();

		/**
		 * @return mixed
		 */
		abstract public function getSessionId();

	}
}

