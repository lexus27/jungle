<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.10.2016
 * Time: 14:03
 */
namespace Jungle\Util\Communication\HttpClient {

	use Jungle\Util\Communication\Http;
	use Jungle\Util\Communication\HttpClient;
	use Jungle\Util\Communication\HttpClient\CacheManager\CacheManager;
	use Jungle\Util\Communication\HttpClient\CookiesManager\CookiesManagerInterface;
	use Jungle\Util\Communication\HttpFoundation\BrowserInterface;
	use Jungle\Util\Communication\HttpFoundation\ClientInterface;
	use Jungle\Util\Communication\HttpFoundation\Cookie;
	use Jungle\Util\Communication\Hypertext\Document;
	use Jungle\Util\Communication\Hypertext\Document\WriteProcessor;
	use Jungle\Util\Contents\ContentsAwareInterface;
	use Jungle\Util\Value\UserAgent;

	/**
	 * Class Agent
	 * @package Jungle\Util\Communication\HttpClient
	 */
	class Agent extends Executor implements BrowserInterface, ClientInterface{

		/** @var  string|null */
		protected $user_agent,
				  $platform,
				  $browser,
				  $version;

		/** @var  array  */
		protected $desired_languages;

		/** @var  string */
		protected $desired_charsets;

		/** @var  array */
		protected $desired_media_types;

		/** @var  array */
		protected $default_headers;

		/** @var  CacheManager */
		protected $cache_manager;

		/** @var  CookiesManagerInterface */
		protected $cookies_manager;

		/** @var bool  */
		protected $follow_location;

		/** @var int  */
		protected $max_redirects;

		/**
		 * Agent constructor.
		 * @param null $user_agent
		 */
		public function __construct($user_agent = null){
			$this->desired_charsets     = [ini_get('default_charset')];
			$this->desired_languages    = ['ru-RU','ru','en-US','en'];
			$this->desired_media_types  = ['text/html','application/xhtml+xml','application/xml','image/webp','*/*'];
			$this->default_headers      = [];
			$this->user_agent           = $user_agent;
			$this->follow_location      = false;
			$this->max_redirects        = 10;
			if($this->user_agent){
				$this->setUserAgent($user_agent);
			}
		}

		/**
		 * @param bool|true $follow
		 * @return $this
		 */
		public function setFollowLocation($follow = true){
			$this->follow_location = $follow;
			return $this;
		}

		/**
		 * @param int $max
		 * @return $this
		 */
		public function setMaxRedirects($max = 10){
			$this->max_redirects = $max;
			return $this;
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
			return NAN;
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
			if(!$this->desired_languages) return 'en-US';
			return current($this->desired_languages);
		}

		/**
		 * @return string[]
		 */
		public function getDesiredLanguages(){
			return $this->desired_languages;
		}

		/**
		 * @return string|null
		 */
		public function getBestMediaType(){
			return current($this->desired_media_types);
		}

