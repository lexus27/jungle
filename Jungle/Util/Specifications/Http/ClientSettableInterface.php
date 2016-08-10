<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.07.2016
 * Time: 1:25
 */
namespace Jungle\Util\Specifications\Http {

	/**
	 * Interface ClientSettableInterface
	 * @package Jungle\Util\Specifications\Http
	 */
	interface ClientSettableInterface{

		/**
		 * @param ProxyInterface $proxy
		 * @return mixed
		 */
		public function setProxy(ProxyInterface $proxy);

	}
}

