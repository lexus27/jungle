<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 13.02.2016
 * Time: 19:30
 */
namespace Jungle\User\Lockable {

	/**
	 * Interface IFreezable
	 * @package Jungle\User\Lockable
	 */
	interface IFreezable{

		/**
		 * @param $delay
		 * @param $reason
		 * @return mixed
		 */
		public function freeze($delay, $reason);

		/**
		 * @return mixed
		 */
		public function unfreeze();

		/**
		 * @return mixed
		 */
		public function isFreezed();

		/**
		 * @return mixed
		 */
		public function getFreezedTime();

		/**
		 * @return mixed
		 */
		public function getFreezedReason();

		/**
		 * @return mixed
		 */
		public function getFreezedDelay();

		/**
		 * @return mixed
		 */
		public function calcUnfreezeTime();

	}
}

