<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 13.02.2016
 * Time: 19:27
 */
namespace Jungle\User\Owner {

	/**
	 * Interface IOwner
	 * @package Jungle\User\Owner
	 */
	interface IOwner{

		/**
		 * @param IOwnership $ownership
		 * @return bool
		 */
		public function isBelongs(IOwnership $ownership);

	}
}

