<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.07.2016
 * Time: 15:58
 */
namespace Jungle\Util\Specifications\Http {

	/**
	 * Interface ServerSettableInterface
	 * @package Jungle\Util\Specifications\Http
	 */
	interface ServerSettableInterface{

		/**
		 * @param $ip
		 * @return mixed
		 */
		public function setIp($ip);

		/**
		 * @param $domain
		 * @return mixed
		 */
		public function setDomain($domain);


		/**
		 * @param $port
		 * @return mixed
		 */
		public function setPort($port);

		/**
		 * @param $gateway
		 * @return mixed
		 */
		public function setGateway($gateway);

		/**
		 * @param $software
		 * @return mixed
		 */
		public function setSoftware($software);

		/**
		 * @param $protocol
		 * @return mixed
		 */
		public function setProtocol($protocol);

		/**
		 * @return mixed
		 */
		public function setTimeZone($timeZone);
		/**
		 * @param $engine
		 * @return $this
		 */
		public function setEngine($engine);
	}
}

