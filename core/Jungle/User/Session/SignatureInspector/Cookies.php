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
		protected $https;

		/** @var  string|null */
		protected $hostname;

		/** @var  string|null */
		protected $uri;

		/** @var   */
		protected $lifetime;

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
				$this->lifetime? time() + $this->lifetime : null,
				$this->uri,
				$this->https,
				$this->http_only,
				$this->hostname
			);
			return $this;
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
	}
}

