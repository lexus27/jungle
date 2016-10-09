<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 23:14
 */
namespace Jungle\Util\Communication {

	/**
	 * Interface ConnectionInterface
	 * @package Jungle\Util\Communication
	 */
	interface ConnectionInterface{

		/**
		 * @param array $config
		 * @return mixed
		 */
		public function setConfig(array $config);

		/**
		 * @return array
		 */
		public function getConfig();

		/**
		 * @return $this
		 */
		public function reconnect();

		/**
		 * @return $this
		 */
		public function connect();

		/**
		 * @return $this
		 */
		public function close();

		/**
		 * @return mixed
		 */
		public function getInternalConnection();

	}
}

