<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.06.2016
 * Time: 18:36
 */
namespace Jungle\Http {
	
	use Jungle\Application\RequestInterface;
	use Jungle\User\AccessAuth\Auth;
	use Jungle\Util\Communication\HttpFoundation\BrowserInterface;
	use Jungle\Util\Communication\HttpFoundation\ResponseInterface;
	use Jungle\Util\Communication\HttpFoundation\ServerInterface;
	use Jungle\Util\Communication\Hypertext\HeaderRegistryTrait;
	use Jungle\Util\Value;
	
	
	/**
	 * Class Request
	 * @package Jungle\Http\Request
	 */
	class Request implements \Jungle\Util\Communication\HttpFoundation\RequestInterface, RequestInterface{

		use HeaderRegistryTrait;

		const AUTH_DIGEST   = 'digest';
		const AUTH_BASE     = 'base';

		/** @var  Request */
		protected static $instance;

		protected static $request_time;

		/** @var  Auth */
		protected $auth;

		/** @var  Browser  */
		protected $browser;

		/** @var  Server  */
		protected $server;

		/** @var  Client  */
		protected $client;

		/** @var  Response  */
		protected $response;

		/** @var   */
		protected $uri;

		/** @var null  */
		protected $content_type = null;

		/** @var  mixed */
		protected $content = false;

		/** @var  UploadedFile[]|null */
		protected $files;

