<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.04.2016
 * Time: 21:19
 */
namespace Jungle\HTTPFoundation {

	/**
	 * Interface BrowserInterface
	 * @package Jungle\HTTPFoundation
	 */
	interface BrowserInterface{

		/**
		 * @return string
		 */
		public function getName();

		/**
		 * @return string
		 */
		public function getVersion();

		/**
		 * @return string
		 */
		public function getEngine();

		/**
		 * @return string
		 */
		public function getPlatformName();

		/**
		 * @return string
		 */
		public function getPlatformVersion();

		/**
		 * @return string
		 */
		public function getPlatformCapacity();
		
	}
}

