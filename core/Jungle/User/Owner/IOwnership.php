<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 13.02.2016
 * Time: 19:27
 */
namespace Jungle\User\Owner {

	/**
	 * Чье-то владение
	 * Interface IOwnership
	 * @package Jungle\User\Owner
	 */
	interface IOwnership{

		/**
		 * @param IOwner $owner
		 * @return bool
		 */
		public function isOwnedBy(IOwner $owner);

	}
}

