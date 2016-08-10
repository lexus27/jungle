<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 18:54
 */
namespace Jungle\Util\Data\Foundation\Schema\OuterInteraction {

	/**
	 * Interface ValueAccessAwareInterface
	 * @package Jungle\Data\Schema\OuterInteraction
	 */
	interface ValueAccessAwareInterface{

		/**
		 * @param $data
		 * @param $key
		 * @return mixed
		 */
		public function valueAccessGet($data, $key);

		/**
		 * @param $data
		 * @param $key
		 * @param $value
		 * @return mixed
		 */
		public function valueAccessSet($data, $key, $value);

		/**
		 * @param $key
		 * @return bool
		 */
		public function valueAccessExists($key);

	}
}

