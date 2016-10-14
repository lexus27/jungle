<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 12.10.2016
 * Time: 18:32
 */
namespace Jungle\Util\Communication\Http {

	use Jungle\Util\Communication\Connection\Stream\Socket;
	use Jungle\Util\Communication\Connection\StreamInterface;
	use Jungle\Util\Communication\URL;
	use Jungle\Util\Communication\URL\Host\IP;
	use Jungle\Util\Specifications\Hypertext\Document\WriteProcessor;

	/**
	 * Class Streamer
	 * @package Jungle\Util\Communication\Http
	 */
	abstract class Streamer{

		/** @var  NetworkManager */
		protected $network_manager;

		/** @var  string */
		protected $ip;

		/** @var  string */
		protected $domain;

		/** @var  int */
		protected $port;


		/** @var  StreamInterface[] */
		protected $streams_closed = [];

		/** @var  StreamInterface[] */
		protected $streams_idle = [];

		/** @var  StreamInterface[] */
		protected $streams_pending = [];

		/** @var  int */
		protected $last_touch_time;

		/**
		 * Server constructor.
		 * @param NetworkManager $manager
		 */
		public function __construct(NetworkManager $manager){
			$this->network_manager = $manager;
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
			if(IP::isIPAddress($host)){
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
		 * @param Request $request
		 * @param WriteProcessor $writer
		 */
		public function beforeRequest(Request $request, WriteProcessor $writer){
			/** Set the EXECUTION_STREAM */
			$source = $writer->getSource();
			if($source instanceof StreamInterface){
				$request->setOption(Agent::EXECUTION_STREAM, $source);
			}

			$this->last_touch_time = time();
			$request->setHeader('Host', $this->getHost());
		}


		/**
		 * @param Response $response
		 * @param Request $request
		 */
		public function onResponse(Response $response, Request $request){
			$stream = $request->getOption(Agent::EXECUTION_STREAM);
			if($stream instanceof StreamInterface){
				$request->removeOption(Agent::EXECUTION_STREAM);
				if($response->haveHeader('Connection','close')){
					$stream->close();
				}
				$this->passStream($stream);
			}
		}

		/**
		 * @return string
		 */
		public function getId(){
			return $this->getIp().':'.$this->getPort();
		}

		/**
		 * @return string
		 */
		public function __toString(){
			return $this->getIp().':'.$this->getPort();
		}


		/**
		 * @return bool
		 */
		public function hasActiveStreams(){
			return !empty($this->streams_pending) || !empty($this->streams_idle);
		}

		/**
		 * @return bool
		 */
		public function hasClosedStreams(){
			return !empty($this->streams_closed);
		}


		/**
		 * @return bool
		 */
		public function hasIdleStreams(){
			return !empty($this->streams_idle);
		}

		/**
		 * @return bool
		 */
		public function hasPendingStreams(){
			return !empty($this->streams_pending);
		}

		/**
		 * @return StreamInterface
		 */
		public function takeStream(){
			if(!empty($this->streams_idle)){
				$stream = array_shift($this->streams_idle);
			}elseif(!empty($this->streams_closed)){
				$stream = array_shift($this->streams_closed);
				$stream = $this->_configureStream($stream);
				$stream->connect();
			}else{
				$stream = $this->_createStream();
				$stream = $this->_configureStream($stream);
				$stream->connect();
			}
			$this->streams_pending[] = $stream;
			return $stream;
		}

		/**
		 * @param StreamInterface $stream
		 * @return $this
		 */
		public function passStream(StreamInterface $stream){
			if($stream->isConnected()){
				$this->streams_idle[] = $stream;
			}else{
				$this->streams_closed[] = $stream;
			}
			$i = array_search($stream,$this->streams_pending, true);
			if($i!==false){
				array_splice($this->streams_pending,$i,1);
			}
			return $this;
		}

		/**
		 * @return int
		 */
		public function getLastTouchTime(){
			return $this->last_touch_time;
		}


		/**
		 * @return StreamInterface|Socket
		 */
		protected function _createStream(){
			return new Socket([]);
		}


		/**
		 * @param StreamInterface $stream
		 * @return StreamInterface|Socket
		 */
		protected function _configureStream(StreamInterface $stream){
			$stream->setConfig([
				'host'      => $this->getHost(),
				'port'      => $this->getPort(),
				'transport' => $this->getTransportProtocol()
			]);
			return $stream;
		}

		/**
		 * @return string
		 */
		public function getTransportProtocol(){
			return $this->port===443?'ssl':'tcp';
		}


		/**
		 * @param $port
		 * @return string
		 */
		public static function getTransportProtoByPort($port){
			return $port===443?'ssl':'tcp';
		}


	}
}

