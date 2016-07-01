<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 13.02.2016
 * Time: 19:30
 */
namespace Jungle\User\Lockable {

	/**
	 * Interface ILockable
	 * @package Jungle\User\Lockable
	 */
	interface ILockable{


		/**
		 * @param $delay
		 * @param $reason
		 * @return mixed
		 */
		public function lock($delay, $reason);

		/**
		 * @return mixed
		 */
		public function amnesty();

		/**
		 * @return mixed
		 */
		public function isLocked();

		/**
		 * @return mixed
		 */
		public function getLockedTime();

		/**
		 * @return mixed
		 */
		public function getLockedReason();

		/**
		 * @return mixed
		 */
		public function getLockedDelay();

		/**
		 * @return mixed
		 */
		public function calcAmnestyTime();

	}
}

