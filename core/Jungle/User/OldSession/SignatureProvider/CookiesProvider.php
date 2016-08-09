<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.07.2016
 * Time: 1:02
 */
namespace Jungle\User\OldSession\SignatureProvider {

	use Jungle\User\OldSession\SignatureProvider;

	/**
	 * Class CookiesProvider
	 * @package Jungle\User\OldSession\SignatureProvider
	 */
	class CookiesProvider extends SignatureProvider{

		/** @var  string  */
		protected $cookie_name = 'JUNGLE_SESSION_ID';

		protected $http_only;

		protected $https;

		protected $hostname;

		protected $uri;


		/**
		 * @param $name
		 * @return $this
		 */
		public function setCookieName($name){
			$this->cookie_name = $name;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getCookieName(){
			return $this->cookie_name;
		}

		/**
		 * @return mixed
		 */
		public function getSignature(){
			return $this->cookie->getCookie($this->cookie_name);
		}

		/**
		 * @param $signature
		 * @return $this
		 */
		public function setSignature($signature){
			$lifetime = $this->session_manager->getLifetime();
			$this->cookie->setCookie(
				$this->cookie_name,
				$signature,$lifetime?time() + $lifetime:null,
				$this->uri,
				$this->https,
				$this->http_only,
				$this->hostname
			);
			return $this;
		}

		/**
		 * @return $this
		 */
		public function removeSignature(){
			$this->cookie->removeCookie($this->cookie_name);
			return $this;
		}

	}
}

