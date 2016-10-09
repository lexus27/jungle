<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.10.2016
 * Time: 14:03
 */
namespace Jungle\Util\Communication\Http {
	
	use Jungle\Util\Communication\Connection\Stream\Socket;
	use Jungle\Util\ContentsAwareInterface;
	use Jungle\Util\Specifications\Http\BrowserInterface;
	use Jungle\Util\Specifications\Http\ClientInterface;
	use Jungle\Util\Specifications\Http\Cookie;
	use Jungle\Util\Specifications\Http\ProxyInterface;
	use Jungle\Util\Specifications\Http\ResponseInterface;
	use Jungle\Util\Specifications\Hypertext\Document;
	use Jungle\Util\Specifications\Hypertext\Document\ReadProcessor;
	use Jungle\Util\Specifications\Hypertext\Document\WriteProcessor;
	use Jungle\Util\Specifications\Hypertext\DocumentInterface;
	use Jungle\Util\Value\UserAgent;

	/**
	 * Class Agent
	 * @package Jungle\Util\Communication\Http
	 */
	class Agent implements BrowserInterface, ClientInterface{

		/** @var  string|null */
		protected $user_agent;

		/** @var  string */
		protected $platform;

		/** @var  string */
		protected $browser;

		/** @var  string */
		protected $version;

		/** @var  array  */
		protected $desired_languages = ['ru-RU','ru','en-US','en'];

		/** @var  string */
		protected $desired_charset = 'utf-8';

		/** @var  array */
		protected $cookies_cache = [];

		/** @var array  */
		protected $default_headers = [];

		/** @var  WriteProcessor */
		protected $_write_processor;
		/** @var  ReadProcessor */
		protected $_read_processor;


		/**
		 * Agent constructor.
		 * @param null $user_agent
		 */
		public function __construct($user_agent = null){
			$this->desired_charset = ini_get('default_charset');
			$this->user_agent = $user_agent;
			if($this->user_agent){
				$a = UserAgent::parse($user_agent);
				$this->platform = $a['platform'];
				$this->browser  = $a['browser'];
				$this->version  = $a['version'];
			}
		}

		/**
		 * @param null $user_agent
		 */
		public function setUserAgent($user_agent = null){
			$this->user_agent = $user_agent;
			if($this->user_agent){
				$a = UserAgent::parse($user_agent);
				$this->platform = $a['platform'];
				$this->browser  = $a['browser'];
				$this->version  = $a['version'];
			}
		}

		/**
		 * @return string
		 */
		public function getName(){
			return $this->browser;
		}

		/**
		 * @return string
		 */
		public function getVersion(){
			return $this->version;
		}

		/**
		 * @return bool
		 */
		public function isMobile(){
			 return preg_match('@android|ios|ipad|mobile|phone@i',$this->platform)>0;
		}

		/**
		 * @return bool
		 */
		public function isUnknown(){
			return $this->browser===null;
		}

		/**
		 * @return string
		 */
		public function getPlatform(){
			return $this->platform;
		}

		/**
		 * @return string
		 */
		public function getUserAgent(){
			return $this->user_agent;
		}


		/**
		 * @param array $languages
		 */
		public function setDesiredLanguages(array $languages){
			$this->desired_languages = $languages;
		}

		/**
		 * @return string|null
		 */
		public function getBestLanguage(){
			if(!$this->desired_languages){
				return 'en-US';
			}
			return current($this->desired_languages);
		}

		/**
		 * @return string[]
		 */
		public function getDesiredLanguages(){
			if(!$this->desired_languages){
				return ['en-US'];
			}
			return $this->desired_languages;
		}

		/**
		 * @return string|null
		 */
		public function getBestMediaType(){
			// TODO: Implement getBestMediaType() method.
		}

		/**
		 * @return array
		 */
		public function getDesiredMediaTypes(){
			// TODO: Implement getDesiredMediaTypes() method.
		}

		/**
		 * @return string|null
		 */
		public function getBestCharset(){
			if(!$this->desired_charset){
				return 'utf-8';
			}
			return $this->desired_charset;
		}

		/**
		 * @param array $headers
		 * @return $this
		 */
		public function setDefaultHeaders(array $headers){
			$this->default_headers = $headers;
			return $this;
		}

		/**
		 * @return array
		 */
		public function getDefaultHeaders(){
			return $this->default_headers;
		}

		/**
		 * @return string
		 */
		public function getIp(){
			return $_SERVER['SERVER_ADDR'];
		}

		/**
		 * @return string
		 */
		public function getDomain(){
			return $_SERVER['HTTP_HOST'];
		}

		/**
		 * @return mixed
		 */
		public function getHost(){
			return $_SERVER['SERVER_ADDR'];
		}

		/**
		 * @return int
		 */
		public function getPort(){
			return 80;
		}

		/**
		 * @return bool
		 */
		public function isProxied(){
			// TODO: Implement isProxied() method.
		}

		/**
		 * @return ProxyInterface
		 */
		public function getProxy(){
			// TODO: Implement getProxy() method.
		}