		/**
		 * @return mixed
		 */
		public static function serverRequestTime(){
			if(is_null(self::$request_time)){
				self::$request_time = $_SERVER['REQUEST_TIME'];
			}
			return self::$request_time;
		}

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
		protected function __construct(){
			$this->setHeaders(self::getallheaders(),false,false);
			if(isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER']){
				$this->auth = Auth::getAccessAuth([$_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']]);
			}
			$this->browser  = new Browser($this);
			$this->server   = new Server();
			$this->client   = new Client();
			$this->response = new Response($this);
			$this->content_type = isset($this->headers['Content-Type'])?$this->headers['Content-Type']:null;
		}
		
		/**
		 * @return array
		 */
		public static function getallheaders(){
			
			if(function_exists('getallheaders')){
				return \getallheaders();
			}
			
			$headers = [];
			foreach($_SERVER as $key=>$value){
				if(preg_match('HTTP_(.+)',$key,$result)){
					$headers[$result[1]]=$value;
				}
			}
			return $headers;
		}

		/**
		 * @return string
		 */
		public function getFullPath(){
			return urldecode($_SERVER['REQUEST_URI']);
		}

		protected function __clone(){}



		/**
		 * @return ResponseInterface
		 */
		public function getResponse(){
			return $this->response;
		}

		/**
		 * @param $specificity
		 * @return bool
		 */
		public function hasSpecificity($specificity){
			return stripos($specificity,'http')!==false;
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
		 * @return Client
		 */
		public function getClient(){
			return $this->client;
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
		 * @return int
		 */
		public function getTime(){
			return $_SERVER['REQUEST_TIME'];
		}

		/**
		 * @return string
		 */
		public function getUrl(){
			return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . urldecode($_SERVER['REQUEST_URI']);
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
			return isset($_SERVER['PHP_AUTH_DIGEST']) && $_SERVER['PHP_AUTH_DIGEST']?self::AUTH_DIGEST:self::AUTH_BASE;
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
		public function getUri(){
			if(is_null($this->uri)){
				$arr = explode('?',$_SERVER['REQUEST_URI']);
				$this->uri = urldecode($arr[0]);
			}
			return $this->uri;
		}

		/**
		 * @return string
		 */
		public function getPath(){
			if(is_null($this->uri)){
				$arr = explode('?',$_SERVER['REQUEST_URI']);
				$this->uri = urldecode($arr[0]);
			}
			return $this->uri;
		}

		/**
		 * @return bool
		 */
		public function isDelete(){
			return stripos($_SERVER['REQUEST_METHOD'],'delete')!==false;
		}

		/**
		 * @return bool
		 */
		public function isHead(){
			return stripos($_SERVER['REQUEST_METHOD'],'head')!==false;
		}

		/**
		 * @return bool
		 */
		public function isPatch(){
			return stripos($_SERVER['REQUEST_METHOD'],'patch')!==false;
		}

		/**
		 * @return bool
		 */
		public function isOptions(){
			return stripos($_SERVER['REQUEST_METHOD'],'options')!==false;
		}

		/**
		 * @return bool
		 */
		public function isPost(){
			return stripos($_SERVER['REQUEST_METHOD'],'post')!==false;
		}

		/**
		 * @return bool
		 */
		public function isGet(){
			return stripos($_SERVER['REQUEST_METHOD'],'get')!==false;
		}

		/**
		 * @return bool
		 */
		public function isPut(){
			return stripos($_SERVER['REQUEST_METHOD'],'put')!==false;
		}

		/**
		 * @return mixed
		 */
		public function isConnect(){
			return stripos($_SERVER['REQUEST_METHOD'],'connect')!==false;
		}

		/**
		 * @return mixed
		 */
		public function isTrace(){
			return stripos($_SERVER['REQUEST_METHOD'],'trace')!==false;
		}


		/**
		 * @return bool
		 */
		public function isSecure(){
			return (isset($_SERVER['HTTPS'])?true:false) && stripos($_SERVER['REQUEST_SCHEME'],'https')!==false;
		}


		/**
		 * @param $key
		 * @param null $filter
		 * @param null $default
		 * @return mixed
		 */
		public function getPost($key = null, $default = null, $filter = null){
			if($key === null){
				return $_POST;
			}
			if(isset($_POST[$key])){
				return $this->_handleValueFilter($_POST[$key],$filter);
			}
			return $default;
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasPost($key = null){
			if($key === null)return !empty($_POST);
			return isset($_POST[$key]);
		}

		/**
		 * @param $parameter
		 * @param null $filter
		 * @param null $default
		 * @return mixed
		 */
		public function getParam($parameter = null, $default = null, $filter = null){
			if($parameter === null){
				return $_REQUEST;
			}
			if(isset($_REQUEST[$parameter])){
				return $this->_handleValueFilter($_REQUEST[$parameter],$filter);
			}
			return $this->getObjectParam($parameter,$filter,$default);
		}

		/**
		 * @param $parameter
		 * @return bool
		 */
		public function hasParam($parameter = null){
			if($parameter === null){
				return !empty($_REQUEST);
			}
			return isset($_REQUEST[$parameter]) || $this->hasObjectParam($parameter);
		}

		/**
		 * @param $key
		 * @param null $filter
		 * @param null $default
		 * @return mixed
		 */
		public function getQuery($key = null, $default = null, $filter = null){
			if($key === null){
				return $_GET;
			}
			if(isset($_GET[$key])){
				return $this->_handleValueFilter($_GET[$key],$filter);
			}
			return $default;
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasQuery($key = null){
			if($key === null)return !empty($_GET);
			return isset($_GET[$key]);
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getPut($key){}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasPut($key){}



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
			return stripos($this->getHeader('Requested-With'),'XmlHttpRequest')!==false;
		}

		/**
		 * @return string|null
		 */
		public function getReferrer(){
			return isset($_SERVER['HTTP_REFERRER'])?$_SERVER['HTTP_REFERRER']:null;
		}

		/**
		 * @return mixed
		 */
		public function getContent(){
			if($this->content === false){
				$content = file_get_contents('php://input');
				$this->content = !is_string($content) ? null : $this->_handleContent($content);
			}
			return $this->content;
		}

		/**
		 * @return bool
		 */
		public function hasObject(){
			return is_array($this->content) || strpos($this->content_type,'json')!==false || strpos($this->content_type,'xml')!==false || strpos($this->content_type,'soap')!==false;
		}

		/**
		 * @return array|mixed|null
		 */
		public function getObject(){
			if(is_array($this->content)){
				return $this->content;
			}else{
				$content = $this->getContent();
				if(is_array($content)){
					return $content;
				}
			}
			return null;
		}

		/**
		 * @param $key
		 * @param null $filter
		 * @param null $default
		 * @return mixed|null
		 */
		public function getObjectParam($key, $default = null, $filter = null){
			if(is_array($this->content)){
				if(isset($this->content[$key])){
					return $this->_handleValueFilter($this->content[$key], $filter);
				}
			}else{
				$content = $this->getContent();
				if(is_array($content) && isset($content[$key])){
					return $this->_handleValueFilter($content[$key], $filter);
				}
			}
			return $default;
		}

		/**
		 * @param $key
		 * @return bool
		 */
		public function hasObjectParam($key){
			if(is_array($this->content)){
				return isset($this->content[$key]);
			}else{
				return is_array(($content = $this->getContent())) && isset($content[$key]);
			}
		}

		/**
		 * @return string
		 */
		public function getContentType(){
			return $this->content_type;
		}

		/**
		 * @param string $content
		 * @return array|string|int|float|object|null
		 */
		protected function _handleContent($content){
			if(strpos($this->content_type,'soap')!==false){
				return [];
			}
			if(strpos($this->content_type,'xml')!==false){
				return [];
			}
			if(strpos($this->content_type,'json')!==false){
				return $content?json_decode($content,true):[];
			}
			return null;
		}

		/**
		 * @return bool
		 */
		public function isSoap(){
			return strpos($this->content_type,'soap')!==false;
		}

		/**
		 * @return bool
		 */
		public function isJson(){
			return strpos($this->content_type,'json')!==false;
		}

		/**
		 * @return bool
		 */
		public function isXml(){
			return strpos($this->content_type,'xml')!==false;
		}

		/**
		 * @return bool
		 */
		public function hasFiles(){
			return !empty($this->files) || !empty($_FILES);
		}

		/**
		 * @return array
		 */
		public function getFiles(){
			if($this->files === null){
				$this->files = [];
				if(!empty($_FILES)){
					foreach($_FILES as $param_name => $_file){

						$this->files[$param_name] = [];
						if(is_array($_file['error'])){
							foreach($_file['error'] as $i => $error){
								if(is_uploaded_file($_file['tmp_name'][$i])){
									if($_file['error'][$i] !== UPLOAD_ERR_OK || $_file['size'][$i] || $_file['tmp_name'][$i]){
										$uploaded_file = new UploadedFile();
										$uploaded_file->status = $error;
										$uploaded_file->param_name = $param_name.'['.$i.']';
										$uploaded_file->name = $_file['name'][$i];
										$uploaded_file->path = $_file['tmp_name'][$i];
										$uploaded_file->mime_type = $_file['type'][$i];
										$uploaded_file->size = $_file['size'][$i];
										$this->files[$param_name][] = $uploaded_file;
									}
								}
							}
						}else{
							if(is_uploaded_file($_file['tmp_name'])){
								if($_file['error'] === UPLOAD_ERR_OK && !$_file['size'] && !$_file['tmp_name']){
									$this->files[$param_name] = null;
								}else{
									$uploaded_file = new UploadedFile();
									$uploaded_file->status = $_file['error'];
									$uploaded_file->param_name = $param_name;
									$uploaded_file->name = $_file['name'];
									$uploaded_file->path = $_file['tmp_name'];
									$uploaded_file->size = $_file['size'];
									$uploaded_file->mime_type = $_file['type'];
									$this->files[$param_name] = $uploaded_file;
								}
							}else{

							}
						}
					}
				}
			}
			return $this->files;
		}

		/**
		 * @param $param_name
		 * @param bool|false $asCollection
		 * @return UploadedFile[]|UploadedFile|null
		 */
		public function getFile($param_name, $asCollection = false){
			$this->getFiles();
			if(isset($this->files[$param_name])){
				if($asCollection){
					return is_array($this->files[$param_name])?$this->files[$param_name]:[$this->files[$param_name]];
				}else{
					return is_array($this->files[$param_name])?(isset($this->files[$param_name][0])?$this->files[$param_name][0]:null):$this->files[$param_name];
				}
			}
			return $asCollection?[]:null;
		}

		/**
		 * @param $value
		 * @param null $filter
		 * @return mixed|null
		 *
		 * @TODO: Sanitize & Filter Value in DataBase and other, by RegExp Type component or implement filtering abstract subject for all usage
		 */
		protected function _handleValueFilter($value, $filter = null){
			if(!isset($value)){
				return $value;
			}
			if($filter === null){
				return $value;
			}
			if(is_array($filter)){
				if(isset($filter['filter'])){
					$_filter = array_replace([
						'type' => FILTER_DEFAULT,
						'options' => []
					],(array)$filter['filter']);
					$value = filter_var($value, $_filter['type'],$_filter['options']);
				}elseif(isset($filter['type'])){
					$value = filter_var($value, $filter['type']);
				}elseif(isset($filter['converter'])){
					// Конвертирование таких пришедших данных как JSON CommaSeparatedList
					// Проверка
					if($filter['converter'] === 'list'){

					}

				}

				if(isset($filter['vartype'])){
					Value::settype($value, $filter['vartype']);
				}
				return $value;
			}else{
				return filter_var($value, $filter);
			}
		}

	}
}

