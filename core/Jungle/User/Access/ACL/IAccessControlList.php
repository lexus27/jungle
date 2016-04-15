<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 14.02.2016
 * Time: 0:03
 */
namespace Jungle\User\Access\ACL {

	/**
	 * Interface IAccessControlList
	 * @package Jungle\User\Access\ACL
	 */
	interface IAccessControlList{

		/**
		 * @param IRole $role
		 * @param IResource $resource
		 * @param null $actions
		 * @param bool|true $allowed
		 * @return bool
		 */
		public function setAllowed($role,$resource, $actions = null, $allowed = true);

		/**
		 * @param $role
		 * @param $resource
		 * @param $action
		 * @return bool
		 */
		public function isAllowed($role,$resource,$action);

	}
}

