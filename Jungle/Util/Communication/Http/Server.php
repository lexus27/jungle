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
		protected $host;

		/** @var  int */
		protected $port;

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
		 * @param $host
		 * @return mixed
		 */
		public function setHost($host){
			$this->host = $host;
			$this->ip = gethostbyname($host);
			return $this;
		}
		/**
		 * @return string
		 */
		public function getHost(){
			return $this->host;
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
			return $this;
		}
		/**
		 * @return string
		 */
		public function getProtocol(){
			return null;
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
	}
}

