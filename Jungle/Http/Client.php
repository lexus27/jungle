<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.06.2016
 * Time: 0:12
 */
namespace Jungle\Http {

	use Jungle\Util\Specifications\Http\ClientInterface;

	/**
	 * Class Client
	 * @package Jungle\HTTPFoundation
	 */
	class Client implements ClientInterface{

		/** @var  string */
		protected $ip;

		/** @var  int */
		protected $port;

		/**
		 * @return string
		 */
		public function getIp(){
			return $_SERVER['REMOTE_ADDR'];
		}

		/**
		 * @return string
		 */
		public function getDomain(){
			return $_SERVER['REMOTE_HOST'];
		}

		/**
		 * @return string
		 */
		public function getHost(){
			return $_SERVER['REMOTE_HOST'];
		}


		/**
		 * @return int
		 */
		public function getPort(){
			return $_SERVER['REMOTE_PORT'];
		}

		public function isProxied(){
			// TODO: Implement isProxied() method.
		}

		public function getProxy(){
			// TODO: Implement getProxy() method.
		}

	}
}

