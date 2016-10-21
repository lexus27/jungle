<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.07.2016
 * Time: 23:45
 */
namespace Jungle\Util\Communication\HttpClient {
	
	use Jungle\User\AccessAuth\Auth;
	use Jungle\Util\Communication\HttpClient;
	use Jungle\Util\Communication\HttpFoundation\BrowserInterface;
	use Jungle\Util\Communication\HttpFoundation\ClientInterface;
	use Jungle\Util\Communication\HttpFoundation\RequestInterface;
	use Jungle\Util\Communication\HttpFoundation\RequestWriteInterface;
	use Jungle\Util\Communication\HttpFoundation\ResponseInterface;
	use Jungle\Util\Communication\HttpFoundation\ServerInterface;
	use Jungle\Util\Communication\Hypertext\Content\Multipart;
	use Jungle\Util\Communication\Hypertext\Document;
	use Jungle\Util\Communication\Hypertext\Document\Processor;
	use Jungle\Util\Communication\Hypertext\Document\ReadProcessor;
	use Jungle\Util\Communication\Hypertext\Document\WriteProcessor;
	use Jungle\Util\Communication\URL;
	use Jungle\Util\ContentsAwareInterface;

	/**
	 * Class Request
	 * @package Jungle\Util\Communication\HttpClient
	 */
	class Request extends Document implements RequestInterface, RequestWriteInterface, \ArrayAccess{

		use \Jungle\Util\PropContainer\PropContainerOptionTrait;

		/** @var  Response */
		protected $response;

		/** @var  Server */
		protected $server;

		/** @var  Agent */
		protected $agent;

		/** @var  Agent */
		protected $browser;

		/** @var  ClientInterface */
		protected $client;

		/** @var bool  */
		protected $cacheable = true;

		/** @var bool  */
		protected $loading = false;

		/** @var  string */
		protected $auth_type;

		/** @var  Auth */
		protected $auth;

		/** @var  int|null */
		protected $time;


		/** @var  string */
		protected $_url;

		/** @var  string */
		protected $_full_path;

		/** @var  string */
		protected $scheme = 'http';

		/** @var  string */
		protected $method = 'get';

		/** @var  string */
		protected $host;

		/** @var  string */
		protected $uri = '/';

		/** @var  string */
		protected $fragment;

		/** @var  array  */
		protected $query_parameters = [];

		/** @var  array  */
		protected $post_parameters = [];

		/** @var  array  */
		protected $cookies = [];

		/** @var bool  */
		protected $cookies_inserted = false;

		/** @var  ContentsAwareInterface[]  */
		protected $files = [];

		/**
		 * Request constructor.
		 * @param Agent $agent
		 * @param string|null $url
		 * @param null $method
		 */
		public function __construct(Agent $agent, $url = null, $method = null){
			$this->setAgent($agent);
			if($url!==null){
				if(!is_string($url) && !is_array($url)){
					throw new \InvalidArgumentException(__METHOD__. ' param $url, must be string or array');
				}
				$this->setUrl($url);
				if($method !== null){
					$this->setMethod($method);
				}
			}
			$this->time     = time();
		}

		/**
		 * @return int
		 */
		public function getTime(){
			return $this->time;
		}


