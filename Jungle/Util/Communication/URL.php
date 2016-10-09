<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 06.01.2016
 * Time: 20:10
 */
namespace Jungle\Util\Communication {

	use Jungle\Util\Communication\URL\Host\IP;
	use Jungle\Util\Smart\Value\Value;

	/**
	 * Class Url
	 * @package Jungle\Util\Communication
	 */
	class URL extends Value{

		const V_SCHEME   = 'scheme';
		const V_USER     = 'user';
		const V_PASS     = 'pass';
		const V_HOST     = 'host';
		const V_PORT     = 'port';
		const V_PATH     = 'path';
		const V_QUERY    = 'query';
		const V_FRAGMENT = 'fragment';

		protected static $default_value = [
			self::V_SCHEME   => null,
			self::V_USER     => null,
			self::V_PASS     => null,
			self::V_HOST     => null,
			self::V_PORT     => null,
			self::V_PATH     => null,
			self::V_QUERY    => null,
			self::V_FRAGMENT => null
		];

		/**
		 * @param $index
		 * @param $newValue
		 * @return $this
		 */
		protected function manipulate($index, $newValue){
			$raw = $this->getRaw();
			$raw[$index] = $newValue;
			$this->setValue($raw);
			return $this;
		}

		/**
		 * @param $scheme
		 * @return $this
		 */
		public function setScheme($scheme){
			return $this->manipulate(self::V_SCHEME,$scheme);
		}

		/**
		 * @return IP
		 */
		public function getIp(){
			return gethostbyname($this->value[self::V_HOST]);
		}

		/**
		 * @return string
		 */
		public function getDomain(){
			return gethostbyaddr($this->value[self::V_HOST]);
		}

		/**
		 * @return string
		 */
		public function getScheme(){
			return $this->value[self::V_SCHEME];
		}


		/**
		 * @return bool
		 */
		public function hasAuth(){
			return $this->value[self::V_USER] && $this->value[self::V_PASS];
		}


		/**
		 * @param $login
		 * @return $this
		 */
		public function setUserLogin($login){
			return $this->manipulate(self::V_USER,$login);
		}


		/**
		 * @return string
		 */
		public function getUserLogin(){
			return $this->value[self::V_USER];
		}


		/**
		 * @param $password
		 * @return $this
		 */
		public function setUserPassword($password){
			return $this->manipulate(self::V_PASS,$password);
		}

		/**
		 * @return string
		 */
		public function getUserPassword(){
			return $this->value[self::V_PASS];
		}


		/**
		 * @param $host
		 * @return URL
		 */
		public function setHost($host){
			return $this->manipulate(self::V_HOST,$host);
		}

		/**
		 * @return string
		 */
		public function getHost(){
			return $this->value[self::V_HOST];
		}

		/**
		 * @param $port
		 * @return $this
		 */
		public function setPort($port){
			return $this->manipulate(self::V_PORT,intval($port));
		}
		/**
		 * @return int|null
		 */
		public function getPort(){
			return $this->value[self::V_PORT];
		}


		/**
		 * @param mixed $uri
		 * @return $this
		 */
		public function setPath($uri){
			return $this->manipulate(self::V_PATH,$uri);
		}


		/**
		 * @return \string[]
		 */
		public function getPath(){
			return $this->value[self::V_PATH];
		}

		/**
		 * @param array $parameters
		 */
		public function setQueryParams(array $parameters){
			if(!is_array($this->value[self::V_QUERY])){
				$this->value[self::V_QUERY] = [];
			}
			$this->value[self::V_QUERY] = $parameters;
		}

		/**
		 * @return array
		 */
		public function getQueryParams(){
			return is_array($this->value[self::V_QUERY])?$this->value[self::V_QUERY]:[];
		}



		/**
		 * @param $key
		 * @return null
		 */
		public function getParam($key){
			if($this->value[self::V_QUERY]){
				return isset($this->value[self::V_QUERY][$key])?$this->value[self::V_QUERY][$key]:null;
			}
			return null;
		}

		/**
		 * @param $key
		 * @return bool
		 */
		public function hasParam($key){
			if($this->value[self::V_QUERY]){
				return isset($this->value[self::V_QUERY][$key]);
			}
			return false;
		}

		/**
		 * @param $key
		 * @return $this
		 */
		public function removeParam($key){
			if($this->value[self::V_QUERY]){
				unset($this->value[self::V_QUERY][$key]);
			}
			return $this;
		}

