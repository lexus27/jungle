<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.06.2016
 * Time: 13:44
 */
namespace Jungle\Util {

	/**
	 * Interface BrowserInterface
	 * @package Jungle\Util
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

	}
}

