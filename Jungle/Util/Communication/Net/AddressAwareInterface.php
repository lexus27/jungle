<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.10.2016
 * Time: 0:36
 */
namespace Jungle\Util\Communication\Net {

	/**
	 * Interface AddressAwareInterface
	 * @package Jungle\Util\Communication
	 */
	interface AddressAwareInterface{

		/**
		 * @return string
		 */
		public function getHost();

		/**
		 * @return int
		 */
		public function getPort();

	}
}

