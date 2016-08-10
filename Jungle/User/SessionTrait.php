<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.08.2016
 * Time: 19:52
 */
namespace Jungle\User {

	use Jungle\Exception\ForbiddenException;

	/**
	 * Class SessionTrait
	 * @package Jungle\User
	 */
	trait SessionTrait{

		/** @var  string */
		protected $id;

		/** @var  array */
		protected $data = [];

		/** @var  int */
		protected $create_time;

		/** @var  int */
		protected $modify_time;

		/** @var  string */
		protected $registered_ip;

		/** @var  string */
		protected $registered_user_agent;

		/** @var  bool  */
		protected $token = false;

		/** @var  bool  */
		protected $permanent = false;

		/** @var  array|null */
		protected $permissions;

		/**
		 * @param $permanent
		 * @return $this
		 */
		public function setPermanent($permanent = true){
			$this->permanent = $permanent;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isPermanent(){
			return $this->permanent;
		}

		/**
		 * @return bool
		 */
		public function isToken(){
			return $this->token;
		}

		/**
		 * @param bool|true $token
		 * @return $this
		 */
		public function setToken($token = true){
			$this->token = $token;
			return $this;
		}

		/**
		 * @param UserInterface $user
		 * @return $this
		 * @throws ForbiddenException
		 */
		abstract public function setUser(UserInterface $user = null);

		/**
		 * @return UserInterface|null
		 */
		abstract public function getUser();


		/**
		 * @return bool
		 */
		abstract public function hasUser();

		/**
		 * @param $id
		 * @return $this
		 */
		public function setSessionId($id){
			$this->id = $id;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getSessionId(){
			return $this->id;
		}

		/**
		 * @return string
		 */
		public function getRegisteredIp(){
			return $this->registered_ip;
		}

		/**
		 * @return string
		 */
		public function getRegisteredUserAgent(){
			return $this->registered_user_agent;
		}

		/**
		 * @return mixed
		 */
		public function getData(){
			return $this->data;
		}

		/**
		 * @return int
		 */
		public function getModifyTime(){
			return $this->modify_time;
		}

		/**
		 * @return int
		 */
		public function getCreateTime(){
			return $this->create_time;
		}

		/**
		 * @param array $data
		 * @return mixed
		 */
		public function setData(array $data){
			$this->data = $data;
			return $this;
		}

		/**
		 * @param $time
		 * @return mixed
		 */
		public function setModifyTime($time){
			$this->modify_time = $time;
			return $this;
		}

		/**
		 * @param $time
		 * @return mixed
		 */
		public function setCreateTime($time){
			$this->create_time = $time;
			return $this;
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function get($name){
			return isset($this->data[$name])?$this->data[$name]:null;
		}

		/**
		 * @param $name
		 * @param $value
		 * @return mixed
		 */
		public function set($name, $value){
			$this->data[$name] = $value;
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function has($name){
			return isset($this->data[$name]);
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function remove($name){
			unset($this->data[$name]);
		}

		/**
		 * @param $ip
		 * @return $this
		 */
		public function setRegisteredIp($ip){
			$this->registered_ip = $ip;
			return $this;
		}

		/**
		 * @param $user_agent
		 * @return $this
		 */
		public function setRegisteredUserAgent($user_agent){
			$this->registered_user_agent = $user_agent;
		}

		/**
		 * @param $permissions
		 * @return mixed
		 */
		public function setPermissions($permissions = null){
			$this->permissions = $permissions;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getPermissions(){
			return $this->permissions;
		}

	}
}

