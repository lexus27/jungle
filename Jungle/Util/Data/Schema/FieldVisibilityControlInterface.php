<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 18:55
 */
namespace Jungle\Util\Data\Schema {

	/**
	 * Interface FieldVisibilityControlInterface
	 * @package Jungle\Util\Data\Schema
	 */
	interface FieldVisibilityControlInterface{

		/**
		 * @return bool
		 */
		public function isReadonly();

		/**
		 * @return bool
		 */
		public function isPrivate();

	}
}

