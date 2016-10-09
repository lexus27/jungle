<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.07.2016
 * Time: 16:17
 */
namespace Jungle\Http {
	
	use Jungle\Util\Communication\URL;
	use Jungle\Util\Specifications\Http\ServerInterface;

	/**
	 * Class Server
	 * @package Jungle\Http
	 */
	class Server implements ServerInterface{
		
		/**
		 * @return string
		 */
		public function getIp(){
			return $_SERVER['SERVER_ADDR'];
		}

		/**
		 * @return mixed
		 */
		public function getDomain(){
			return $_SERVER['HTTP_HOST'];
		}

		/**
		 * @return string
		 */
		public function getDomainBase(){
			return URL::getBaseDomain($this->getDomain());
		}


		/**
		 * @return string
		 */
		public function getHost(){
			return $_SERVER['HTTP_HOST'];
		}

		/**
		 * @return int
		 */
		public function getPort(){
			return $_SERVER['SERVER_PORT'];
		}

		/**
		 * @return string
		 */
		public function getGateway(){
			return $_SERVER['GATEWAY_INTERFACE'];
		}

		/**
		 * @return string
		 */
		public function getSoftware(){
			return $_SERVER['SERVER_SOFTWARE'];
		}

		/**
		 * @return string
		 */
		public function getEngine(){
			return 'php/'.PHP_VERSION;
		}

		/**
		 * @return string
		 */
		public function getProtocol(){
			return $_SERVER['SERVER_PROTOCOL'];
		}

		/**
		 * @return string
		 */
		public function getTimeZone(){
			return isset($_SERVER['TZ'])?$_SERVER['TZ']:isset($_SERVER['TIME_ZONE'])?$_SERVER['TIME_ZONE']:null;
		}
	}
}

