<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.07.2016
 * Time: 18:44
 */
namespace Jungle\Util\Communication\Http {
	
	use Jungle\Util\Communication\URL;
	use Jungle\Util\Specifications\Http\Cookie;
	use Jungle\Util\Specifications\Http\RequestInterface;
	use Jungle\Util\Specifications\Http\ResponseInterface;
	use Jungle\Util\Specifications\Http\ResponseSettableInterface;
	use Jungle\Util\Specifications\Http\ServerInterface;

	/**
	 * Class Response
	 * @package Jungle\Util\Communication\Http
	 */
	class Response implements ResponseInterface,ResponseSettableInterface{

		/** @var  Server */
		protected $server;

		/** @var  Request */
		protected $request;

		/** @var  int */
		protected $code;

		/** @var  Cookie[]  */
		protected $cookies = [];

		/** @var  array  */
		protected $headers = [];

		/** @var   */
		protected $content;

		/**
		 * @return RequestInterface
		 */
		public function getRequest(){
			return $this->request;
		}

		/**
		 * @return ServerInterface
		 */
		public function getServer(){
			return $this->server;
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getHeader($key){
			return isset($this->headers[$key])?$this->headers[$key]:null;
		}

		/**
		 * @param $key
		 * @return Cookie
		 */
		public function getCookie($key){
			return isset($this->cookies[$key])?$this->cookies[$key]:null;
		}

		/**
		 * @return mixed
		 */
		public function getContent(){
			return $this->content;
		}

		/**
		 * @return mixed
		 */
		public function getContentType(){
			return $this->getHeader('Content-Type');
		}

		/**
		 * @return mixed
		 */
		public function getContentDisposition(){
			return $this->getHeader('Content-Disposition');
		}

		/**
		 * @return bool
		 */
		public function isRedirect(){
			return isset($this->headers['Location']);
		}

		/**
		 * @return bool
		 */
		public function isTemporalRedirect(){
			return $this->code === 302;
		}

		/**
		 * @return null|URL|string
		 */
		public function getRedirectUrl(){
			return isset($this->headers['Location'])?$this->headers['Location']:null;
		}

		/**
		 * @param RequestInterface $request
		 * @return mixed
		 */
		public function setRequest(RequestInterface $request){
			$this->request = $request;
			return $this;
		}

		/**
		 * @param ServerInterface $server
		 * @return mixed
		 */
		public function setServer(ServerInterface $server){
			$this->server = $server;
			return $this;
		}

		/**
		 * @param $key
		 * @param $value
		 * @param int $expire
		 * @param string $path
		 * @param null $secure
		 * @param null $httpOnly
		 * @param null $host
		 * @return mixed
		 */
		public function setCookie(
			$key, $value, $expire = null, $path = null, $secure = null, $httpOnly = null, $host = null
		){
			// TODO: Implement setCookie() method.
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
		 * @param $content
		 * @return mixed
		 */
		public function setContent($content){
			$this->content = $content;
			return $this;
		}

		/**
		 * @param $type
		 * @return mixed
		 */
		public function setContentType($type){
			// TODO: Implement setContentType() method.
		}

		/**
		 * @param $disposition
		 * @return mixed
		 */
		public function setContentDisposition($disposition){
			// TODO: Implement setContentDisposition() method.
		}

		/**
		 * @return bool
		 */
		public function isSent(){
			// TODO: Implement isSent() method.
		}

		/**
		 * @return void
		 */
		public function send(){
			// TODO: Implement send() method.
		}

		/**
		 * @param $code
		 * @return mixed
		 */
		public function setCode($code){
			// TODO: Implement setCode() method.
		}

		/**
		 * @return mixed
		 */
		public function getCode(){
			// TODO: Implement getCode() method.
		}
	}
}

