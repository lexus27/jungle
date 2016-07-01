<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.06.2016
 * Time: 21:23
 */
namespace Jungle\Http\Current {
	
	use Jungle\Util\Specifications\Http\Cookie;
	use Jungle\Util\Specifications\Http\CookieInterface;
	use Jungle\Util\Specifications\Http\ResponseInterface;

	/**
	 * Class Response
	 * @package Jungle\Http\Current
	 */
	class Response implements ResponseInterface{

		/** @var  bool */
		protected $sent = false;

		/** @var  int */
		protected $code;

		/** @var  array */
		protected $headers = [];

		/** @var  CookieInterface[] */
		protected $cookies = [];

		/** @var  mixed */
		protected $content;

		/**
		 * @param null $code
		 * @return $this
		 */
		public function setCode($code = null){
			$this->code = $code;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getCode(){
			return $this->code;
		}

		/**
		 * @param $key
		 * @param $value
		 * @return mixed
		 */
		public function setHeader($key, $value){
			$this->headers[$key] = $value;
			return $this;
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getHeader($key){
			return $this->headers[$key];
		}

		/**
		 * @param null $path
		 * @param int $code
		 * @return $this
		 */
		public function redirect($path = null, $code = 302){
			$this->headers['Location'] = $path;
			$this->setCode($code);
			return $this;
		}

		/**
		 * @param $key
		 * @param $value
		 * @param int $expire
		 * @param string $path
		 * @param null $secure
		 * @param null $httpOnly
		 * @param null $domain
		 * @return CookieInterface
		 */
		public function setCookie($key, $value, $expire = null, $path = null, $secure = null, $httpOnly = null, $domain = null){
			if(isset($this->cookies[$key])){
				$cookie = $this->cookies[$key];
			}else{
				$this->cookies[$key] = $cookie = new Cookie();
				$cookie->setName($key);
			}
			$cookie->setValue($value);
			$cookie->setExpires($expire);
			$cookie->setPath($path);
			$cookie->setSecure($secure);
			$cookie->setHttpOnly($httpOnly);
			$cookie->setHost($domain);
			return $cookie;
		}

		/**
		 * @param $key
		 * @return Cookie
		 */
		public function getCookie($key){
			return $this->cookies[$key];
		}

		/**
		 * @return \Jungle\Util\Specifications\Http\CookieInterface[]
		 */
		public function getCookies(){
			return $this->cookies;
		}

		/**
		 * @param $content
		 * @return mixed
		 */
		public function setContent($content){
			$this->content = $content;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getContent(){
			return $this->content;
		}

		/**
		 * @param $type
		 * @return mixed
		 */
		public function setContentType($type = null){
			$this->headers['Content-Type'] = $type;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getContentType(){
			return isset($this->headers['Content-Type'])?$this->headers['Content-Type']:'text/html';
		}

		/**
		 * @param $disposition
		 * @return mixed
		 */
		public function setContentDisposition($disposition = null){
			$this->headers['Content-Disposition'] = $disposition;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getContentDisposition(){
			return $this->headers['Content-Disposition'];
		}

		/**
		 * @return bool
		 */
		public function isSent(){
			return $this->sent;
		}

		/**
		 * @return void
		 */
		public function send(){
			if(!$this->sent){
				$this->sent = true;
				http_response_code($this->code);
				foreach($this->cookies as $cookie){
					setcookie(
						$cookie->getName(),
						$cookie->getValue(),
						$cookie->getExpires(),
						$cookie->getPath(),
						$cookie->getHost(),
						$cookie->isSecure(),
						$cookie->isHttpOnly()
					);
				}
				foreach($this->headers as $key => $header){
					if($header){
						header($key.': '.$header);
					}
				}
			}

		}
	}
}

