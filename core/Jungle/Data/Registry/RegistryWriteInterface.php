<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 06.04.2016
 * Time: 18:51
 */
namespace Jungle\Data\Registry {

	/**
	 * Interface RegistryWriteInterface
	 * @package Jungle\Data\Registry
	 */
	interface RegistryWriteInterface{

		/**
		 * @param $key
		 * @param $value
		 * @return $this
		 */
		public function set($key, $value);

		/**
		 * @param $key
		 * @return $this
		 */
		public function remove($key);

	}
}

