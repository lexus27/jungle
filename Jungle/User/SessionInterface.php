<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 21.07.2016
 * Time: 0:00
 */
namespace Jungle\User {

	use Jungle\Util\Data\Registry\RegistryInterface;
	use Jungle\Util\Data\Registry\RegistryRemovableInterface;

	/**
	 * Interface SessionInterface
	 * @package Jungle\User
	 */
	interface SessionInterface extends RegistryInterface, RegistryRemovableInterface{

		/**
		 * @param UserInterface $user
		 * @return mixed
		 */
		public function setUser(UserInterface $user = null);

		/**
		 * @return UserInterface|null
		 */
		public function getUser();

		/**
		 * @return bool
		 */
		public function hasUser();

		/**
		 * @param $id
		 * @return mixed
		 */
		public function setSessionId($id);

		/**
		 * @return mixed
		 */
		public function getSessionId();

		/**
		 * @param bool|true $token
		 * @return mixed
		 */
		public function setToken($token = true);

		/**
		 * @return bool
		 */
		public function isToken();

		/**
		 * @param bool|true $permanent
		 * @return mixed
		 */
		public function setPermanent($permanent = true);

		/**
		 * @return bool
		 */
		public function isPermanent();

		/**
		 * @param $permissions
		 * @return mixed
		 */
		public function setPermissions($permissions);

		/**
		 * @return mixed
		 */
		public function getPermissions();


		/**
		 * @param $ip
		 * @return $this
		 */
		public function setRegisteredIp($ip);

		/**
		 * @return string
		 */
		public function getRegisteredIp();

		/**
		 * @param $user_agent
		 * @return $this
		 */
		public function setRegisteredUserAgent($user_agent);

		/**
		 * @return string
		 */
		public function getRegisteredUserAgent();


		/**
		 * @param array $data
		 * @return mixed
		 */
		public function setData(array $data);

		/**
		 * @return mixed
		 */
		public function getData();


		/**
		 * @param $time
		 * @return mixed
		 */
		public function setModifyTime($time);

		/**
		 * @return int
		 */
		public function getModifyTime();


		/**
		 * @param $time
		 * @return mixed
		 */
		public function setCreateTime($time);

		/**
		 * @return int
		 */
		public function getCreateTime();


		/**
		 * @param $name
		 * @return mixed
		 */
		public function get($name);

		/**
		 * @param $name
		 * @param $value
		 * @return mixed
		 */
		public function set($name, $value);

		/**
		 * @param $name
		 * @return mixed
		 */
		public function has($name);

		/**
		 * @param $name
		 * @return mixed
		 */
		public function remove($name);

	}
}

