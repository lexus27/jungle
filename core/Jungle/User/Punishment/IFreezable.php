<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 13.02.2016
 * Time: 19:30
 */
namespace Jungle\User\Lockable {

	interface IFreezable{



		public function freeze($delay, $reason);

		public function unfreeze();



		public function isFreezed();

		public function getFreezedTime();

		public function getFreezedReason();

		public function getFreezedDelay();

		public function calcUnfreezeTime();

	}
}

