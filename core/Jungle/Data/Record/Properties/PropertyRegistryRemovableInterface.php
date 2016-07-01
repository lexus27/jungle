<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.05.2016
 * Time: 20:17
 */
namespace Jungle\Data\Record\Properties {

	/**
	 * Interface PropertyRegistryRemovableInterface
	 * @package Jungle\Data\Record
	 */
	interface PropertyRegistryRemovableInterface{

		/**
		 * @param $key
		 * @return mixed
		 */
		public function removeProperty($key);

	}
}

