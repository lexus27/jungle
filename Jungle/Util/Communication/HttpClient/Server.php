<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.07.2016
 * Time: 18:09
 */
namespace Jungle\Util\Communication\HttpClient {

	use Jungle\Util\Communication\Net\ConnectionInterface;
	use Jungle\Util\Communication\Net\Stream;
	use Jungle\Util\Communication\Stream\StreamInteractionInterface;
	use Jungle\Util\Communication\URL;
	use Jungle\Util\Communication\HttpFoundation\ServerInterface;
	use Jungle\Util\Communication\HttpFoundation\ServerSettableInterface;
	use Jungle\Util\Communication\Hypertext\Document\WriteProcessor;

	/**
	 * Class Server
	 * @package Jungle\Util\Communication\HttpClient
	 */
	class Server implements ServerInterface, ServerSettableInterface{

		/** Constant for request option key */
		const EXECUTION_STREAM = 'execution_stream';

		/** @var  Network */
		protected $network;


		/** @var  string */
		protected $ip;

		/** @var  string */
		protected $domain;

		/** @var  int */
		protected $port;


		/** @var  string */
		protected $protocol = 'HTTP/1.1';

		/** @var  string */
		protected $engine;

		/** @var  int */
		protected $last_touch_time;


		/**
		 * Server constructor.
		 * @param Network $network
		 */
		public function __construct(Network $network){
			$this->network = $network;
		}

		/**
		 * @param $ip
		 * @return mixed
		 */
		public function setIp($ip){
			$this->domain = gethostbyaddr($ip);
			$this->ip = $ip;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getIp(){
			return $this->ip;
		}

		/**
		 * @param $domain
		 * @return mixed
		 */
		public function setDomain($domain){
			$this->domain = $domain;
			$this->ip = gethostbyname($domain);
			return $this;
		}

		/**
		 * @return string
		 */
		public function getDomain(){
			return $this->domain;
		}

		/**
		 * @return mixed
		 */
		public function getDomainBase(){
			return URL::getBaseDomain($this->domain);
		}


		/**
		 * @param $host
		 * @return mixed
		 */
		public function setHost($host){
			if(@inet_pton($host)){
				$this->setIp($host);
			}else{
				$this->setDomain($host);
			}
			return $this;
		}

		/**
		 * @return string
		 */
		public function getHost(){
			if($this->domain){
				return $this->domain;
			}elseif($this->ip){
				return $this->ip;
			}
			return $this->domain;
		}

		/**
		 * @param $port
		 * @return mixed
		 */
		public function setPort($port){
			$this->port = $port;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getPort(){
			return $this->port?:80;
		}

		/**
		 * @param $gateway
		 * @return mixed
		 */
		public function setGateway($gateway){
			return $this;
		}
		/**
		 * @return string
		 */
		public function getGateway(){
			return '';
		}

		/**
		 * @param $software
		 * @return $this
		 */
		public function setSoftware($software){
			return $this;
		}

		/**
		 * @return string
		 */
		public function getSoftware(){
			return '';
		}

		/**
		 * @param $protocol
		 * @return mixed
		 */
		public function setProtocol($protocol){
			$this->protocol = $protocol;
			return $this;
		}
		/**
		 * @return string
		 */
		public function getProtocol(){
			if(!$this->protocol){
				return 'HTTP/1.1';
			}
			return $this->protocol;
		}

		/**
		 * @param $timeZone
		 * @return mixed
		 */
		public function setTimeZone($timeZone){
			return $this;
		}

		/**
		 * @return string
		 */
		public function getTimeZone(){
			return null;
		}

		/**
		 * @param $engine
		 * @return $this
		 */
		public function setEngine($engine){
			$this->engine = $engine;
			return $this;
		}
		/**
		 * @return string
		 */
		public function getEngine(){
			return $this->engine;
		}

		/**
		 * @param Request $request
		 * @param WriteProcessor $writer
		 */
		public function beforeRequest(Request $request, WriteProcessor $writer){

			// Set the EXECUTION_STREAM
			$source = $writer->getSource();
			if($source instanceof StreamInteractionInterface){
				if($source instanceof ConnectionInterface){
					//Auto reconnect if keep_alive timeout is overdue
					$expired = $source->getOption('server::keep_alive_expired');
					if($expired && ($request->getTime() >= $expired)){
						$source->reconnect();
					}
				}
				$request->setOption(Server::EXECUTION_STREAM, $source);
			}
			$this->last_touch_time = time();
			$request->setHeader('Host', $this->getHost());
		}


		/**
		 * @param Response $response
		 * @param Request $request
		 */
		public function onResponse(Response $response, Request $request){
			$stream = $request->getOption(Server::EXECUTION_STREAM);
			if($stream instanceof ConnectionInterface){
				$request->rmOption(Server::EXECUTION_STREAM);
				if($response->haveHeader('Connection','close')){
					$stream->close();
				}elseif($keepAlive = $response->getHeader('Keep-Alive')){
					// Initialize Options for reanimate connection
					$keepAlive = $this->_decompositeKeepAliveParams($keepAlive);
					if(isset($keepAlive['timeout']) && $stream instanceof Stream){
						$stream->setOption('server::keep_alive_expired', $request->getTime() + intval($keepAlive['timeout']));
					}
				}
				$this->network->pass($stream);
			}
			if($this->engine===null){
				$this->engine = $response->getHeader('Server');
			}
		}

		/**
		 * @param $keepAliveValue
		 * @return array
		 */
		protected function _decompositeKeepAliveParams($keepAliveValue){
			$keepAlive = explode(',', $keepAliveValue);
			$b = [];
			foreach($keepAlive as $v){
				list($k,$v) = explode('=',trim($v));
				$b[trim(strtolower($k))] = trim(strtolower($v));
			}
			return $b;
		}

	}
}

