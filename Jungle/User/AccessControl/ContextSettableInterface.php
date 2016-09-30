<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 26.09.2016
 * Time: 21:30
 */
namespace Jungle\User\AccessControl {

	/**
	 * Interface ContextSettableInterface
	 * @package Jungle\User\AccessControl
	 */
	interface ContextSettableInterface{

		/**
		 * @param array $properties
		 * @param bool|false $merge
		 */
		public function setProperties(array $properties = [ ], $merge = false);

		/**
		 * @param $user
		 * @return $this
		 */
		public function setUser($user);

		/**
		 * @param $action
		 * @return $this
		 */
		public function setAction($action);

		/**
		 * @param $object
		 * @return $this
		 */
		public function setObject($object);

		/**
		 * @param $scope
		 * @return $this
		 */
		public function setScope($scope);

	}
}

