<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 13.02.2016
 * Time: 19:38
 */
namespace Jungle\User\Practical\Owner {

	/**
	 * Interface IAccessOwner
	 * @package Jungle\User\Practical\Owner
	 */
	interface IAccessOwner{

		/**
		 * @param $resource
		 * @param $action
		 * @param $resourceType
		 * @return boolean
		 */
		public function hasPermission($resource,$action,$resourceType);

		/**
		 * @param $controller
		 * @param $action
		 * @return boolean
		 */
		public function hasControllerPermission($controller,$action);

		/**
		 * @param $class_name
		 * @param $action
		 * @return boolean
		 */
		public function hasObjectPermission($class_name,$action);

	}
}

