<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 25.04.2016
 * Time: 7:47
 */
namespace Jungle\Basic {

	/**
	 * Interface INamedAlternates
	 * @package Jungle\Basic
	 */
	interface INamedAlternates extends INamed{

		/**
		 * @param $name
		 * @return bool
		 */
		public function isName($name);

	}
}

