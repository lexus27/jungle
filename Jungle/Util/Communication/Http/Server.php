<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.07.2016
 * Time: 18:09
 */
namespace Jungle\Util\Communication\Http {
	
	use Jungle\Util\Communication\URL;
	use Jungle\Util\Communication\URL\Host\IP;
	use Jungle\Util\Specifications\Http\RequestSettableInterface;
	use Jungle\Util\Specifications\Http\ServerInterface;
	use Jungle\Util\Specifications\Http\ServerSettableInterface;

	/**
	 * Class Server
	 * @package Jungle\Util\Communication\Http
	 */
	class Server implements ServerInterface, ServerSettableInterface{


		/** @var  string */
		protected $ip;

		/** @var  string */
		protected $domain;




		/** @var  int */
		protected $port;


		/** @var  string */
		protected $protocol;

		/** @var   */
		protected $engine;



		/**
		 * @param $ip
		 * @return mixed
		 */
		public function setIp($ip){
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
			return $this;
		}

		/**
		 * @return string
		 */
		public function getDomain(){
			if(!$this->domain && $this->ip){
				return gethostbyaddr($this->ip);
			}
			return $this->domain;
		}

		/**
		 * @return mixed
		 */
		public function getDomainBase(){
			return URL::getBaseDomain($this->getDomain());
		}


		/**
		 * @param $host
		 * @return mixed
		 */
		public function setHost($host){
			if(IP::isIPAddress($host)){
				$this->ip = $host;
			}else{
				$this->domain = $host;
			}
			return $this;
		}

		/**
		 * @return string
		 */
		public function getHost(){
			if($this->ip){
				return $this->getIp();
			}elseif($this->domain){
				return $this->getDomain();
			}
			return $this->getDomain();
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
			return null;
		}
		/**
		 * @param $software
		 * @return mixed
		 */
		public function setSoftware($software){
			return $this;
		}
		/**
		 * @return string
		 */
		public function getSoftware(){
			return null;
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
		 * @param RequestSettableInterface $request
		 */
		public function beforeRequestSend(RequestSettableInterface $request){
			$request->setHeader('Host', $this->getHost());
		}


	}
}

