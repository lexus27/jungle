<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.07.2016
 * Time: 4:12
 */
namespace Jungle\Application\Strategy\Http {

	use Jungle\Application\Component;
	use Jungle\Di\DiInterface;
	use Jungle\Http\Request;
	use Jungle\Http\Response;
	use Jungle\Util\Communication\URL;
	use Jungle\Util\Specifications\Http\Cookie\ManagerInterface;
	use Jungle\Util\Specifications\Http\RequestInterface;
	use Jungle\Util\Specifications\Http\ResponseInterface;

	/**
	 * Class Manager
	 * @package Jungle\Application\Strategy\Http
	 */
	class Manager extends Component implements ManagerInterface{

		/** @var  Manager */
		protected $parent;


		/** @var  RequestInterface */
		protected $request;

		/** @var  ResponseInterface */
		protected $response;

		/** @var  int */
		protected $default_expires;

		/** @var  string */
		protected $default_path;

		/** @var  string */
		protected $default_host;

		/** @var  bool */
		protected $default_secure;

		/** @var  bool */
		protected $default_http_only;

		/**
		 * Manager constructor.
		 * @param Manager|null $parent
		 */
		public function __construct(Manager $parent = null){
			$this->parent = $parent;
			if($parent){
				if(!$parent->_dependency_injection){
					throw new \LogicException('Dependency Injection not defined in supplied parent!');
				}
				$this->_dependency_injection = $parent->_dependency_injection;
			}
		}

		/**
		 * @param DiInterface $di
		 * @return $this
		 */
		public function setDi(DiInterface $di){
			$this->_dependency_injection = $di;
			$this->request = null;
			$this->response = null;
			return $this;
		}

		/**
		 * @param null $expires
		 * @return mixed
		 */
		public function setExpires($expires = null){
			$this->default_expires = $expires;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getExpires(){
			if($this->default_expires !== null){
				return $this->default_expires;
			}
			if($this->parent){
				return $this->parent->getExpires();
			}
			return time() + 86000;
		}

		/**
		 * @param null $path
		 * @return mixed
		 */
		public function setPath($path = null){
			$this->default_path = $path;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getPath(){
			if($this->default_path !== null){
				return $this->default_path;
			}
			if($this->parent){
				return $this->parent->getPath();
			}
			return '/';
		}

		/**
		 * @param $hostname
		 * @return mixed
		 */
		public function setDomain($hostname = null){
			$this->default_host = $hostname;
			return $this;
		}

		/**
		 * @return string|null
		 */
		public function getDomain(){
			if($this->default_path !== null){
				return $this->default_path;
			}
			if($this->parent){
				return $this->parent->getDomain();
			}
			return $this->getDi()->getShared('request')->getServer()->getHost();
		}

		/**
		 * @param null $secure
		 * @return mixed
		 */
		public function setSecure($secure = null){
			$this->default_secure = $secure;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isSecure(){
			if($this->default_secure !== null){
				return $this->default_secure;
			}
			if($this->parent){
				return $this->parent->isSecure();
			}
			return false;
		}

		/**
		 * @param $domain
		 * @return mixed
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
		 * @param null $httpOnly
		 * @return mixed
		 */
		public function setHttpOnly($httpOnly = null){
			$this->default_http_only = $httpOnly;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isHttpOnly(){
			if($this->default_http_only !== null){
				return $this->default_http_only;
			}
			if($this->parent){
				return $this->parent->isHttpOnly();
			}
			return false;
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function getCookie($name){
			$cookie = $this->getResponse()->getCookie($name);
			if(!$cookie){
				return $this->getRequest()->getCookie($name);
			}
			if($cookie->isOverdue()){
				return null;
			}
			return $cookie->getValue();
		}

		/**
		 * @param $name
		 * @return \Jungle\Util\Specifications\Http\Cookie
		 */
		public function getCookieObject($name){
			return $this->getResponse()->getCookie($name);
		}

		/**
		 * @param $key
		 * @param $value
		 * @param null $expire
		 * @param null $path
		 * @param null $secure
		 * @param null $httpOnly
		 * @param null $domain
		 * @return mixed
		 */
		public function setCookie(
			$key, $value = null, $expire = null, $path = null, $secure = null, $httpOnly = null, $domain = null
		){
			if(is_array($key)){
				$this->getResponse()->setCookie($key);
			}else{
				$this->getResponse()->setCookie($key,$value,
					$expire!==null?$expire:$this->getExpires(),
					$path!==null?$path:$this->getPath(),
					$secure!==null?$secure:$this->isSecure(),
					$httpOnly!==null?$httpOnly:$this->isHttpOnly(),
					$domain !== null?$domain:$this->getDomain()
				);
			}
			return $this;
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function removeCookie($name){
			$this->getResponse()->setCookie($name,null,time() - 3600);
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
		 * @param ResponseInterface $response
		 * @return mixed
		 */
		public function setResponse(ResponseInterface $response){
			$this->response = $response;
			return $this;
		}

		/**
		 * @return Request
		 */
		public function getRequest(){
			if($this->request){
				return $this->request;
			}
			return $this->request = $this->getDi()->getShared('request');
		}


		/**
		 * @return Response
		 */
		public function getResponse(){
			if($this->response){
				return $this->response;
			}
			return $this->response = $this->getDi()->getShared('response');
		}

		/**
		 * @return mixed
		 */
		public function isSent(){
			return $this->getResponse()->isSent();
		}
	}
}

