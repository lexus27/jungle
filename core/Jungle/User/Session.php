<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.07.2016
 * Time: 1:38
 */
namespace Jungle\User {

	/**
	 * Class Session
	 * @package Jungle\User
	 */
	class Session implements SessionInterface{

		/** @var  string */
		protected $id;

		/** @var  UserInterface */
		protected $user;

		/** @var  string */
		protected $registered_ip;

		/** @var  string */
		protected $registered_user_agent;

		/** @var  array */
		protected $data;

		/** @var  int */
		protected $modify_time;

		/** @var  int */
		protected $create_time;

		/** @var bool  */
		protected $token = false;

		/** @var array|null */
		protected $permissions = null;

		/** @var bool */
		protected $permanent = false;

		/**
		 * @param $permanent
		 * @return $this
		 */
		public function setPermanent($permanent = true){
			$this->permanent = $permanent;
			return $this;
		}

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
		 * @param $user
		 * @return $this
		 */
		public function setUser(UserInterface $user = null){
			$this->user = $user;
			return $this;
		}

		/**
		 * @return UserInterface|null
		 */
		public function getUser(){
			return $this->user;
		}

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
		 * @return bool
		 */
		public function hasUser(){
			return !!$this->user;
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

