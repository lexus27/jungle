<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.10.2016
 * Time: 14:26
 */
namespace Jungle\User\Verification {

	/**
	 * Interface TokenInterface
	 * @package Jungle\User\ActivationVerification
	 */
	interface TokenInterface{

		/**
		 * @param $verification_key
		 * @return mixed
		 */
		public function setVerificationKey($verification_key);

		/**
		 * @return string
		 */
		public function getVerificationKey();


		/**
		 * @param $scope_key
		 * @return mixed
		 */
		public function setScopeKey($scope_key);

		/**
		 * @return string
		 */
		public function getScopeKey();




		/**
		 * @param $user_id
		 * @return $this
		 */
		public function setUserId($user_id);

		/**
		 * @return mixed
		 */
		public function getUserId();

		/**
		 * @param $session_id
		 * @return $this
		 */
		public function setSessionId($session_id);

		/**
		 * @return mixed
		 */
		public function getSessionId();


		/**
		 * @param null $time
		 * @return $this
		 */
		public function setInvokeTime($time = null);

		/**
		 * @return mixed
		 */
		public function getInvokeTime();


		/**
		 * @param bool $satisfied
		 * @param null $time
		 * @return $this
		 */
		public function setSatisfied($satisfied = true, $time = null);

		/**
		 * @return mixed
		 */
		public function getSatisfyTime();

		/**
		 * @return bool
		 */
		public function isSatisfied();



		/**
		 * @param $code
		 * @return mixed
		 */
		public function setCode($code);

		/**
		 * @return string
		 */
		public function getCode();

		/**
		 * @param array $data
		 * @return mixed
		 */
		public function setData(array $data);

		/**
		 * @return array
		 */
		public function getData();



	}
}

