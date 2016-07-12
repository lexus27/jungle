<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.07.2016
 * Time: 1:01
 */
namespace Jungle\Util {

	/**
	 * Interface ResponsibleNotificationInterface
	 * @package Jungle\Util
	 */
	interface ResponsibleNotificationInterface{

		public function getMessage();

		public function getType();

		public function isContainer();

	}
}

