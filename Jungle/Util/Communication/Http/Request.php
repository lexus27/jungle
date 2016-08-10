<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.07.2016
 * Time: 23:45
 */
namespace Jungle\Util\Communication\Http {
	
	use Jungle\User\AccessAuth\Auth;
	use Jungle\Util\Specifications\Http\BrowserInterface;
	use Jungle\Util\Specifications\Http\ClientInterface;
	use Jungle\Util\Specifications\Http\RequestInterface;
	use Jungle\Util\Specifications\Http\RequestSettableInterface;
	use Jungle\Util\Specifications\Http\ResponseInterface;
	use Jungle\Util\Specifications\Http\ServerInterface;

	/**
	 * Class Request
	 * @package Jungle\Util\Communication\Http
	 */
	class Request implements RequestInterface, RequestSettableInterface{

		/** @var  Response */
		protected $response;

		/** @var  BrowserInterface */
		protected $browser;

		/** @var  Server */
		protected $server;

		/** @var  Client */
		protected $client;

		/** @var  string */
		protected $auth_type;

		/** @var  Auth */
		protected $auth;

		/** @var  int|null */
		protected $time;

		/** @var  string */
		protected $method;

		/** @var  array  */
		protected $query_parameters = [];

		/** @var  array  */
		protected $post_parameters = [];

		/** @var  array  */
		protected $headers = [];

		/** @var  array  */
		protected $cookies = [];

		/** @var  string */
		protected $uri;

		/** @var  bool  */
		protected $secure = false;

		/** @var  string|null */
		protected $referrer;

		/** @var  mixed */
		protected $content;

		/** @var array  */
		protected $files = [];


		protected $onSuccess;

		protected $onFailure;


		/**
		 * @return ResponseInterface
		 */
		public function getResponse(){
			return $this->response;
		}

		/**
		 * @return BrowserInterface
		 */
		public function getBrowser(){
			return $this->browser;
		}

		/**
		 * @return ServerInterface
		 */
		public function getServer(){
			return $this->server;
		}

		/**
		 * @return ClientInterface
		 */
		public function getClient(){
			return $this->client;
		}

		/**
		 * @return string
		 */
		public function getAuthType(){
			return $this->auth_type;
		}

		/**
		 * @return Auth|null
		 */
		public function getAuth(){
			return $this->auth;
		}

		/**
		 * @return string
		 */
		public function getUserAgent(){
			return $this->browser->getUserAgent();
		}

		/**
		 * @return int
		 */
		public function getTime(){
			return $this->time;
		}

		/**
		 * @return string
		 */
		public function getMethod(){
			return $this->method;
		}

		/**
		 * @return bool
		 */
		public function isHead(){
			return stripos($this->method,'head')!==false;
		}

		/**
		 * @return bool
		 */
		public function isDelete(){
			return stripos($this->method,'delete')!==false;
		}

		/**
		 * @return bool
		 */
		public function isPatch(){
			return stripos($this->method,'patch')!==false;
		}

		/**
		 * @return bool
		 */
		public function isOptions(){
			return stripos($this->method,'options')!==false;
		}

		/**
		 * @return bool
		 */
		public function isGet(){
			return stripos($this->method,'get')!==false;
		}

		/**
		 * @return bool
		 */
		public function isPost(){
			return stripos($this->method,'post')!==false;
		}

		/**
		 * @return bool
		 */
		public function isPut(){
			return stripos($this->method,'put')!==false;
		}

		/**
		 * @return string
		 */
		public function getUri(){
			return $this->uri;
		}

		/**
		 * @param $parameter
		 * @return mixed
		 */
		public function getParam($parameter){
			return (isset($this->post_parameters[$parameter])?
				$this->post_parameters[$parameter]:(isset($this->query_parameters[$parameter])?
					$this->query_parameters[$parameter]:null));
		}

