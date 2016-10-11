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
	use Jungle\Util\Communication\Http;
	use Jungle\Util\Communication\URL;
	use Jungle\Util\ContentsAwareInterface;
	use Jungle\Util\Specifications\Http\BrowserInterface;
	use Jungle\Util\Specifications\Http\ClientInterface;
	use Jungle\Util\Specifications\Http\RequestInterface;
	use Jungle\Util\Specifications\Http\RequestSettableInterface;
	use Jungle\Util\Specifications\Http\ResponseInterface;
	use Jungle\Util\Specifications\Http\ServerInterface;
	use Jungle\Util\Specifications\Hypertext\Content\Multipart;
	use Jungle\Util\Specifications\Hypertext\Document;
	use Jungle\Util\Specifications\Hypertext\Document\Processor;
	use Jungle\Util\Specifications\Hypertext\Document\ReadProcessor;
	use Jungle\Util\Specifications\Hypertext\Document\WriteProcessor;

	/**
	 * Class Request
	 * @package Jungle\Util\Communication\Http
	 */
	class Request extends Document implements RequestInterface, RequestSettableInterface, \ArrayAccess{

		/** @var  Response */
		protected $response;

		/** @var  Server */
		protected $server;

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
		protected $scheme = 'http';

		/** @var  string */
		protected $method = 'get';

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

		/** @var array  */
		protected $options = [];
		protected $host;

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
		 * @param $key
		 * @param $value
		 * @return $this
		 */
		public function setOption($key, $value){
			$this->options[$key] = $value;
			return $this;
		}

		/**
		 * @param $key
		 * @param $default
		 * @return mixed
		 */
		public function getOption($key, $default=null){
			return isset($this->options[$key])?$this->options[$key]:$default;
		}

		/**
		 * @param $key
		 * @return $this
		 */
		public function removeOption($key){
			unset($this->options[$key]);
			return $this;
		}

		/**
		 * @param array $options
		 * @param bool|false $merge
		 * @return $this
		 */
		public function setOptions(array $options = [ ], $merge = false){
			$this->options = $merge?array_merge($this->options, $options):$options;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getTime(){
			return $this->time;
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
		 * @return Server|ServerInterface
		 */
		public function getServer(){
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
		 */
		public function setAgent(Agent $agent){
			$this->client = $agent;
			$this->browser = $agent;
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
		 * @param $headerKey
		 * @return bool
		 */
		public function hasHeader($headerKey){
			return isset($this->headers[$headerKey]);
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
		 * @param array $queryParams
		 * @param bool $merge
		 * @return $this
		 */
		public function setQueryParams(array $queryParams, $merge = false){
			$this->query_parameters = $merge?array_replace($this->query_parameters,$queryParams):$queryParams;
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
		 * @param array $post
		 * @param bool|false $merge
		 * @return $this
		 */
		public function setPostParams(array $post, $merge = false){
			$this->post_parameters = $merge?array_replace($this->post_parameters,$post):$post;
			if($this->post_parameters){
				$this->method = 'post';
			}
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
			$this->post_parameters[$name] = $value;
			$this->method = 'post';
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
		 * @param $uri
		 * @return mixed
		 */
		public function setUri($uri){
			$this->uri = $uri;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getUri(){
			if(!$this->uri){
				return '/';
			}
			return $this->uri;
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
			if($this->hasFiles()){
				return 'multipart/form-data';
			}elseif($this->isPost()){
				return 'application/x-www-form-urlencoded';
			}
			return $this->getHeader('Content-Type');
		}



		/**
		 * @return bool
		 */
		public function isAjax(){
			return stripos($this->getHeader('Requested-With'),'XmlHttpRequest')!==false;
		}



		/**
		 * @param null $content
		 * @return mixed
		 */
		public function setContent($content = null){
			$this->content = $content;
		}


		/**
		 * @return mixed
		 */
		public function getContent(){
			return $this->content;
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
		 * @param $scheme
		 * @return mixed
		 */
		public function setScheme($scheme = 'http'){
			$this->scheme = $scheme;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getScheme(){
			if(!$this->scheme){
				return 'http';
			}
			return $this->scheme;
		}

		/**
		 * @param array|string $url
		 * @return $this
		 */
		public function setUrl($url){
			$url = URL::parseUrl($url,true);
			if(!$url['scheme']){
				$url['scheme'] = 'http';
			}
			if(!$url['path']){
				$url['path'] = '/';
			}
			if(!$url['query']){
				$url['query'] = [];
			}

			$this->host = $url['host'];
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
			return URL::renderUrl([
				'scheme'  => $this->getScheme()?:'http',
				'host'    => $this->host,
				'path'    => $this->getUri(),
				'query'   => $this->query_parameters,
				'fragment'=> $this->fragment,
			]);
		}

		/**
		 * @return string
		 */
		public function getFullPath(){
			return URL::renderUrl([
				'path'    => $this->getUri(),
				'query'   => $this->query_parameters,
				'fragment'=> $this->fragment,
			],true,false);
		}

		/**
		 * @param $fragment
		 * @return $this
		 */
		public function setFragment($fragment){
			$this->fragment = $fragment;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getFragment(){
			return $this->fragment;
		}

		/**
		 * @param Processor $processor
		 * @return mixed|void
		 */
		public function beforeProcessStart(Processor $processor){

			if($processor instanceof WriteProcessor){
				/** Propagation */
				$this->getAgent()->beforeRequest($this, $processor);



				/** Prepare Content */
				if(!$this->content && (($hasFiles = !empty($this->files)) || $this->post_parameters)){

					$this->method = 'post';

					if(!$hasFiles){
						$this->setHeader('Content-Type','application/x-www-form-urlencoded');
						$content = URL::renderParams($this->post_parameters);
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
				/** Propagation */
				$this->getAgent()->checkCache($this);
			}
		}


		/**
		 * @param WriteProcessor $writer
		 * @return void
		 */
		public function beforeWrite(WriteProcessor $writer){
			$this->server->beforeRequest($this, $writer);
			/** Meta Header Write */
			$writer->write(strtoupper($this->method).' '.$this->getFullPath().' ' . "{$this->server->getProtocol()}\r\n");
		}

		/**
		 * @param WriteProcessor $writer
		 * @return mixed|void
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


		/**
		 * @return string
		 * @throws \Exception
		 */
		protected function render(){
			$writer = $this->getWriteProcessor();
			$writer->setDocument($this);
			$writer->setBuffer(null);
			$source = $writer->process('');
			if($this->cacheable){
				$this->cache = (string)$source;
			}
			return $source;
		}

		protected $response_reused = false;

		/**
		 * @return bool
		 */
		public function isReusedResponse(){
			return $this->response_reused;
		}

		/**
		 * @param bool|true $reused
		 * @return $this
		 */
		public function setReusedResponse($reused = true){
			$this->response_reused = $reused;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isSent(){
			return isset($this->options[Agent::EXECUTION_STREAM]);
		}


	}
}

