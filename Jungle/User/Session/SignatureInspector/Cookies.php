<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.08.2016
 * Time: 21:19
 */
namespace Jungle\User\Session\SignatureInspector {
	
	use Jungle\User\Session\SignatureInspector;

	/**
	 * Class Cookies
	 * @package Jungle\User\Session\SignatureInspector
	 */
	class Cookies extends SignatureInspector{

		/** @var  string  */
		protected $cookie_name = 'JUNGLE_SESSION_ID';

		/** @var  bool|null */
		protected $http_only;

		/** @var  bool|null */
		protected $secure;

		/** @var  string|null */
		protected $domain;

		/** @var  string|null */
		protected $uri;

		/** @var   */
		protected $lifetime = NAN;

		/**
		 * @param null $default_lifetime
		 * @return null
		 */
		public function getExpires($default_lifetime = null){
			if($this->lifetime === NAN){
				$l = $default_lifetime;
			}else{
				$l = $this->lifetime;
			}
			return $l?time() + $l:null;
		}

		/**
		 * @return mixed
		 */
		public function getSignature(){
			return $this->cookie->getCookie($this->cookie_name);
		}

		/**
		 * @param $signature
		 * @param $lifetime
		 * @return $this
		 */
		public function setSignature($signature, $lifetime = null){
			$this->cookie->setCookie($this->cookie_name,
				$signature,
				$this->getExpires($lifetime),
				$this->uri,
				$this->secure,
				$this->http_only,
				$this->domain
			);
			return $this;
		}

		/**
		 * @return bool
		 */
		public function hasSignal(){
			return !!$this->cookie->getCookie($this->cookie_name);
		}

		/**
		 * @return mixed
		 */
		public function generateSignature(){
			return uniqid('SID_',true);
		}

		/**
		 * @param $string
		 * @return $this
		 */
		public function setCookieName($string){
			$this->cookie_name = $string;
			return $this;
		}

		public function setParam($key, $value){
			$this->{$key} = $value;
			return $this;
		}
	}
}

