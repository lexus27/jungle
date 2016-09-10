<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.09.2016
 * Time: 16:16
 */
namespace Jungle\Util {

	/**
	 * Interface ValidationMessageInterface
	 * @package Jungle\Util
	 */
	interface ValidationMessageInterface{

		/**
		 * @return string
		 */
		public function getType();

		/**
		 * @return string
		 */
		public function getMessage();

	}
}

