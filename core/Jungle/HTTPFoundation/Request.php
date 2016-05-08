<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.05.2016
 * Time: 16:51
 */
namespace Jungle\HTTPFoundation {

	use Jungle\Application\RequestInterface as ApplicationRequestInterface;

	/**
	 * Class Request
	 * @package Jungle\HTTPFoundation
	 */
	class Request implements RequestInterface , ApplicationRequestInterface{

		/** @var  string|null */
		protected $method;

		/** @var  string|null */
		protected $scheme;

		/** @var  int|null */
		protected $port;

		/** @var  string|null */
		protected $hostname;

		/** @var  string|null */
		protected $auth_type;

		/** @var  string|null */
		protected $auth_login;

		/** @var  string|null */
		protected $auth_password;

		/** @var  array*/
		protected $headers = [];

		/** @var array  */
		protected $get = [];

		/** @var array  */
		protected $post = [];

		/** @var  string|null */
		protected $uri;

		/** @var  ClientInterface */
		protected $client;

		/** @var  string|null */
		protected $content;

		/**
		 * @return Request
		 */
		public static function fromCurrent(){
			$request = new Request();
			$request->method 	= $_SERVER['REQUEST_METHOD'];
			$request->uri 		= $_SERVER['REQUEST_URI'];
			$request->get 		= $_GET;
			$request->post 		= $_POST;
			$request->headers 	= getallheaders();
			return $request;
		}

		/**
		 * @return string
		 */
		public function getMethod(){
			return $this->method;
		}

		/**
		 * @return string
		 */
		public function getScheme(){
			return $this->scheme;
		}

		/**
		 * @return int
		 */
		public function getPort(){
			return $this->port;
		}

		/**
		 * @return string
		 */
		public function getHostname(){
			return $this->hostname;
		}

		/**
		 * @return string
		 */
		public function getAuthType(){
			return $this->auth_type;
		}

		/**
		 * @return string|null
		 */
		public function getAuthLogin(){
			return $this->auth_login;
		}

		/**
		 * @return string|null
		 */
		public function getAuthPassword(){
			return $this->auth_password;
		}

		/**
		 * @return string
		 */
		public function getUri(){
			return $this->uri;
		}

		/**
		 * @return string
		 */
		public function getPath(){
			return $this->getUri();
		}

		/**
		 * @param $parameter
		 * @return mixed
		 */
		public function getParameter($parameter){
			if(isset($this->post[$parameter])){
				return $this->post[$parameter];
			}else{
				return isset($this->get[$parameter])?$this->get[$parameter]:null;
			}
		}

		/**
		 * @param $parameter
		 * @return bool
		 */
		public function hasParameter($parameter){
			return isset($this->post[$parameter]) || isset($this->get[$parameter]);
		}

		/**
		 * @return string|null
		 */
		public function getReferrer(){
			return $this->getHeader('HTTP-Referrer');
		}

		/**
		 * @param $headerKey
		 * @return mixed
		 */
		public function getHeader($headerKey){
			return $this->headers[$headerKey];
		}

		/**
		 * @param $headerKey
		 * @return bool
		 */
		public function hasHeader($headerKey){
			return isset($this->headers[$headerKey]);
		}

		/**
		 * @return ClientInterface
		 */
		public function getClient(){
			return $this->client;
		}

		/**
		 * @return ContentInterface
		 */
		public function getContent(){
			return $this->content;
		}

		/**
		 * @return string
		 */
		public function getContentType(){
			return $this->getHeader('Content-Type');
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getQueryParameter($key){
			return isset($this->get[$key])?$this->get[$key]:null;
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasQueryParameter($key){
			return isset($this->get[$key]);
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getPostParameter($key){
			return isset($this->post[$key])?$this->post[$key]:null;
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasPostParameter($key){
			return isset($this->post[$key]);
		}
	}
}

