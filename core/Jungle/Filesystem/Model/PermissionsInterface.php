<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 01.02.2016
 * Time: 23:53
 */
namespace Jungle\FileSystem\Model {

	/**
	 * Interface PermissionsInterface
	 * @package Jungle\FileSystem\Model
	 */
	interface PermissionsInterface{

		const PERMISSION_OWNER_FULL             = 0700;
		const PERMISSION_OWNER_READ             = 0400;
		const PERMISSION_OWNER_WRITE            = 0200;
		const PERMISSION_OWNER_EXECUTE          = 0100;

		const PERMISSION_GROUP_FULL             = 070;
		const PERMISSION_GROUP_READ             = 040;
		const PERMISSION_GROUP_WRITE            = 020;
		const PERMISSION_GROUP_EXECUTE          = 010;

		const PERMISSION_WORLD_FULL             = 07;
		const PERMISSION_WORLD_READ             = 04;
		const PERMISSION_WORLD_WRITE            = 02;
		const PERMISSION_WORLD_EXECUTE          = 01;

		const PERMISSIONS_NO_LIMITED            = 0777;

		const PERMISSIONS_READ_ONLY_FILE        = 0444;
		const PERMISSIONS_READ_ONLY_DIRECTORY   = 0555;




		/** @return bool */
		public function hasOwnerRead();
		/** @return bool */
		public function hasOwnerWrite();
		/** @return bool */
		public function hasOwnerExecute();

		/**
		 * @param bool|true $readable
		 * @return $this
		 */
		public function setOwnerRead($readable = true);

		/**
		 * @param bool|true $writable
		 * @return $this
		 */
		public function setOwnerWrite($writable = true);

		/**
		 * @param bool|true $executable
		 * @return $this
		 */
		public function setOwnerExecute($executable = true);





		/** @return bool */
		public function hasGroupRead();
		/** @return bool */
		public function hasGroupWrite();
		/** @return bool */
		public function hasGroupExecute();

		/**
		 * @param bool|true $readable
		 * @return $this
		 */
		public function setGroupRead($readable = true);

		/**
		 * @param bool|true $writable
		 * @return $this
		 */
		public function setGroupWrite($writable = true);

		/**
		 * @param bool|true $executable
		 * @return $this
		 */
		public function setGroupExecute($executable = true);





		/** @return bool */
		public function hasWorldRead();
		/** @return bool */
		public function hasWorldWrite();
		/** @return bool */
		public function hasWorldExecute();

		/**
		 * @param bool|true $readable
		 * @return $this
		 */
		public function setWorldRead($readable = true);

		/**
		 * @param bool|true $writable
		 * @return $this
		 */
		public function setWorldWrite($writable = true);

		/**
		 * @param bool|true $executable
		 * @return $this
		 */
		public function setWorldExecute($executable = true);


		/**
		 * @param $permissions
		 * @param bool $merge
		 * @return mixed
		 */
		public function setPermissions($permissions, $merge = false);

		/**
		 * @param bool $octal
		 * @return string|int if octal = false
		 */
		public function getPermissions($octal = true);


		/**
		 * @param string|int $permissions
		 * @return bool
		 */
		public function isEqual($permissions);

		/**
		 * @param int|null $permissions
		 * @return array [
		 *      'owner' => ['read' => true, 'write' => true, 'write' => true]
		 *      'group' => ['read' => true, 'write' => true, 'write' => true]
		 *      'world' => ['read' => true, 'write' => true, 'write' => true]
		 * ]
		 *
		 */
		public function toArray($permissions = null);

	}
}

