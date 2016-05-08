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
	 * Interface ClientInterface
	 * @package Jungle\HTTPFoundation
	 */
	interface ClientInterface{

		/**
		 * @return string
		 */
		public function getIpAddress();

		/**
		 * @return BrowserInterface
		 */
		public function getBrowser();

		/**
		 * @return string
		 */
		public function getBestLanguage();

		/**
		 * @return string
		 */
		public function getBestEncoding();


	}
}

