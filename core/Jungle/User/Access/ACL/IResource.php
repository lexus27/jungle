<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 13.02.2016
 * Time: 23:56
 */
namespace Jungle\User\Access\ACL {

	/**
	 * Interface IResource
	 * @package Jungle\User\Access\ACL
	 */
	interface IResource{

		/**
		 * @return string
		 */
		public function getName();

		/**
		 * @return string
		 */
		public function getDescription();

		/**
		 * @return IResourceAction[]
		 */
		public function getActions();

		/**
		 * @param IResourceAction $action
		 * @return mixed
		 */
		public function addAction(IResourceAction $action);

		/**
		 * @param $name
		 * @return IResourceAction|null
		 */
		public function getAction($name);

		/**
		 * @param IResourceAction $action
		 * @return mixed
		 */
		public function removeAction(IResourceAction $action);
	}
}

