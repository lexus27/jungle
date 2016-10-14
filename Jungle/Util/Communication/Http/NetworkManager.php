<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.10.2016
 * Time: 23:21
 */
namespace Jungle\Util\Communication\Http {

	use Jungle\Util\Communication\Http\Agent;
	use Jungle\Util\Communication\URL\Host\IP;

	/**
	 * Class NetworkManager
	 * @package Jungle\Util\Communication\Http\Agent
	 */
	class NetworkManager{

		/** @var  Agent */
		protected $agent;

		/** @var  Server[] */
		protected $servers = [];

		/**
		 * @param $host
		 * @return string
		 */
		protected function _normalizeHost($host){
			if(!IP::isIPAddress($host)){
				$e = explode('.',$host);
				if(count($e)>1){
					$e = array_reverse($e);
					if(!isset($e[2])){
						$e[2] = 'www';
					}
					return implode('.',array_reverse($e));
				}
			}
			return $host;
		}

		/**
		 * @param $host
		 * @param $scheme
		 * @return array === list($ip,$port)
		 */
		public function getIpAndPort($host, $scheme){
			return [gethostbyname($host), getservbyname($scheme,'tcp')];
		}

		/**
		 * @param $host
		 * @param $scheme
		 * @return string
		 */
		public function getServerId($host,$scheme){
			$port   = getservbyname($scheme,'tcp');
			$ip     = gethostbyname($host);
			return "$ip:$port";
		}

		/**
		 * @param $host
		 * @param $scheme
		 * @return Server
		 */
		public function getServer($host, $scheme){
			$port   = getservbyname($scheme,'tcp');
			$ip     = gethostbyname($host);
			$key    = "$ip:$port";
			if(!isset($this->servers[$key])){
				$server = $this->createServer();
				$server->setHost($host);
				$server->setPort($port);
				return $this->servers[$key] = $server;
			}else{
				return $this->servers[$key];
			}
		}


		/**
		 * @return Server
		 */
		public function createServer(){
			return new Server($this);
		}

		/**
		 * @param $host
		 * @param $port
		 * @param $transport
		 */
		public function createStream($host, $port, $transport){

		}

		/**
		 * @param $port
		 * @return string
		 */
		public static function getTransportByPort($port){
			return $port===443?'ssl':'tcp';
		}

	}
}

