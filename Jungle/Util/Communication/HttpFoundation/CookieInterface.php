<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.06.2016
 * Time: 15:21
 */
namespace Jungle\Util\Communication\HttpFoundation {

	use Jungle\Util\NamedInterface;
	use Jungle\Util\Smart\Value\IValue;
	use Jungle\Util\Smart\Value\IValueSettable;
	use Jungle\Util\Communication\HttpFoundation\Cookie\ConfigurationInterface;
	
	/**
	 * Interface CookieInterface
	 * @package Jungle\Util\Communication\HttpFoundation
	 */
	interface CookieInterface extends ConfigurationInterface, IValue, IValueSettable, NamedInterface{

		/**
		 * @return mixed
		 */
		public function isOverdue();

	}
}

