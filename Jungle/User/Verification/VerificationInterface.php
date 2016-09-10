<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.09.2016
 * Time: 13:05
 */
namespace Jungle\User\Verification {

	/**
	 * Interface VerificationInterface
	 * @package Jungle\User\Verification
	 */
	interface VerificationInterface{

		/**
		 * @return mixed
		 */
		public function getCode();

		/**
		 * @return mixed
		 */
		public function getId();

		/**
		 * @param $strategy
		 * @return mixed
		 */
		public function setVerificationStrategy($strategy);

		/**
		 * @return mixed
		 */
		public function getSessionId();

		/**
		 * @return mixed
		 */
		public function satisfy();

		/**
		 * @return bool
		 */
		public function isSatisfied();

		/**
		 * @return bool
		 */
		public function isOverdue();


		/**
		 * @param $expires
		 * @return mixed
		 */
		public function setExpires($expires);



	}
}

