<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.07.2016
 * Time: 22:18
 */
namespace Jungle\Application\PublicInput {

	/**
	 * Interface QueryListenerInterface
	 * @package Jungle\Application\PublicInput
	 */
	interface QueryListenerInterface{

		public function hasParam($key);

		public function getParam($key);

	}
}

