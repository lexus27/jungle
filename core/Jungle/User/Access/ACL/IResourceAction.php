<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 13.02.2016
 * Time: 23:56
 */
namespace Jungle\User\Access\ACL {

	/**
	 * Interface IResourceAction
	 * @package Jungle\User\Access\ACL
	 */
	interface IResourceAction{

		/**
		 * @return string
		 */
		public function getName();

	}
}