		/**
		 * @param $parameter
		 * @return bool
		 */
		public function hasParam($parameter){
			return isset($this->post_parameters[$parameter]) || isset($this->query_parameters[$parameter]);
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getQuery($key){
			return isset($this->query_parameters[$key])?$this->query_parameters[$key]:null;
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasQuery($key){
			return isset($this->query_parameters[$key]);
		}



		/**
		 * @param $key
		 * @return mixed
		 */
		public function getPost($key){
			return isset($this->post_parameters[$key])?$this->post_parameters[$key]:null;
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasPost($key){
			return isset($this->post_parameters[$key]);
		}



		/**
		 * @param $key
		 * @return mixed
		 */
		public function getPut($key){
			// TODO: Implement getPut() method.
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasPut($key){
			// TODO: Implement hasPut() method.
		}



		/**
		 * @return string|null
		 */
		public function getReferrer(){
			return $this->getHeader('Referrer');
		}

		/**
		 * @param $headerKey
		 * @return mixed
		 */
		public function getHeader($headerKey){
			return isset($this->headers[$headerKey])?$this->headers[$headerKey]:null;
		}

		/**
		 * @param $headerKey
		 * @return bool
		 */
		public function hasHeader($headerKey){
			return isset($this->headers[$headerKey]);
		}

		/**
		 * @return mixed
		 */
		public function getRequestedWith(){
			return $this->getHeader('Requested-With');
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function getCookie($name){
			return isset($this->cookies[$name])?$this->cookies[$name]:null;
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function hasCookie($name){
			return isset($this->cookies[$name]);
		}

		/**
		 * @return bool
		 */
		public function isAjax(){
			return stripos($this->getHeader('Requested-With'),'XmlHttpRequest')!==false;
		}

		/**
		 * @return string
		 */
		public function getContentType(){
			return $this->getHeader('Content-Type');
		}

		/**
		 * @return mixed
		 */
		public function getContent(){
			return $this->content;
		}

		/**
		 * @return bool
		 */
		public function hasFiles(){
			return !empty($this->files);
		}

		/**
		 * @return array
		 */
		public function getFiles(){
			return $this->files;
		}

		/**
		 * @param BrowserInterface $browser
		 * @return mixed
		 */
		public function setBrowser(BrowserInterface $browser){
			$this->browser = $browser;
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
		 * @param $name
		 * @param $value
		 * @return mixed
		 */
		public function setHeader($name, $value){
			$this->headers[$name] = $value;
			return $this;
		}

		/**
		 * @param $method
		 * @return mixed
		 */
		public function setMethod($method){
			$this->method = $method;
			return $this;
		}

		/**
		 * @param $name
		 * @param $value
		 * @return mixed
		 */
		public function setCookie($name, $value){
			$this->cookies[$name] = $value;
			return $this;
		}

		/**
		 * @param $type
		 * @return mixed
		 */
		public function setContentType($type){
			$this->setHeader('Content-Type',$type);
			return $this;
		}

		/**
		 * @param null $content
		 * @return mixed
		 */
		public function setContent($content = null){
			$this->content = $content;
		}

		/**
		 * @param $name
		 * @param $value
		 * @return mixed
		 */
		public function setPost($name, $value){
			$this->post_parameters[$name] = $value;
			return $this;
		}

		/**
		 * @param $name
		 * @param $value
		 * @return mixed
		 */
		public function setQuery($name, $value){
			$this->query_parameters[$name] = $value;
			return $this;
		}

		/**
		 * @param bool|true $secure
		 * @return $this
		 */
		public function setSecure($secure = true){
			$this->secure = $secure;
			return $this;
		}

		public function isSecure(){
			return $this->secure;
		}

		/**
		 * @param $uri
		 * @return mixed
		 */
		public function setUri($uri){
			$this->uri = $uri;
			return $this;
		}

		/**
		 * @param Auth $auth
		 * @return mixed
		 */
		public function setAuth(Auth $auth){
			$this->auth = $auth;
			return $this;
		}

		/**
		 * @param null $referrer
		 * @return mixed
		 */
		public function setReferrer($referrer = null){
			$this->setHeader('Referrer',$referrer);
			return $this;
		}

		/**
		 * @param $file
		 * @return mixed
		 */
		public function addFile($file){
			$this->files[] = $file;
		}

		/**
		 * @param $requestedWith
		 * @return mixed
		 */
		public function setRequestedWith($requestedWith){
			$this->setHeader('Requested-With',$requestedWith);
			return $this;
		}

	}
}

