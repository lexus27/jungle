<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.10.2016
 * Time: 12:29
 */
namespace Jungle\Util\Communication\Hypertext {

	/**
	 * Interface HeaderRegistryAwareInterface
	 * @package Jungle\Util\Communication\Hypertext
	 */
	interface HeaderRegistryAwareInterface{

		/**
		 * @return HeaderRegistryInterface
		 */
		public function getHeaderRegistry();

		/**
		 * @param HeaderRegistryInterface $header_registry
		 * @return mixed
		 */
		public function setHeaderRegistry(HeaderRegistryInterface $header_registry);

	}
}

