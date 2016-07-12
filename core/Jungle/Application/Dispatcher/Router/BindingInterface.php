<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 12.07.2016
 * Time: 6:42
 */
namespace Jungle\Application\Dispatcher\Router {

	/**
	 * Class BindingInterface
	 * @package Jungle\Application\Dispatcher\Router
	 */
	interface BindingInterface{

		/**
		 * @param array $params
		 * @return mixed
		 */
		public function composite(array $params);

		/**
		 * @param array $params
		 * @return array
		 */
		public function afterComposite(array $params);

		/**
		 * @param $value
		 * @return array
		 */
		public function decomposite($value);

	}
}

