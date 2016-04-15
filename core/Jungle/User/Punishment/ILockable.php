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



		public function lock($delay, $reason);

		public function amnesty();



		public function isLocked();

		public function getLockedTime();

		public function getLockedReason();

		public function getLockedDelay();

		public function calcAmnestyTime();

	}
}

