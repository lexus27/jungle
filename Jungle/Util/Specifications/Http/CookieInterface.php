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

	use Jungle\Util\INamed;
	use Jungle\Util\Smart\Value\IValue;
	use Jungle\Util\Smart\Value\IValueSettable;
	
	/**
	 * Interface CookieInterface
	 * @package Jungle\Util\Specifications\Http
	 */
	interface CookieInterface extends CookieConfigurationInterface, IValue, IValueSettable, INamed{

		/**
		 * @return mixed
		 */
		public function isOverdue();

	}
}

