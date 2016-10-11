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

	use Jungle\Util\Communication\Http;
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

		const EXECUTION_STREAM = 'execution_stream';

		/** @var  string|null */
		protected $user_agent,
				  $platform,
				  $browser,
				  $version;

		/** @var  array  */
		protected $desired_languages = ['ru-RU','ru','en-US','en'];

		/** @var  string */
		protected $desired_charsets = ['utf-8'];

		/** @var  array */
		protected $cookies_cache = [];

		/** @var array  */
		protected $default_headers = [];

		/** @var  bool  */
		protected $support_referrer = false,
				  $follow_location = false,
			      $follow_content_location = false;

		/** @var  CacheManager */
		protected $cache_manager;

		/** @var  NetworkManager */
		protected $network_manager;

		/** @var  WriteProcessor */
		protected $_write_processor;

		/** @var  ReadProcessor */
		protected $_read_processor;


		/**
		 * Agent constructor.
		 * @param null $user_agent
		 */
		public function __construct($user_agent = null){
			$this->desired_charsets = [ini_get('default_charset')];
			$this->user_agent = $user_agent;
			if($this->user_agent){
				$this->setUserAgent($user_agent);
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
		public function getUserAgent(){
			return $this->user_agent;
		}

		/**
		 * @param array $languages
		 * @return $this
		 */
		public function setDesiredLanguages(array $languages){
			$this->desired_languages = $languages;
			return $this;
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
			return current($this->desired_charsets);
		}

		/**
		 * @return mixed
		 */
		public function getDesiredCharsets(){
			return $this->desired_charsets;
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
			return $_SERVER['HTTP_HOST'];
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
		 * @return Request
		 */
		public function get($url){
			return new Request($this,$url,'get');
		}

		/**
		 * @param $url
		 * @param array $params
		 * @param ContentsAwareInterface[] $files
		 * @return Request
		 */
		public function post($url, array $params = null, array $files = null){
			$request = new Request($this,$url,'post');
			if($params){
				if($files === null){
					$post = [];
					$files = [];
					foreach($params as $key => $param){
						if($param instanceof ContentsAwareInterface){
							if(is_string($key)){
								$files[$key] = $param;
							}else{
								$files[] = $param;
							}
						}else{
							$post[$key] = $param;
						}
					}
					$request->setPostParams($post);
				}
			}
			if($files){
				$request->setFiles($files);
			}
			return $request;
		}

		/**
		 * @param NetworkManager $network_manager
		 * @return $this
		 */
		public function setNetworkManager(NetworkManager $network_manager){
			$this->network_manager = $network_manager;
			return $this;
		}

		/**
		 * @return NetworkManager
		 */
		public function getNetworkManager(){
			if(!$this->network_manager){
				$this->network_manager = new NetworkManager();
			}
			return $this->network_manager;
		}



		/**
		 * @param CacheManager $cache_manager
		 * @return $this
		 */
		public function setCacheManager(CacheManager $cache_manager = null){
			$this->cache_manager = $cache_manager;
			return $this;
		}

		/**
		 * @return CacheManager|null
		 */
		public function getCacheManager(){
			return $this->cache_manager;
		}

		/**
		 * @param $host
		 * @param $scheme
		 * @return Server
		 */
		public function needServer($host, $scheme){
			$nm = $this->getNetworkManager();
			return $nm->getServer($host,$scheme);
		}


		/**
		 * @param Request $request
		 * @return Response|ResponseInterface|Document
		 */
		public function execute(Request $request){
			$server = $request->getServer();
			$stream = $server->takeStream();
			/** Request sending... */
			$request->setLoading(true);
			$this->_getWriterFor($request)->process($stream);

			/** Response reading... */
			$response = $request->getResponse();
			if(!$response->isReused()){
				$response->setLoading(true);
				$response = $this->_getReaderFor($response)->process($stream);
			}
			return $response;
		}

		/**
		 * @param Request $request
		 * @return bool|Response
		 */
		public function push(Request $request){
			$server = $request->getServer();
			$stream = $server->takeStream();
			/** Request sending... */
			$request->setLoading(true);
			$this->_getWriterFor($request)->process($stream);
			if($request->getOption('cache_manager::from_cache')){
				return $request->getResponse();
			}
			return true;
		}

		/**
		 * @param Request $request
		 * @return Response
		 * @throws \Exception
		 */
		public function pull(Request $request){
			$stream = $request->getOption(Agent::EXECUTION_STREAM);
			$response = $request->getResponse();
			if(!$response->isReused()){
				$response->setLoading(true);
				$response = $this->_getReaderFor($response)->process($stream);
			}
			return $response;
		}

		/**
		 * @param Request $request
		 * @param WriteProcessor $writer
		 * @return void
		 */
		public function beforeRequest(Request $request, WriteProcessor $writer){
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
				$request->setHeader('Accept-Language', Http::renderAccept($this->getDesiredLanguages()));
			}
			if(!$request->hasHeader('Accept-Charset')){
				$request->setHeader('Accept-Charset', $this->getBestCharset());
			}
			$this->_insertCookies($request);
			return null;
		}

		/**
		 * @param Response $response
		 * @param Request $request
		 */
		public function onResponseHeadersLoaded(Response $response, Request $request){
			$cookies = $response->getCookies();
			$domain_base = $response->getRequest()->getServer()->getDomainBase();
			foreach($cookies as $cookie){
				$this->cookies_cache[$domain_base][$cookie->getName()] = $cookie;
			}
			if($this->cache_manager){
				$this->cache_manager->onResponseHeaders($response,$request);
			}
		}

		/**
		 * @param Response $response
		 * @param Request $request
		 */
		public function onResponse(Response $response, Request $request){

		}


		/**
		 * @param Request $request
		 */
		protected function _insertCookies(Request $request){
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
		 * @param DocumentInterface $document
		 * @return ReadProcessor
		 */
		protected function _getReaderFor(DocumentInterface $document){
			if(!$this->_read_processor){
				$this->_read_processor = new Document\ReadProcessor(null,true,false,'');
			}
			return $this->_read_processor->setDocument($document);
		}

		/**
		 * @param DocumentInterface $document
		 * @return WriteProcessor
		 */
		protected function _getWriterFor(DocumentInterface $document){
			if(!$this->_write_processor){
				$this->_write_processor = new Document\WriteProcessor(null,true,false,'');
			}
			return $this->_write_processor->setDocument($document);
		}

		/**
		 * @param Request $request
		 */
		public function checkCache(Request $request){
			if($this->cache_manager){
				$this->cache_manager->checkRequest($request);
			}
		}


		/**
		 * @param Response $response
		 * @param Request $request
		 */
		public function updateCacheHeaders(Response $response, Request $request){
			if($this->cache_manager){
				$this->cache_manager->onResponseHeaders($response, $request);
			}
		}

		/**
		 * @param Request $request
		 * @param Response $response
		 */
		public function updateCache(Response $response, Request $request){
			if($this->cache_manager){
				$this->cache_manager->onResponse($response, $request);
			}
		}

	}
}

