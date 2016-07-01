<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.06.2016
 * Time: 21:53
 */
namespace Jungle\Util\Specifications\Http {

	/**
	 * Class Cookie
	 * @package Jungle\Util\Specifications\Http
	 */
	class Cookie implements CookieInterface{

		/** @var  CookieManagerInterface */
		protected $cookie_manager;

		/** @var  string */
		protected $name;

		/** @var  string */
		protected $value;

		/** @var  bool */
		protected $secure;

		/** @var  bool */
		protected $httpOnly;

		/** @var  int */
		protected $expires;

		/** @var  string */
		protected $host;

		/** @var  string */
		protected $path;

		/**
		 * @param $name
		 * @return mixed
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}

		/**
		 * @param $value
		 * @return mixed
		 */
		public function setValue($value){
			$this->value = $value;
			return $this;
		}


		/**
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @return mixed
		 */
		public function getValue(){
			return $this->value;
		}

		/**
		 * @param null $expires
		 * @return mixed
		 */
		public function setExpires($expires = null){
			$this->expires = $expires;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getExpires(){
			if($this->expires===null && $this->cookie_manager){
				return $this->cookie_manager->getExpires();
			}
			return $this->expires;
		}

		/**
		 * @param null $path
		 * @return mixed
		 */
		public function setPath($path = null){
			$this->path = $path;
			return $this;
		}
		/**
		 * @return string
		 */
		public function getPath(){
			if($this->path===null && $this->cookie_manager){
				return $this->cookie_manager->getPath();
			}
			return $this->path;
		}

		/**
		 * @param null $hostname
		 * @return mixed
		 */
		public function setHost($hostname = null){
			$this->host = $hostname;
			return $this;
		}
		/**
		 * @return string|null
		 */
		public function getHost(){
			if($this->host === null && $this->cookie_manager){
				return $this->cookie_manager->getHost();
			}
			return $this->host;
		}

		/**
		 * @param null $secure
		 * @return mixed
		 */
		public function setSecure($secure = null){
			$this->secure = $secure;
			return $this;
		}
		/**
		 * @return bool
		 */
		public function isSecure(){
			if($this->secure===null && $this->cookie_manager){
				return $this->cookie_manager->isSecure();
			}
			return $this->secure;
		}

		/**
		 * @param null $httpOnly
		 * @return mixed
		 */
		public function setHttpOnly($httpOnly = null){
			$this->httpOnly = $httpOnly;
			return $this;
		}
		/**
		 * @return bool
		 */
		public function isHttpOnly(){
			if($this->httpOnly===null && $this->cookie_manager){
				return $this->cookie_manager->isHttpOnly();
			}
			return $this->httpOnly;
		}
	}
}

