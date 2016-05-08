<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 26.04.2016
 * Time: 23:37
 */
namespace Jungle\RegExp\Type {

	/**
	 * Interface TypeAwareInterface
	 * @package Jungle\RegExp
	 */
	interface TypeAwareInterface{

		/**
		 * @param $name
		 * @return mixed
		 */
		public function getType($name);

	}
}

