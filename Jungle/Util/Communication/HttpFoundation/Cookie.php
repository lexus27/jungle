<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.06.2016
 * Time: 21:53
 */
namespace Jungle\Util\Communication\HttpFoundation {

	use Jungle\Util\Smart\Value\IValue;
	use Jungle\Util\Communication\HttpFoundation\Cookie\ManagerInterface;

	/**
	 * Class Cookie
	 * @package Jungle\Util\Communication\HttpFoundation
	 */
	class Cookie implements CookieInterface{

		/** @var  ManagerInterface */
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
		protected $domain;

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
		public function setDomain($hostname = null){
			$this->domain = $hostname;
			return $this;
		}

		/**
		 * @return string|null
		 */
		public function getDomain(){
			if($this->domain === null && $this->cookie_manager){
				return $this->cookie_manager->getDomain();
			}
			return $this->domain;
		}

		/**
		 * @param $domain
		 * @return bool
		 */
		public function checkDomain($domain){
			$domain_allowed = $this->getDomain();
			$domain = array_reverse(explode('.',$domain));
			$domain_allowed = array_reverse(explode('.',$domain_allowed));
			foreach($domain_allowed as $i => $item){
				if(!$item){
					return true;
				}
				if($item && (!isset($domain[$i]) || strcasecmp($item,$domain[$i])!==0)){
					return false;
				}
			}
			return true;
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

		/**
		 * @param IValue|mixed $value
		 * @return bool
		 */
		public function equal($value){
			if($value instanceof CookieInterface){
				return $value->getPath() === $this->getPath() &&
				       $value->getExpires() === $this->getExpires() &&
				       $value->getDomain() === $this->getDomain() &&
				       $value->getName() === $this->getName() &&
				       $value->getValue() === $this->getValue();
			}
			return $this->value === $value;

		}

		/**
		 * @return mixed
		 */
		public function isOverdue(){
			$expires = $this->getExpires();
			if($expires===null)return false;
			return time() >= $this->getExpires();
		}
	}
}

