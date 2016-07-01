<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.06.2016
 * Time: 18:36
 */
namespace Jungle\Http\Current {
	
	use Jungle\Application\RequestInterface;
	use Jungle\Http\Client;
	use Jungle\Util\Specifications\Http\ClientInterface;


	/**
	 * Class Request
	 * @package Jungle\Http\Request
	 */
	class Request implements \Jungle\Util\Specifications\Http\RequestInterface, RequestInterface{

		const AUTH_DIGEST = 'digest';
		const AUTH_BASE = 'base';

		/** @var  Request */
		protected static $instance;

		/** @var  array */
		protected $headers = [];

		/** @var  ClientInterface */
		protected $client;

		/**
		 * @return Request
		 */
		public static function getInstance(){
			if(!self::$instance){
				self::$instance = new Request();
			}
			return self::$instance;
		}

		/**
		 * Request constructor.
		 */
		public function __construct(){
			$this->headers = getallheaders();
		}

		/**
		 * @param $name
		 * @return null
		 */
		public function getCookie($name){
			return isset($_COOKIE[$name])?$_COOKIE[$name]:null;
		}

		/**
		 * @param $name
		 * @return bool
		 */
		public function hasCookie($name){
			return isset($_COOKIE[$name]);
		}

		/**
		 * @return mixed
		 */
		public function getUserAgent(){
			return $_SERVER['HTTP_USER_AGENT'];
		}

		/**
		 * @return string
		 */
		public function getClientIp(){
			return $_SERVER['REMOTE_ADDR'];
		}

		/**
		 * @return string
		 */
		public function getClientHost(){
			return $_SERVER['REMOTE_HOST'];
		}

		/**
		 * @return string
		 */
		public function getClientPort(){
			return $_SERVER['REMOTE_PORT'];
		}

		/**
		 * @return ClientInterface
		 */
		public function getClient(){
			if(!$this->client){
				$this->client = new Client();
			}
			return $this->client;
		}

		/**
		 * @return int
		 */
		public function getTime(){
			return $_SERVER['REQUEST_TIME'];
		}

		/**
		 * @return string
		 */
		public function getMethod(){
			return $_SERVER['REQUEST_METHOD'];
		}

		/**
		 * @return string
		 */
		public function getScheme(){
			return $_SERVER['REQUEST_SCHEME'];
		}


		/**
		 * @return string
		 */
		public function getAuthType(){
			return $_SERVER['PHP_AUTH_DIGEST']?self::AUTH_DIGEST:self::AUTH_BASE;
		}

		/**
		 * @return string|null
		 */
		public function getAuthLogin(){
			return $_SERVER['PHP_AUTH_USER']?:null;
		}

		/**
		 * @return string|null
		 */
		public function getAuthPassword(){
			return $_SERVER['PHP_AUTH_PW']?:null;
		}

		/**
		 * @return mixed
		 */
		public function getServerIp(){
			return $_SERVER['SERVER_ADDR'];
		}

		/**
		 * @return string
		 */
		public function getServerHost(){
			return $_SERVER['HTTP_HOST'];
		}

		/**
		 * @return int
		 */
		public function getServerPort(){
			return $_SERVER['SERVER_PORT'];
		}

		/**
		 * @return string
		 */
		public function getUri(){
			return $_SERVER['REQUEST_URI'];
		}

		/**
		 * @return string
		 */
		public function getPath(){
			return $_SERVER['REQUEST_URI'];
		}

		/**
		 * @return bool
		 */
		public function isDelete(){
			return strcasecmp($_SERVER['REQUEST_METHOD'],'delete')===0;
		}

		/**
		 * @return bool
		 */
		public function isHead(){
			return strtolower($_SERVER['REQUEST_METHOD']) === 'head';
		}

		/**
		 * @param $parameter
		 * @return bool
		 */
		public function hasParam($parameter){
			return $_REQUEST[$parameter];
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getPost($key){
			return $_POST[$key];
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasPost($key){
			return isset($_POST[$key]);
		}

		/**
		 * @return bool
		 */
		public function isPost(){
			return strcasecmp($_SERVER['REQUEST_METHOD'],'post')===0;
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getPut($key){

		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasPut($key){

		}

		/**
		 * @param $headerKey
		 * @param null $default
		 * @return mixed
		 */
		public function getHeader($headerKey, $default = null){
			return isset($this->headers[$headerKey])?$this->headers[$headerKey]:$default;
		}

		/**
		 * @return mixed
		 */
		public function getRequestedWith(){
			return $this->getHeader('Requested-With');
		}

		/**
		 * @return bool
		 */
		public function isAjax(){
			return strcasecmp($this->getHeader('Requested-With'),'XmlHttpRequest')===0;
		}

		/**
		 * @return bool
		 */
		public function isPatch(){
			return strcasecmp($_SERVER['REQUEST_METHOD'],'patch')===0;
		}

		/**
		 * @return bool
		 */
		public function isOptions(){
			return strcasecmp($_SERVER['REQUEST_METHOD'],'options')===0;
		}

		/**
		 * @param $parameter
		 * @return mixed
		 */
		public function getParam($parameter){
			return $_REQUEST[$parameter];
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getQuery($key){
			return $_GET[$key];
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasQuery($key){
			return isset($_GET[$key]);
		}

		/**
		 * @return bool
		 */
		public function isGet(){
			return strcasecmp($_SERVER['REQUEST_METHOD'],'get')===0;
		}

		/**
		 * @return bool
		 */
		public function isPut(){
			return strcasecmp($_SERVER['REQUEST_METHOD'],'put')===0;
		}

		/**
		 * @return string|null
		 */
		public function getReferrer(){
			return $_SERVER['HTTP_REFERRER'];
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
		public function getContent(){
			return file_get_contents('php://input');
		}

		/**
		 * @return string
		 */
		public function getContentType(){
			return $this->getHeader('Content-Type');
		}

		/**
		 * @return bool
		 */
		public function isSoap(){
			return strpos($this->getContentType(),'soap')!==false;
		}

		/**
		 * @return bool
		 */
		public function isJson(){
			return strpos($this->getContentType(),'json')!==false;
		}

		/**
		 * @return bool
		 */
		public function isXml(){
			return strpos($this->getContentType(),'xml')!==false;
		}

		/**
		 * @return mixed
		 */
		public function getData(){
			$contentType = $this->getContentType();
			if(strpos($contentType,'json')!==false){
				$string = file_get_contents('php://input');
				return json_decode($string,true);
			}elseif(strpos($contentType,'soap')!==false){


			}elseif(strpos($contentType,'xml')!==false){


			}
			return null;
		}

		/**
		 * @return bool
		 */
		public function hasFiles(){
			return !empty($_FILES);
		}

		/**
		 * @return array
		 */
		public function getFiles(){
			return $_FILES;
		}
	}
}

