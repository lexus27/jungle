<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.06.2016
 * Time: 15:21
 */
namespace Jungle\Util\Specifications\Http {
	
	/**
	 * Interface CookieInterface
	 * @package Jungle\Util\Specifications\Http
	 */
	interface CookieInterface extends CookieConfigurationInterface{

		/**
		 * @return string
		 */
		public function getName();

		/**
		 * @param $name
		 * @return mixed
		 */
		public function setName($name);

		/**
		 * @return mixed
		 */
		public function getValue();

		/**
		 * @param $value
		 * @return mixed
		 */
		public function setValue($value);

	}
}

