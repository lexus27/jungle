<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 10.10.2016
 * Time: 23:21
 */
namespace Jungle\Util\Communication\HttpClient {

	use Jungle\Util\Communication\HttpClient\Agent;
	use Jungle\Util\Communication\Net\Broker;

	/**
	 * Class Network
	 * @package Jungle\Util\Communication\HttpClient\Agent
	 */
	class Network extends Broker{

		/** @var  Server[] */
		protected $servers = [];

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
		protected function createServer(){
			return new Server($this);
		}


	}
}

