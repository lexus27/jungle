<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.04.2016
 * Time: 19:56
 */
namespace Jungle\Application\Dispatcher\Controller {

	use Jungle\Application\Dispatcher\Router\Routing;

	/**
	 * Interface ProcessInterface
	 * @package Jungle\Application
	 */
	interface ProcessInterface{

		/**
		 * HMVC-Architecture
		 * @return bool
		 */
		public function isExternal();

		/**
		 * @return Routing
		 */
		public function getRouting();

		/**
		 * @return ProcessInterface
		 */
		public function getProcess();

		/**
		 * @return ProcessInterface|Routing|null
		 */
		public function getInitiator();

		/**
		 * @return mixed
		 */
		public function getReference();

		/**
		 * @return array
		 */
		public function getParams();

		/**
		 * @param $reference
		 * @param $data
		 * @return mixed
		 */
		public function call($reference, $data);

		/**
		 * @param $reference
		 * @param $data
		 * @return mixed
		 */
		public function callIn($reference, $data);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function __get($key);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function __isset($key);

		/**
		 * @param $key
		 * @param $value
		 * @return mixed
		 */
		public function __set($key, $value);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function __unset($key);

	}
}

