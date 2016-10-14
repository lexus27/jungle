<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 14.10.2016
 * Time: 21:15
 */
namespace Jungle\Util\Communication\Connection {

	/**
	 *
	 * Interface StreamBrokerInterface
	 * @package Jungle\Util\Communication\Connection
	 */
	interface StreamBrokerInterface{

		/**
		 * @return StreamInterface
		 */
		public function takeStream();

		/**
		 * @param StreamInterface $stream
		 * @return void
		 */
		public function passStream(StreamInterface $stream);

	}
}

