<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 06.01.2016
 * Time: 20:10
 */
namespace Jungle\Util\Communication {

	use Jungle\User\AccessAuth\Auth;
	use Jungle\Util\Communication\URL\Host;
	use Jungle\Util\Communication\URL\Host\Domain;
	use Jungle\Util\Communication\URL\Host\IP;
	use Jungle\Util\Communication\URL\Port;
	use Jungle\Util\Communication\URL\Scheme;
	use Jungle\Util\Smart\Value\Value;

	/**
	 * Class Url
	 * @package Jungle\Util\Communication
	 * @TODO AccessAuth(V_AUTH) correct with Login and Password self parameters
	 */
	class URL extends Value{

		const V_SCHEME      = 'scheme';
		const V_LOGIN       = 'login';
		const V_PASSWORD    = 'password';
		const V_AUTH        = 'auth';
		const V_HOST        = 'host';
		const V_PORT        = 'port';
		const V_URI         = 'uri';
		const V_PARAMETERS  = 'parameters';
		const V_ANCHOR      = 'anchor';

		protected static $default_value = [
			self::V_SCHEME     => null,
			self::V_LOGIN      => null,
			self::V_PASSWORD   => null,
			self::V_AUTH       => null,
			self::V_HOST       => null,
			self::V_PORT       => 0,
			self::V_URI        => [],
			self::V_PARAMETERS => [],
			self::V_ANCHOR     => null
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
		public function getHostIP(){
			/** @var Host $host */
			$host = $this->value[self::V_HOST];
			return $host?$host->getIP():null;
		}

		/**
		 * @return Domain
		 */
		public function getHostDomain(){
			/** @var Host $host */
			$host = $this->value[self::V_HOST];
			return $host?$host->getDomain():null;
		}

		/**
		 * @return Scheme|string
		 */
		public function getScheme(){
			return $this->value[self::V_SCHEME];
		}


		/**
		 * @return bool
		 */
		public function hasAuth(){
			return $this->value[self::V_LOGIN] && $this->value[self::V_PASSWORD];
		}


		/**
		 * @param $login
		 * @return $this
		 */
		public function setLogin($login){
			return $this->manipulate(self::V_LOGIN,$login);
		}


		/**
		 * @return string
		 */
		public function getLogin(){
			return $this->value[self::V_LOGIN];
		}


		/**
		 * @param $password
		 * @return $this
		 */
		public function setPassword($password){
			return $this->manipulate(self::V_PASSWORD,$password);
		}

		/**
		 * @return string
		 */
		public function getPassword(){
			$auth = $this->getAccessAuth();
			return $auth?$auth->getPassword():$this->value[self::V_PASSWORD];
		}

		/**
		 * @return Auth|null
		 */
		public function getAccessAuth(){
			return $this->value[self::V_AUTH];
		}


		/**
		 * @param $host
		 * @return URL
		 */
		public function setHost($host){
			return $this->manipulate(self::V_HOST,Host::get($host));
		}

		/**
		 * @return Host
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
		 * @return Port|int
		 */
		public function getPort(){
			return $this->value[self::V_PORT];
		}


		/**
		 * @param mixed $uri
		 * @param bool $decode
		 * @return $this
		 */
		public function setUri($uri,$decode = false){
			if(is_string($uri)){
				$uri = self::parseUri($uri,$decode);
			}
			return $this->manipulate(self::V_URI,(array)$uri);
		}


		/**
		 * @return \string[]
		 */
		public function getUri(){
			return $this->value[self::V_URI];
		}

		/**
		 * @param array $parameters
		 */
		public function setParameters(array $parameters){
			if(!is_array($this->value[self::V_PARAMETERS])){
				$this->value[self::V_PARAMETERS] = [];
			}
			$this->value[self::V_PARAMETERS] = $parameters;
		}

		/**
		 * @return array
		 */
		public function getParameters(){
			return is_array($this->value[self::V_PARAMETERS])?$this->value[self::V_PARAMETERS]:[];
		}



		/**
		 * @param $key
		 * @return null
		 */
		public function getParam($key){
			if($this->value[self::V_PARAMETERS]){
				return isset($this->value[self::V_PARAMETERS][$key])?$this->value[self::V_PARAMETERS][$key]:null;
			}
			return null;
		}

		/**
		 * @param $key
		 * @return bool
		 */
		public function hasParam($key){
			if($this->value[self::V_PARAMETERS]){
				return isset($this->value[self::V_PARAMETERS][$key]);
			}
			return false;
		}

		/**
		 * @param $key
		 * @return $this
		 */
		public function removeParam($key){
			if($this->value[self::V_PARAMETERS]){
				unset($this->value[self::V_PARAMETERS][$key]);
			}
			return $this;
		}

		/**
		 * @return string
		 */
		public function getAnchor(){
			return $this->value[self::V_ANCHOR];
		}



		/**
		 * @param $url
		 * @return array
		 */
		public static function parseURLString($url){
			if(is_string($url)){
				$url = urldecode($url);
				$regex = '~(?:([\w]+)://)?(?:([\w\d]+)(?::([\w\d]+))?@)?([^/:]+)(?::([\d]+))?([^?]+)?(?:\?([^#]+))?(?:#([\w\d@]+))?~';
				$placeholders = [
					self::V_SCHEME,
					self::V_LOGIN,
					self::V_PASSWORD,
					self::V_HOST,
					self::V_PORT,
					self::V_URI,
					self::V_PARAMETERS,
					self::V_ANCHOR,
				];
				$placeholders   = array_flip($placeholders);
				$placeholders   = array_map(function($index){return $index+1;},$placeholders);
				$matched        = preg_match($regex,$url,$matches);

				if($matched){
					$data = [];
					foreach($placeholders as $key => $maskId){
						$data[$key] = $matches[$maskId];
					}

					$a = [];

					$a[self::V_SCHEME]      = $data[self::V_SCHEME];
					$a[self::V_LOGIN]       = $data[self::V_LOGIN];
					$a[self::V_PASSWORD]    = $data[self::V_PASSWORD];
					if($a[self::V_PASSWORD] && ($decoded = @urldecode($a[self::V_PASSWORD]))){
						$a[self::V_PASSWORD] = $decoded;
					}


					$a[self::V_HOST]        = trim($data[self::V_HOST],'[]');
					$a[self::V_PORT]        = $data[self::V_PORT];
					$a[self::V_PORT]        = intval($a[self::V_PORT]);

					$a[self::V_URI]         = self::parseUri($data[self::V_URI]);
					$a[self::V_PARAMETERS]  = self::parseParameters($data[self::V_PARAMETERS]);
					$a[self::V_ANCHOR]      = $data[self::V_ANCHOR];

					return $a;
				}
			}
			return false;
		}

		/**
		 * @param $uri
		 * @param bool $decode
		 * @return array
		 */
		public static function parseUri($uri,$decode = false){
			if(is_array($uri))return array_filter($uri);
			elseif(is_string($uri)){
				$uri = explode('/',preg_replace('@[/]+@','/',trim($uri,'/')));
				if($decode) foreach($uri as &$c){$c = urldecode($c);}
				return self::parseUri($uri);
			}else{
				return [];
			}
		}

		/**
		 * @param $uri
		 * @param bool|false $encode
		 * @return string
		 */
		public static function renderUri($uri,$encode = false){
			return implode('/',array_map(function($v) use ($encode) {return urlencode($v);},array_filter($uri)));
		}

		/**
		 * @param $params
		 * @param bool $decode
		 * @return array
		 */
		public static function parseParameters($params,$decode = false){
			if(is_string($params)){
				$parameters = [];
				array_map(function($paramPair) use($parameters,$decode){
					list($k,$v) = explode('=',$paramPair);
					if($decode){
						$parameters[urldecode($k)] = urldecode($v);
					}else{
						$parameters[$k] = $v;
					}
				},explode('&',$params));
				return $parameters;
			}elseif(is_array($params)){
				return $params;
			}else{
				return [];
			}
		}

		/**
		 * @param array $parameters
		 * @param bool $encode
		 * @return string
		 */
		public static function renderParameters(array $parameters = [],$encode = false){
			return implode('&',array_map(function($k,$v) use($encode){return $encode?(urlencode($k).'='.urlencode($v)):($k.'='.$v);},array_keys($parameters),$parameters));
		}

		/**
		 * @param $url
		 * @return bool
		 */
		public function merge($url){
			if(is_string($url)){
				$this->merge(self::parseURLString($url));
			}elseif($url instanceof URL){
				$this->merge($url->toArray());
			}elseif(is_array($url)){
				$v = [];
				if(isset($url[self::V_SCHEME])){     $v[self::V_SCHEME]     = $url[self::V_SCHEME];}
				if(isset($url[self::V_LOGIN])){      $v[self::V_LOGIN]      = $url[self::V_LOGIN];}
				if(isset($url[self::V_PASSWORD])){   $v[self::V_PASSWORD]   = $url[self::V_PASSWORD];}
				if(isset($url[self::V_HOST])){       $v[self::V_HOST]       = $url[self::V_HOST];}
				if(isset($url[self::V_PORT])){       $v[self::V_PORT]       = $url[self::V_PORT];}
				if(isset($url[self::V_URI])){        $v[self::V_URI]        = $url[self::V_URI];}
				if(isset($url[self::V_PARAMETERS])){ $v[self::V_PARAMETERS] = $url[self::V_PARAMETERS];}
				if(isset($url[self::V_ANCHOR])){     $v[self::V_ANCHOR]     = $url[self::V_ANCHOR];}
				foreach($this->value as $k => $value){
					if(!isset($v[$k]) || !$v[$k]){
						$v[$k] = $value;
					}
				}
				$this->setValue($v);
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
				$u = new URL();
				$u->setValue($url);
				return $u;
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
				$value = self::parseURLString($value);
			}elseif($value instanceof URL){
				$value = $value->toArray();
			}elseif(is_array($value)){
				foreach(self::$default_value as $index => $defaultValue){
					$value[$index] = isset($value[$index])?$value[$index]:$defaultValue;
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
			$scheme     = & $value[self::V_SCHEME];

			$port = intval($port);

			/** @var URL\Manager $manager */
			$manager = URL\Manager::getDefault();
			/** @var Port|null $port */



			$port = $port? $manager->getPool('PortPool')->get($port):null;

			/** @var Scheme|null $scheme */
			$scheme = $scheme? $manager->getPool('SchemePool')->get($scheme):null;

			if($scheme){
				if(!$port){
					$port = $scheme->getDefaultPort();
				}else{
					if(!$scheme->isAllowedPort($port->getIdentifier())){
						$scheme->errorWrongPort($port);
					}
				}
			}elseif($port){
				$scheme = $port->getDefaultScheme();
			}

			if($host){
				$host = Host::get($host);
			}else{
				throw new \LogicException('URL must have hostname(ip or domain address)');
			}

			$value[self::V_URI]         = self::parseUri($value[self::V_PORT]);
			$value[self::V_PARAMETERS]  = self::parseParameters($value[self::V_PARAMETERS]);
		}

		/**
		 * @return array
		 */
		public function toArray(){
			return $this->value;
		}

		/**
		 * @param array $chunks
		 * @return string
		 */
		public static function renderURLFromArray(array $chunks){
			$chunks[self::V_SCHEME]     = $chunks[self::V_SCHEME]       ?:null;
			$chunks[self::V_LOGIN]      = $chunks[self::V_LOGIN]        ?:null;
			$chunks[self::V_PASSWORD]   = $chunks[self::V_PASSWORD]     ?:null;
			$chunks[self::V_HOST]       = $chunks[self::V_HOST]         ?:null;
			$chunks[self::V_PORT]       = $chunks[self::V_PORT]         ?:null;
			$chunks[self::V_URI]        = $chunks[self::V_URI]          ?:null;
			$chunks[self::V_PARAMETERS] = $chunks[self::V_PARAMETERS]   ?:[];
			$chunks[self::V_ANCHOR]     = $chunks[self::V_ANCHOR]       ?:null;
			if(!$chunks[self::V_HOST]){
				throw new \LogicException('Host must have in url chunks');
			}

			/** @var Scheme $scheme */
			$scheme = $chunks[self::V_SCHEME];
			return ($scheme?$scheme . '://' :'') .
			       ($chunks[self::V_LOGIN] && $chunks[self::V_PASSWORD] ?$chunks[self::V_LOGIN] .':'. $chunks[self::V_PASSWORD]. '@': '') .
			       $chunks[self::V_HOST] . ($scheme && $scheme->getPortRender() || !$scheme?($chunks[self::V_PORT]?':'.$chunks[self::V_PORT]:''):'') .
			       ($chunks[self::V_URI]?        '/' . self::renderUri($chunks[self::V_URI]):'') .
			       ($chunks[self::V_PARAMETERS]? '?' . self::renderParameters($chunks[self::V_PARAMETERS]) : '') .
			       ($chunks[self::V_ANCHOR]?     '#' . $chunks[self::V_ANCHOR]:'');

		}

		/**
		 * @return string
		 */
		public function __toString(){
			return $this->renderURLFromArray($this->value);
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
			return self::renderURLFromArray(array_intersect_key($a,$white_list));
		}

	}
}

