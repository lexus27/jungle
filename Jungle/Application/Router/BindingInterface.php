<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 12.07.2016
 * Time: 6:42
 */
namespace Jungle\Application\Router {

	/**
	 * Class BindingInterface
	 * @package Jungle\Application\Router
	 */
	interface BindingInterface{

		/**
		 * @param array $params
		 * @return mixed - value, prepare after match
		 */
		public function composite(array $params);

		/**
		 * @param array $params
		 * @return array - complete parameters after composite, interceptor for remove params carrier
		 */
		public function afterComposite(array $params);

		/**
		 * @param $value
		 * @return array - value to params needle for link generation
		 */
		public function decomposite($value);

	}
}

