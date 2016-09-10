<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 09.09.2016
 * Time: 16:20
 */
namespace Jungle\Application\Notification {

	/**
	 * Interface MessageAwareInterface
	 * @package Jungle\Application\Notification
	 */
	interface MessageAwareInterface{

		/**
		 * @return string
		 */
		public function getMessage();

		/**
		 * @return string
		 */
		public function getType();


	}
}

