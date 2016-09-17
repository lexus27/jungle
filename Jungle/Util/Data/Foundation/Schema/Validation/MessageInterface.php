<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.09.2016
 * Time: 12:04
 */
namespace Jungle\Util\Data\Foundation\Schema\Validation {

	/**
	 * Interface MessageInterface
	 * @package Jungle\Util\Data\Foundation\Schema\Validation
	 */
	interface MessageInterface{

		/**
		 * @return mixed
		 */
		public function getType();

		/**
		 * @return mixed
		 */
		public function getField();

		/**
		 * @return string[]
		 */
		public function getAdditionFields();

		/**
		 * @return array
		 */
		public function getRules();

		/**
		 * @return mixed
		 */
		public function getMessage();

	}
}

