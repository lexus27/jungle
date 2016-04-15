<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 13.02.2016
 * Time: 23:55
 */
namespace Jungle\User\Access\ACL {

	/**
	 * Interface IRole
	 * @package Jungle\User\Access\ACL
	 */
	interface IRole{

		/**
		 * @return string
		 */
		public function getName();

		/**
		 * @return string
		 */
		public function getDescription();

	}
}

