<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 25.07.2016
 * Time: 22:36
 */
namespace Jungle\RegExp {

	/**
	 * Interface TypeInterface
	 * @package Jungle\RegExp
	 */
	interface TypeInterface{

		/**
		 * @param $value
		 * @param array $arguments
		 * @return mixed
		 */
		public function isValid($value, array $arguments = null);

		/**
		 * @param $value
		 * @param array|null $arguments
		 * @return string
		 */
		public function render($value, array $arguments = null);

		/**
		 * @param $value
		 * @param array|null $arguments
		 * @return mixed
		 */
		public function evaluate($value, array $arguments = null);

	}
}