		/**
		 * @return array
		 */
		public function getDesiredMediaTypes(){
			return $this->desired_media_types;
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
		 * @param Request $request
		 * @param Response $response
		 * @return Response|void
		 * @throws \Exception
		 */
		protected function handleRedirects(Request $request, Response $response){
			$i = 0;
			while($response->isRedirect()){
				if($i < $this->max_redirects){
					$request = $response->getRedirectRequest();
					$request->setOption('redirect_prev_response', $response);
					$request->setOption('redirect_index', $i);
					$response = $this->execute($request);
					$i++;
				}else{
					throw new \Exception('Detect cyclic redirects');
				}
			}
			return $response;
		}

		/**
		 * @param Request $request
		 * @param callable|null $then
		 * @return Response|void
		 * @throws \Exception
		 */
		public function execute(Request $request, callable $then = null){
			$response = parent::execute($request);
			if($this->follow_location){
				$response = $this->handleRedirects($request, $response);
			}
			if($then){
				call_user_func($then, $response);
			}
			return $response;
		}

		/**
		 * @param Request $request
		 * @param callable|null $then
		 * @return bool
		 */
		public function push(Request $request, callable $then = null){
			$then && $request->setOption('on_response', [$then]);
			return parent::push($request);
		}

		/**
		 * @param Request $request
		 * @param callable|null $then
		 * @return Response|\Jungle\Util\Communication\HttpFoundation\ResponseInterface|\Jungle\Util\Communication\Hypertext\DocumentInterface|void
		 */
		public function pull(Request $request, callable $then = null){
			$then = $request->getOption('on_response',[]);
			if($then) $then[] = $then;

			$response = parent::pull($request);

			if($this->follow_location){
				$response = $this->handleRedirects($request, $response);
			}

			if($then){
				call_user_func($then, $response);
			}

			return $response;
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
		 * @param CookiesManagerInterface $cookiesManager
		 * @return $this
		 */
		public function setCookiesManager(CookiesManagerInterface $cookiesManager){
			$this->cookies_manager = $cookiesManager;
			return $this;
		}

		/**
		 * @return CookiesManagerInterface
		 */
		public function getCookiesManager(){
			return $this->cookies_manager;
		}

		/**
		 * @param Request $request
		 * @param WriteProcessor $writer
		 * @return void
		 */
		public function beforeRequest(Request $request, WriteProcessor $writer){
			foreach($this->getDefaultHeaders() as $k => $h){
				if(!$request->hasHeader($k)){
					$request->setHeader($k,$h);
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
			if($this->cookies_manager){
				$cookies = $this->cookies_manager->matchSuitable($request);
				$cookiePairs = [];
				foreach($cookies as $cookie){
					$cookiePairs[] = urlencode($cookie->getName()).'='.urlencode($cookie->getValue());
				}
				if($cookiePairs){
					$request->setHeader('Cookie',implode('; ',$cookiePairs));
				}
			}
			return null;
		}

		/**
		 * @param Response $response
		 * @param Request $request
		 * @throws Document\Exception\ProcessorEarlyException
		 */
		public function onResponseHeadersLoaded(Response $response, Request $request){
			if($this->cookies_manager){
				$cookies = $response->getCookies();
				foreach($cookies as $cookie){
					$this->cookies_manager->storeCookie($cookie);
				}
			}
			if($this->cache_manager){
				$this->cache_manager->onResponseHeaders($response,$request);
			}
		}

		public function onResponse(Response $response, Request $request){}

		/**
		 * @param Request $request
		 * @throws Document\Exception\ProcessorEarlyException
		 */
		public function checkCache(Request $request){
			if($this->cache_manager){
				$this->cache_manager->checkRequest($request);
			}
		}

		/**
		 * @param Response $response
		 * @param Request $request
		 * @throws Document\Exception\ProcessorEarlyException
		 */
		public function updateCacheHeaders(Response $response, Request $request){
			if($this->cache_manager){
				$this->cache_manager->onResponseHeaders($response, $request);
			}
		}

		/**
		 * @param Response $response
		 * @param Request $request
		 */
		public function updateCache(Response $response, Request $request){
			if($this->cache_manager){
				$this->cache_manager->onResponse($response, $request);
			}
		}




		/**
		 * @param $url
		 * @param $method
		 * @return Request
		 */
		public function createRequest($url , $method){
			return new Request($this, $url, $method);
		}

		/**
		 * @param $url
		 * @return Request
		 */
		public function createGet($url){
			return new Request($this,$url,'get');
		}

		/**
		 * @param $url
		 * @param array|ContentsAwareInterface[] $params
		 * @return Request
		 */
		public function createPost($url, array $params = null){
			$request = $this->createRequest($url,'post');
			$files = null;
			if($params){
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
			if($files){
				$request->setFiles($files);
			}
			return $request;
		}

	}
}

