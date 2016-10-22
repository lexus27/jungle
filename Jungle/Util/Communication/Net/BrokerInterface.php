<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.10.2016
 * Time: 17:44
 */
namespace Jungle\Util\Communication\Net {

	/**
	 * Interface BrokerInterface
	 * @package Jungle\Util\Communication\Net
	 */
	interface BrokerInterface{

		/**
		 * @param $host
		 * @param $port
		 * @param ConnectorInterface $connector
		 * @return Stream
		 */
		public function take($host, $port, ConnectorInterface $connector = null);


		/**
		 * @param Stream $stream
		 */
		public function pass(Stream $stream);
	}
}

