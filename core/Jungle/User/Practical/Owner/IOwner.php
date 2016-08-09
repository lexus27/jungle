<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 13.02.2016
 * Time: 19:27
 */
namespace Jungle\User\Practical\Owner {

	/**
	 * Interface IOwner
	 * @package Jungle\User\Practical\Owner
	 */
	interface IOwner{

		/**
		 * @param IOwnership $ownership
		 * @return bool
		 */
		public function isBelongs(IOwnership $ownership);

	}
}