		/**
		 * @return string
		 */
		public function getFragment(){
			return $this->value[self::V_FRAGMENT];
		}

		/**
		 * @param $url
		 * @return bool
		 */
		public function merge($url){
			if(is_string($url)){
				$this->merge(self::parseUrl($url));
			}elseif($url instanceof URL){
				$this->merge($url->toArray());
			}elseif(is_array($url)){
				foreach($this->value as $k => $value){
					if(!isset($url[$k])){
						$url[$k] = $value;
					}
				}
				$url = array_intersect_key($url,self::$default_value);
				$this->setValue($url);
			}
			return $this;
		}

		/**
		 * @param $url
		 * @return URL
		 */
		public static function getURL($url){
			if($url instanceof URL){
				return $url;
			}elseif(is_string($url)){
				$object = new URL();
				$object->setValue($url);
				return $object;
			}else{
				throw new \InvalidArgumentException(__METHOD__ . 'URL is not valid "'.$url.'"');
			}
		}

		/**
		 * @param $value
		 * @return bool
		 */
		public function setValue($value){
			if(is_string($value)){
				$value = self::parseUrl($value);
			}elseif($value instanceof URL){
				$value = $value->toArray();
			}elseif(is_array($value)){
				foreach(self::$default_value as $index => $default){
					$value[$index] = isset($value[$index])?$value[$index]:$default;
				}
				return parent::setValue($value);
			}else{
				return false;
			}
			return $this->setValue($value);
		}

		/**
		 * @param $value
		 */
		public function beforeValueSet(& $value){
			$host       = & $value[self::V_HOST];
			$port       = & $value[self::V_PORT];
			if(isset($port)){
				$port = intval($port);
			}
			if(!$host){
				throw new \LogicException('URL must have hostname(ip or domain address)');
			}
			$value[self::V_QUERY] = self::parseParams($value[self::V_QUERY]);
		}

		/**
		 * @return array
		 */
		public function toArray(){
			return $this->value;
		}

		/**
		 * @param array|null $white_list
		 * @param array $black_list
		 * @return string
		 */
		public function render(array $white_list=null,array $black_list=[]){
			$a = $this->toArray();
			if($white_list===null){
				$white_list = array_keys($a);
			}
			$black_list = array_flip($black_list);
			$white_list = array_flip($white_list);
			$white_list = array_diff_key($white_list,$black_list);
			$white_list[self::V_HOST] = 1;
			return self::renderUrl(array_intersect_key($a,$white_list));
		}

		/**
		 * @return string
		 */
		public function __toString(){
			return $this->renderUrl($this->value);
		}



















		/**
		 * @param string $uri
		 * @param bool $decode
		 * @param string $separator
		 * @return array
		 */
		public static function parseUri($uri, $decode = false, $separator = '/'){
			$uri = explode($separator,$uri);
			if($decode){
				foreach($uri as &$c){
					$c = urldecode($c);
				}
			}
			return array_filter($uri);
		}

		/**
		 * @param array $uri
		 * @param bool|false $encode
		 * @param string $separator
		 * @return string
		 */
		public static function renderPath(array $uri, $encode = false,$separator = '/'){
			if($encode){
				foreach($uri as &$chunk){
					$chunk = urlencode($chunk);
				}
			}
			return implode($separator,$uri);
		}

		/**
		 * @param $string
		 * @param bool|true $decode
		 * @param string $separator
		 * @param string $define
		 * @return array
		 */
		public static function parseParams($string, $decode = true, $separator = '&', $define = '='){
			$parameters = [];
			$pairs = explode($separator,ltrim($string,'?'));
			if($decode){
				foreach($pairs as $pair){
					list($k,$v) = explode($define,$pair);
					$parameters[urldecode($k)] = urldecode($v);
				}
			}else{
				foreach($pairs as $pair){
					list($k,$v) = explode($define,$pair);
					$parameters[$k] = $v;
				}
			}
			return $parameters;
		}

		/**
		 * @param array $params
		 * @param bool $encode
		 * @param string $separator
		 * @param string $define
		 * @return string
		 */
		public static function renderParams(array $params, $encode = false, $separator = '&', $define = '='){
			$a = [];
			if($encode){
				foreach($params as $key => $value){
					$a[] = urlencode($key).$define.urlencode($value);
				}
			}else{
				foreach($params as $key => $value){
					$a[] = $key.$define.$value;
				}
			}
			return implode($separator, $a);
		}


