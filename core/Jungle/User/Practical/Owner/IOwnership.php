<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 13.02.2016
 * Time: 19:27
 */
namespace Jungle\User\Practical\Owner {

	/**
	 * Чье-то владение
	 * Interface IOwnership
	 * @package Jungle\User\Practical\Owner
	 */
	interface IOwnership{

		/**
		 * @param IOwner $owner
		 * @return bool
		 */
		public function isOwnedBy(IOwner $owner);

	}
}

