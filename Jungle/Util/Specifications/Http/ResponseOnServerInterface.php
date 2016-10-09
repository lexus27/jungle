<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 04.10.2016
 * Time: 13:44
 */
namespace Jungle\Util\Specifications\Http {

	/**
	 * Interface ResponseOnServerInterface
	 * @package Jungle\Util\Specifications\Http
	 */
	interface ResponseOnServerInterface{

		/**
		 * @param null $path
		 * @param int $code
		 */
		public function sendRedirect($path = null, $code = 302);

		/**
		 * @param null $path
		 * @param int $code
		 * @return $this
		 */
		public function setRedirect($path = null, $code = 302);

	}
}