		/**
		 * @param $url
		 * @param bool $full
		 * @return array
		 */
		public static function parseUrl($url, $full = true){
			if(is_string($url)){
				$url = urldecode($url);
				$regex = '~(?:([\w]+)://)?(?:([\w\d]+)(?::([\w\d]+))?@)?([^\?\&/:]+|\[[^/]]+\])?(?::([\d]+))?([^?#$]+)?(?:\?([^#$]+))?(?:#([^$]+))?~u';
				$placeholders = [ self::V_SCHEME, self::V_USER, self::V_PASS, self::V_HOST, self::V_PORT, self::V_PATH, self::V_QUERY, self::V_FRAGMENT, ];
				$placeholders   = array_flip($placeholders);
				$placeholders   = array_map(function($index){return $index+1;},$placeholders);
				$matched        = preg_match($regex,$url,$matches);

				if($matched){
					$data = [];
					foreach($placeholders as $key => $maskId){
						if(array_key_exists($maskId,$matches)){
							$data[$key] = !empty($matches[$maskId])?$matches[$maskId]:null;
						}else{
							$data[$key] = null;
						}
					}
					if($data[self::V_PORT]){
						$data[self::V_PORT] = intval([self::V_PORT]);
					}
					if($data[self::V_HOST]){
						$data[self::V_HOST] = trim($data[self::V_HOST],'[]');
					}
					if($data[self::V_QUERY]){
						$data[self::V_QUERY] = self::parseParams($data[self::V_QUERY],true);
					}
					return $full?$data:array_filter($data);
				}
			}elseif(is_array($url)){
				if($full){
					return array_replace($url, array_intersect_key($url, array_flip(self::$default_value)));
				}else{
					return array_intersect_key($url, array_flip(self::$default_value));
				}
			}
			return false;
		}


		/**
		 * @param array $chunks
		 * @param bool $strict
		 * @param bool $encode
		 * @return string
		 */
		public static function renderUrl(array $chunks, $encode = false, $strict = true){
			$chunks = array_replace(self::$default_value, $chunks);
			if($strict && (!isset($chunks[self::V_HOST]) || !$chunks[self::V_HOST])){
				throw new \LogicException('Host must have in url chunks');
			}

			$url = '';
			if($chunks[self::V_SCHEME]){
				$url.= $chunks[self::V_SCHEME] . '://';
			}
			if($chunks[self::V_USER]){
				$url.= $chunks[self::V_USER] . ':' . $chunks[self::V_PASS];

				if($chunks[self::V_HOST]){
					$url.= '@';
				}
			}
			if($chunks[self::V_HOST]){
				$url.=$chunks[self::V_HOST];
			}
			if($chunks[self::V_HOST]){
				$url.=$chunks[self::V_HOST];
			}
			if($chunks[self::V_PORT]){
				$url.=':'.$chunks[self::V_PORT];
			}
			if($chunks[self::V_PATH]){
				if(is_array($chunks[self::V_PATH])){
					$url.=self::renderPath($chunks[self::V_PATH],$encode);
				}else{
					$url.=$chunks[self::V_PATH];
				}
			}
			if($chunks[self::V_QUERY]){
				if(is_array($chunks[self::V_QUERY])){
					$url.= '?'.self::renderParams($chunks[self::V_QUERY],$encode);
				}else{
					$url.= '?'.$chunks[self::V_QUERY];
				}
			}
			if($chunks[self::V_FRAGMENT]){
				$url.= $chunks[self::V_FRAGMENT];
			}
			return $url;
		}

		/**
		 * @param $url
		 * @param $default
		 * @param bool $asArray
		 * @return string
		 */
		public static function absoluteUrl($url, $default, $asArray = false){
			if(!is_array($url)){
				$url = URL::parseUrl($url);
			}
			if(!is_array($default)){
				$default = URL::parseUrl($default);
			}
			if(!$url['scheme']){
				$url['scheme'] = $default['scheme'];
			}
			if(!$url['host']){
				$url['host'] = $default['host'];
			}
			if(!$url['path']){
				$url['path'] = $default['path'];
			}else{
				if(substr($url['path'],0,2) === './'){
					$url['path'] = $default['path'] . substr($url['path'],2);
				}
			}
			return $asArray?$url:URL::renderUrl($url, true, true);
		}

		/**
		 * @param $domain
		 * @return string
		 */
		public static function getBaseDomain($domain){
			if($domain){
				$domain = explode('.',$domain);
				if(count($domain) >= 2){
					$domain = array_slice($domain,-2);
				}
				$domain = implode('.',$domain);
			}
			return $domain;
		}







	}
}

