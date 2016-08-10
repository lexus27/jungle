<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.01.2016
 * Time: 4:03
 */
namespace Jungle\FileSystem\OldModel {

	use Jungle\Basic\INamed;

	/**
	 * Interface IModel
	 * @package Jungle\FileSystem\OldModel
	 */
	interface IModel extends INamed{

		/**
		 * @return string
		 */
		public function __toString();

		/** @return string */
		public function getSourcePath();

		/**
		 * @param Directory $model
		 * @param $appliedInNew
		 * @param $appliedInOld
		 * @return $this
		 */
		public function setParent(Directory $model, $appliedInNew = false, $appliedInOld = false);
		/** @return IModel|null */
		public function getParent();


		/** @return bool */
		public function isExists();
		/** @return bool */
		public function isWritable();
		/** @return bool */
		public function isReadable();
		/** @return bool */
		public function isExecutable();
		/** @return bool */
		public function isLink();
		/** @return bool */
		public function isDeleted();

		/**
		 * @param $newPermission
		 * @param bool|false $recursive
		 */
		public function setPermissions($newPermission, $recursive = false);

		/**
		 * @param null $octal
		 * @return Permissions|int|string
		 */
		public function getPermissions($octal = null);

		/**
		 * @param $newOwner
		 * @return mixed
		 */
		public function setOwner($newOwner);
		/** @return mixed */
		public function getOwner();


		/**
		 * @param $newGroup
		 * @return mixed
		 */
		public function setGroup($newGroup);
		/** @return mixed */
		public function getGroup();



		/**
		 * @param $comparableName
		 * @return bool
		 */
		public function isEqualName($comparableName);


	}
}

