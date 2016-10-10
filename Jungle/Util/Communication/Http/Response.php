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

	use Jungle\Util\Communication\Http;
	use Jungle\Util\Communication\URL;
	use Jungle\Util\Specifications\Http\Cookie;
	use Jungle\Util\Specifications\Http\RequestInterface;
	use Jungle\Util\Specifications\Http\ResponseInterface;
	use Jungle\Util\Specifications\Http\ResponseOnClientInterface;
	use Jungle\Util\Specifications\Http\ResponseSettableInterface;
	use Jungle\Util\Specifications\Http\ServerInterface;
	use Jungle\Util\Specifications\Hypertext\Document;
	use Jungle\Util\Specifications\Hypertext\Document\ReadProcessor;
	use Jungle\Util\Specifications\Hypertext\Document\WriteProcessor;

	/**
	 * Class Response
	 * @package Jungle\Util\Communication\Http
	 */
	class Response extends Document implements ResponseInterface,ResponseSettableInterface, ResponseOnClientInterface{

		/** @var  Server */
		protected $server;

		/** @var  Request */
		protected $request;

		/** @var  string */
		protected $plain_response;

		/** @var  string */
		protected $plain_request;

		/** @var  int */
		protected $code;

		/** @var  string */
		protected $message;

		/** @var  Cookie[]  */
		protected $cookies = [];

		/** @var  array|null */
		protected $redirect;

		/** @var bool  */
		protected $sent = false;




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
		 * @param $code
		 * @return mixed
		 */
		public function setCode($code){
			$this->code = $code;
		}

		/**
		 * @return mixed
		 */
		public function getCode(){
			return $this->code;
		}


		/**
		 * @param string $message
		 * @return $this
		 */
		public function setMessage($message){
			$this->message = $message;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getMessage(){
			return $this->message;
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
		 * @param bool $asArray
		 * @return null|string
		 */
		public function getRedirectUrl($asArray = false){
			if(isset($this->headers['Location'])){
				return URL::absoluteUrl($this->getHeader('Location'), [
					'host'      => $this->getServer()->getHost(),
					'scheme'    => $this->getRequest()->getScheme(),
					'path'      => $this->getRequest()->getUri()
				],$asArray);
			}
			return null;
		}

		/**
		 * @return mixed
		 */
		public function isContentLocated(){
			return isset($this->headers['Content-Location']);
		}

		/**
		 * @param bool $asArray
		 * @return mixed
		 */
		public function getContentLocationUrl($asArray = false){
			if(isset($this->headers['Content-Location'])){
				return URL::absoluteUrl($this->getHeader('Content-Location'), [
					'host'      => $this->getServer()->getHost(),
					'scheme'    => $this->getRequest()->getScheme(),
					'path'      => $this->getRequest()->getUri()
				],$asArray);
			}
			return null;
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
		 * @param array|string $key
		 * @param $value
		 * @param int $expires
		 * @param string $path
		 * @param null $secure
		 * @param null $httpOnly
		 * @param null $domain
		 * @return mixed
		 */
		public function setCookie(
			$key, $value = null, $expires = null, $path = null, $secure = null, $httpOnly = null, $domain = null
		){
			if(is_array($key)){
				$key = array_replace([
					'key'       => null,
					'value'     => null,
					'domain'     => null,
					'expires'   => null,
					'path'      => null,
					'secure'    => null,
					'httpOnly'  => null,
				],$key);
				extract($key,EXTR_OVERWRITE);
			}

			if(!$domain){
				$domain = $this->request->getServer()->getDomain();
			}

			$cookie = new Cookie();
			$cookie->setName($key);
			$cookie->setValue($value);
			$cookie->setExpires($expires);
			$cookie->setPath($path);
			$cookie->setSecure($secure);
			$cookie->setHttpOnly($httpOnly);
			$cookie->setDomain($domain);
			$this->cookies[$key] = $cookie;
		}


		/**
		 * @param $key
		 * @return Cookie
		 */
		public function getCookie($key){
			return isset($this->cookies[$key])?$this->cookies[$key]:null;
		}

		/**
		 * @return \Jungle\Util\Specifications\Http\Cookie[]
		 */
		public function getCookies(){
			return $this->cookies;
		}

		/**
		 * @param $content
		 * @return mixed
		 */
		public function setContent($content = null){
			$this->content = $content;
			return $this;
		}

		/**
		 * @param $type
		 * @return mixed
		 */
		public function setContentType($type){
			$this->setHeader('Content-Type', $type);
			return $this;
		}

		/**
		 * @param $disposition
		 * @return mixed
		 */
		public function setContentDisposition($disposition){
			$this->setHeader('Content-Disposition', $disposition);
			return $this;
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
			$this->sent = true;
		}

		/**
		 * @param WriteProcessor $writer
		 */
		public function beforeWrite(WriteProcessor $writer){
			/** Meta Header Write */
			$writer->write("HTTP/1.1 {$this->code} {$this->message}\r\n");
		}


		/**
		 * @param Document\ReadProcessor $reader
		 * @return mixed
		 */
		public function onHeadersRead(Document\ReadProcessor $reader){
			$collection = $this->getHeaderCollection('Set-Cookie');
			foreach($collection as $cookieString){
				$cookie = Http::parseCookieString($cookieString);
				$this->setCookie($cookie);
			}

			/** @var Agent $browser */
			$browser = $this->getRequest()->getBrowser();
			$browser->onResponseHeadersLoaded($this);

		}

		/**
		 * @param ReadProcessor $reader
		 */
		public function onContentsRead(ReadProcessor $reader){
			$this->cache = $reader->getBuffered();
			/** @var Agent $browser */
			$browser = $this->getRequest()->getBrowser();
			$browser->onResponseContentsLoaded($this);
		}



		/**
		 * @param $data
		 * @param $readingIndex
		 * @return mixed|void
		 */
		public function beforeHeaderRead($data, $readingIndex){
			/** Meta Header Read */
			if($readingIndex===0){
				list($protocol, $code, $message) = explode(' ',$data,3);
				$this->code = intval($code);
				$this->message = $message;
				return false;
			}
			return null;
		}

		/**
		 * @return string
		 * @throws \Exception
		 */
		protected function render(){
			$writer = $this->getWriteProcessor();
			$writer->setDocument($this);
			$writer->setBuffer(null);
			$source = $writer->process('');
			$this->cache = (string)$source;
			$this->cacheable = true;
			return $source;
		}

	}
}