		/**
		 * @param bool|true $loading
		 * @return $this
		 */
		public function setLoading($loading = true){
			$this->loading = $loading;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isLoading(){
			return $this->loading;
		}

		/**
		 * @return Response|ResponseInterface
		 */
		public function getResponse(){
			if(!$this->response){
				$this->response = new Response();
				$this->response->setRequest($this);
			}
			return $this->response;
		}

		/**
		 * @return bool
		 */
		public function isReusedResponse(){
			return $this->response->isReused();
		}

		/**
		 * @return Server|ServerInterface
		 * @throws \Exception
		 */
		public function getServer(){
			if(!$this->server){
				if(!$this->host){
					throw new \Exception('Host is not defined in request');
				}
				$server = $this->agent->getServer($this->host, $this->scheme);
				$this->server = $server;
			}
			return $this->server;
		}


		/**
		 * @param BrowserInterface|Agent $browser
		 * @return mixed
		 */
		public function setBrowser(BrowserInterface $browser){
			$this->browser = $browser;
			return $this;
		}

		/**
		 * @return Agent
		 */
		public function getBrowser(){
			return $this->browser;
		}

		/**
		 * @param ClientInterface $client
		 * @return $this
		 */
		public function setClient(ClientInterface $client){
			$this->client = $client;
			return $this;
		}

		/**
		 * @return ClientInterface
		 */
		public function getClient(){
			return $this->client;
		}

		/**
		 * @param Agent $agent
		 * @return $this
		 */
		public function setAgent(Agent $agent){
			$this->client = $agent;
			$this->browser = $agent;
			$this->agent = $agent;
			return $this;
		}

		/**
		 * @return Agent
		 */
		public function getAgent(){
			return $this->browser;
		}


		/**
		 * @return string
		 */
		public function getUserAgent(){
			return $this->browser->getUserAgent();
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
		 * @return Auth|null
		 */
		public function getAuth(){
			return $this->auth;
		}

		/**
		 * @return string
		 */
		public function getAuthType(){
			return $this->auth_type;
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
		 * @return string
		 */
		public function getMethod(){
			return $this->method;
		}



		/**
		 * @return bool
		 */
		public function isGet(){
			return $this->method?stripos($this->method,'get')!==false:true;
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
		public function isPut(){
			return stripos($this->method,'put')!==false;
		}

		/**
		 * @return mixed
		 */
		public function isConnect(){
			return stripos($this->method,'connect')!==false;
		}

		/**
		 * @return mixed
		 */
		public function isTrace(){
			return stripos($this->method,'trace')!==false;
		}

		/**
		 * @return mixed
		 */
		public function getHeaders(){
			if(!$this->cookies_inserted){
				if($this->cookies){
					$cookies = [];
					foreach($this->cookies as $key => $value){
						$cookies[] = $key.'='.$value;
					}
					$this->setHeader('Cookie', implode('; ',$cookies));
				}
				$this->cookies_inserted = true;
			}
			return $this->headers;
		}





		/**
		 * @param bool|true $secure
		 * @return $this
		 */
		public function setSecure($secure = true){
			if($secure){
				$this->setScheme('https');
			}else{
				$this->setScheme('http');
			}
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isSecure(){
			return $this->getScheme()==='https';
		}

		/**
		 * @param $scheme
		 * @return mixed
		 */
		public function setScheme($scheme = null){
			if(!$scheme)$scheme = 'http';
			else $scheme = strtolower($scheme);
			if($this->scheme!==$scheme){
				$this->scheme = $scheme;
				$this->server = null;
				$this->_url = null;
			}
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getScheme(){
			return $this->scheme;
		}

		/**
		 * @param $host
		 * @return $this
		 */
		public function setHost($host){
			if($this->host !== $host){
				$this->host = $host;
				$this->_onServerCorrupted();
			}
			return $this;
		}

		/**
		 * @return string
		 */
		public function getHost(){
			return $this->host;
		}



		/**
		 * @param $uri
		 * @return mixed
		 */
		public function setUri($uri){
			if($this->uri !== $uri){
				$this->uri = $uri?:'/';
				$this->_onUrlCorrupt();
			}
			return $this;
		}

		/**
		 * @return string
		 */
		public function getUri(){
			return $this->uri;
		}



		/**
		 * @param array $queryParams
		 * @param bool $merge
		 * @return $this
		 */
		public function setQueryParams(array $queryParams, $merge = false){
			$this->query_parameters = $merge?array_replace($this->query_parameters,$queryParams):$queryParams;
			$this->_onUrlCorrupt();
			return $this;
		}

		/**
		 * @param $name
		 * @param $value
		 * @return mixed
		 */
		public function setQuery($name, $value){
			$this->query_parameters[$name] = $value;
			$this->_onUrlCorrupt();
			return $this;
		}

		/**
		 * @param $key
		 * @param null $default
		 * @return mixed
		 */
		public function getQuery($key = null, $default = null){
			return isset($this->query_parameters[$key])?$this->query_parameters[$key]:$default;
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasQuery($key = null){
			return isset($this->query_parameters[$key]);
		}


		/**
		 * @param $fragment
		 * @return $this
		 */
		public function setFragment($fragment){
			$this->fragment = $fragment;
			$this->_onUrlCorrupt();
			return $this;
		}

		/**
		 * @return string
		 */
		public function getFragment(){
			return $this->fragment;
		}


		/**
		 * @param array|string $url
		 * @return $this
		 */
		public function setUrl($url){
			$url = URL::parseUrl($url,true);
			if(!$url['query']){
				$url['query'] = [];
			}
			$this->setHost($url['host']);
			$this->setScheme($url['scheme']);
			$this->setUri($url['path']);
			$this->setQueryParams($url['query']);
			$this->setFragment($url['fragment']);
			return $this;
		}

		/**
		 * @return string
		 */
		public function getUrl(){
			if(!$this->_url){
				$this->_url = URL::renderUrl([
					'scheme'  => $this->getScheme()?:'http',
					'host'    => $this->host,
					'path'    => $this->uri,
					'query'   => $this->query_parameters,
					'fragment'=> $this->fragment,
				]);
			}
			return $this->_url;
		}


		/**
		 * @return string
		 */
		public function getFullPath(){
			if(!$this->_full_path){
				$this->_full_path = URL::renderUrl([
					'path'    => $this->uri,
					'query'   => $this->query_parameters,
					'fragment'=> $this->fragment,
				],true,false);
			}
			return $this->_full_path;
		}





		/**
		 * @param $name
		 * @param $value
		 * @return mixed
		 */
		public function setCookie($name, $value){
			$this->cookies[$name] = $value;
			$this->cookies_inserted = false;
			return $this;
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
		 * @param $requestedWith
		 * @return mixed
		 */
		public function setRequestedWith($requestedWith){
			$this->setHeader('Requested-With',$requestedWith);
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getRequestedWith(){
			return $this->getHeader('Requested-With');
		}



		/**
		 * @return $this
		 */
		public function setAjax(){
			$this->setHeader('Requested-With','XmlHttpRequest');
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isAjax(){
			return stripos($this->getHeader('Requested-With'),'XmlHttpRequest')!==false;
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
		 * @return string|null
		 */
		public function getReferrer(){
			return $this->getHeader('Referrer');
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
		 * @return string
		 */
		public function getContentType(){
			$this->_updateContentState();
			return $this->getHeader('Content-Type');
		}






		/**
		 * @param $parameter
		 * @param null $default
		 * @return mixed
		 */
		public function getParam($parameter = null, $default = null){
			if($parameter === null){
				return $_REQUEST;
			}
			if(isset($this->post_parameters[$parameter])){
				return $this->post_parameters[$parameter];
			}elseif(isset($this->query_parameters[$parameter])){
				return $this->query_parameters[$parameter];
			}
			return $this->getObjectParam($parameter,$default);
		}

		/**
		 * @param $parameter
		 * @return bool
		 */
		public function hasParam($parameter = null){
			if($parameter===null){
				return !empty($this->post_parameters) || !empty($this->query_parameters) || $this->hasObject();
			}
			return isset($this->post_parameters[$parameter]) ||
			       isset($this->query_parameters[$parameter]) ||
			       $this->hasObjectParam($parameter);
		}

		/**
		 * @param array $post
		 * @param bool|false $merge
		 * @return $this
		 */
		public function setPostParams(array $post, $merge = false){
			$this->post_parameters = $merge?array_replace($this->post_parameters,$post):$post;
			if($this->post_parameters){
				$this->method = 'post';
			}
			$this->content = null;
			return $this;
		}

		/**
		 * @return array
		 */
		public function getPostParams(){
			return $this->post_parameters;
		}

		/**
		 * @param $name
		 * @param $value
		 * @return mixed
		 */
		public function setPost($name, $value){
			if($value instanceof ContentsAwareInterface){
				$this->files[$name] = $value;
			}else{
				$this->post_parameters[$name] = $value;
			}
			$this->method = 'post';
			$this->content = null;
			return $this;
		}
		/**
		 * @param $key
		 * @param null $default
		 * @return mixed
		 */
		public function getPost($key = null, $default = null){
			return isset($this->post_parameters[$key])?$this->post_parameters[$key]:$default;
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasPost($key = null){
			return isset($this->post_parameters[$key]);
		}


		/**
		 * @param ContentsAwareInterface[] $files
		 * @param bool $merge
		 * @return $this
		 */
		public function setFiles(array $files, $merge = false){
			if(!$merge){
				$this->files = [];
			}
			foreach($files as $key => $file){
				if(is_int($key)){
					$this->files[] = $file;
				}else{
					$this->files[$key] = $file;
				}
			}
			$this->files = array_unique($this->files,SORT_ASC);

			$this->content = null;

			return $this;
		}

		/**
		 * @param ContentsAwareInterface $file
		 * @param null $name
		 * @return $this
		 */
		public function setFile(ContentsAwareInterface $file, $name = null){
			if($name===null){
				$this->files[] = $file;
			}else{
				$this->files[$name] = $file;
			}

			$this->content = null;

			return $this;
		}

		/**
		 * @return bool
		 */
		public function hasFiles(){
			return !empty($this->files);
		}

		/**
		 * Files indexed or assoc
		 * @return \Jungle\Util\ContentsAwareInterface[]
		 */
		public function getFiles(){
			return $this->files;
		}

		/**
		 * @return bool
		 */
		public function hasObject(){
			$contentType = $this->getContentType();
			return is_array($this->content) || strpos($contentType,'json')!==false || strpos($contentType,'xml')!==false || strpos($contentType,'soap')!==false;
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
		 * @param null $default
		 * @return mixed|null
		 */
		public function getObjectParam($key, $default = null){
			if(is_array($this->content)){
				if(isset($this->content[$key])){
					return $this->content[$key];
				}
			}else{
				$content = $this->getContent();
				if(is_array($content) && isset($content[$key])){
					return $content[$key];
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
		 * @param Processor $processor
		 * @return mixed|void
		 */
		public function beforeProcessStart(Processor $processor){

			if($processor instanceof WriteProcessor){
				/** Propagation */
				$this->getAgent()->beforeRequest($this, $processor);
				$this->_updateContentState();
				/** Propagation */
				$this->getAgent()->checkCache($this);
			}
		}


		/**
		 * @param WriteProcessor $writer
		 * @return void
		 */
		public function beforeWrite(WriteProcessor $writer){
			if($this->loading){
				$this->time = time();
				$this->server->beforeRequest($this, $writer);
			}
			/** Meta Header Write */
			$writer->write(strtoupper($this->method).' '.$this->getFullPath().' ' . "{$this->server->getProtocol()}\r\n");
		}

		/**
		 * @param WriteProcessor $writer
		 * @return void
		 */
		public function continueWrite(WriteProcessor $writer){
			$this->loading = false;
		}

		/**
		 * @param ReadProcessor $reader
		 */
		public function afterRead(ReadProcessor $reader){
			$this->cache = $reader->getBuffered();
		}

		/**
		 * @return bool
		 */
		public function isSent(){
			return $this->hasOption(Agent::EXECUTION_STREAM);
		}



		/**
		 * @param mixed $offset
		 * @return mixed
		 */
		public function offsetExists($offset){
			return $this->hasHeader($offset);
		}

		/**
		 * @param mixed $offset
		 * @return mixed
		 */
		public function offsetGet($offset){
			return $this->getHeader($offset);
		}

		/**
		 * @param mixed $offset
		 * @param mixed $value
		 * @return mixed
		 */
		public function offsetSet($offset, $value){
			if(!$offset){
				throw new \InvalidArgumentException(__METHOD__.' \ArrayAccess, is applicable to headers, empty offset is invalid');
			}
			$this->setHeader($offset, $value);
		}

		/**
		 * @param mixed $offset
		 * @return mixed
		 */
		public function offsetUnset($offset){
			$this->removeHeader($offset);
		}


		protected function _onServerCorrupted(){
			$this->server       = null;
			$this->_url         = null;
			$this->_full_path   = null;
		}

		protected function _onUrlCorrupt(){
			$this->_url         = null;
			$this->_full_path   = null;
		}

		/**
		 * fresh content..
		 */
		protected function _updateContentState(){
			/** Prepare Content */
			if(!$this->content && (($hasFiles = !empty($this->files)) || $this->post_parameters)){

				$this->method = 'post';

				if(!$hasFiles){
					$this->setHeader('Content-Type','application/x-www-form-urlencoded');
					$content = http_build_query($this->post_parameters);
				}else{
					$content = new Multipart();
					$content->setMultipartType('form-data');
					if($this->post_parameters){
						foreach($this->post_parameters as $key => $value){
							$document = new Document();
							$document->setHeader('Content-Disposition', [
								'form-data', 'params' => [
									'name' => $key
								],
							]);
							$document->setContent($value);
							$content->addPart($document);
						}
					}

					if($this->files){
						foreach($this->files as $key => $file){
							$document = new Document();
							if(!is_string($key)){
								$key = 'file['.$key.']';
							}
							$document->setHeader('Content-Disposition',[
								'form-data', 'params' => [
									'name' => $key,
									'filename' => $file->getBasename()
								],
							]);
							$document->setHeader('Content-Transfer-Encoding',"base64");
							$document->setHeader('Content-Type',$file->getMediaType());
							$document->setContent($file->getContents());
							$content->addPart($document);
						}
					}
				}
				$this->content = $content;
			}
		}



	}
}