		/**
		 * @param $url
		 * @return Response
		 */
		public function get($url){
			$request = new Request($url,'get');
			$request->setBrowser($this);
			return $this->execute($request);
		}

		/**
		 * @param $url
		 * @param array $params
		 * @param ContentsAwareInterface[] $files
		 * @return Response|ResponseInterface|Document
		 */
		public function post($url, array $params = null, array $files = null){
			$request = new Request($url,'get');
			$request->setBrowser($this);
			if($params){
				$request->setPostParams($params);
			}
			if($files){
				$request->setFiles($files);
			}
			return $this->execute($request);
		}

		/**
		 * @param $url
		 * @param $media_type
		 * @return Response|ResponseInterface|Document
		 */
		public function getMedia($url, $media_type = 'text/*'){
			$request = new Request($url,'get');
			$request->setBrowser($this);
			$request['Accept'] = $media_type;
			return $this->execute($request);
		}





		/**
		 * @param Request $request
		 * @return Response|ResponseInterface|Document
		 * @throws \Exception
		 */
		public function execute(Request $request){

			/**
			 * @var Response $response
			 */

			$request->setBrowser($this);
			$socket = new Socket([
				'host'          => $request->getServer()->getHost(),
				'port'          => $request->isSecure()?443:$request->getServer()->getPort(),
				'transport'     => $request->isSecure()?'ssl':null,
				'timeout'       => null,
			]);

			$socket->connect();

			$this->onRequestPrepare($request);

			/** Request Sending... */
			$socket = $this->_getWriterFor($request)->process($socket);

			/** Response reading... */
			$response = $request->getResponse();
			$response = $this->_getReaderFor($response)->process($socket);

			$socket->close();

			return $response;
		}

		/**
		 * @return string
		 */
		public function getAcceptLanguage(){
			$languages = $this->getDesiredLanguages();
			$languages = array_reverse($languages, false);
			$count = count($languages);
			$q = [];
			foreach($languages as $i => $lang){
				$p = round((((100 / $count) * ($i+1)) / 100),1);
				$q[] =  $lang . ($p<1?(';q='.$p):'');
			}
			return implode(',', array_reverse($q));
		}

		/**
		 * @param Request $request
		 */
		public function onRequestPrepare(Request $request){
			if($this->default_headers){
				foreach($this->default_headers as $k => $h){
					if(!$request->hasHeader($k)){
						$request->setHeader($k,$h);
					}
				}
			}
			if(!$request->hasHeader('User-Agent')){
				$request->setHeader('User-Agent', $this->getUserAgent());
			}
			if(!$request->hasHeader('Accept-Language')){
				$request->setHeader('Accept-Language', $this->getAcceptLanguage());
			}
			if(!$request->hasHeader('Accept-Charset')){
				$request->setHeader('Accept-Charset', $this->getBestCharset());
			}
			$this->insertCookies($request);
		}

		/**
		 * @param Request $request
		 */
		protected function insertCookies(Request $request){
			$server = $request->getServer();
			$domain_base = $server->getDomainBase();
			$domain = $server->getDomain();
			if(isset($this->cookies_cache[$domain_base])){
				$cookiePairs = [];
				/** @var Cookie $cookie */
				foreach($this->cookies_cache[$domain_base] as $name => $cookie){
					if($request->hasCookie($name)){
						continue;
					}
					if(!$cookie->checkDomain($domain)){
						continue;
					}
					if($cookie->isSecure() && !$request->isSecure()){
						continue;
					}
					if($cookie->isHttpOnly() && $request->hasHeader('Sec-WebSocket-Protocol')){
						continue;
					}
					if(strcmp($cookie->getPath(),$request->getUri()) > 0){
						continue;
					}
					if($cookie->isOverdue()){
						continue;
					}
					$cookiePairs[] = urlencode($name).'='.urlencode($cookie->getValue());
				}
				if($cookiePairs){
					$request->setHeader('Cookie',implode('; ',$cookiePairs));
				}
			}
		}

		/**
		 * @param Response $response
		 */
		public function onResponseHeadersLoaded(Response $response){
			$cookies = $response->getCookies();
			$domain_base = $response->getRequest()->getServer()->getDomainBase();
			foreach($cookies as $cookie){
				$this->cookies_cache[$domain_base][$cookie->getName()] = $cookie;
			}
		}

		/**
		 * @param Response $response
		 */
		public function onResponseContentsLoaded(Response $response){

		}


		/**
		 * @param DocumentInterface $document
		 * @return ReadProcessor
		 */
		protected function _getReaderFor(DocumentInterface $document){
			if(!$this->_read_processor){
				$this->_read_processor = new Document\ReadProcessor();
				$this->_read_processor->setBufferToString();
			}
			return $this->_read_processor->setDocument($document);
		}

		/**
		 * @param DocumentInterface $document
		 * @return WriteProcessor
		 */
		protected function _getWriterFor(DocumentInterface $document){
			if(!$this->_write_processor){
				$this->_write_processor = new Document\WriteProcessor();
				$this->_write_processor->setBufferToString();
			}
			return $this->_write_processor->setDocument($document);
		}

	}
}

