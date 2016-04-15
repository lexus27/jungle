<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 21.03.2016
 * Time: 20:28
 */
namespace Jungle\Data\Registry {

	/**
	 * Interface RegistryReadInterface
	 * @package Jungle\Data\Collection
	 */
	interface RegistryReadInterface{

		/**
		 * @param $key
		 * @return mixed
		 */
		public function get($key);

		/**
		 * @param $key
		 * @return bool
		 */
		public function has($key);

	}
}

