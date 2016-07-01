<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.06.2016
 * Time: 13:34
 */
namespace Jungle\Util\Specifications\Http {
	
	use Jungle\Util\BrowserInterface;
	use Jungle\Util\OperationSystemInterface;

	/**
	 * Interface ClientInterface
	 * @package Jungle\Util\Specifications\Http
	 */
	interface ClientInterface{

		/**
		 * @return string
		 */
		public function getIpAddress();

		/**
		 * @return int
		 */
		public function getPort();

		/**
		 * @return BrowserInterface
		 */
		public function getBrowser();

		/**
		 * @return OperationSystemInterface
		 */
		public function getOperationSystem();

		/**
		 * @return string
		 */
		public function getBestLanguage();

		/**
		 * @return array
		 */
		public function getLanguages();

	}
}