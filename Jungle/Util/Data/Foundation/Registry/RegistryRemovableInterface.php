<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:56
 */
namespace Jungle\Util\Data\Foundation\Registry {
	
	interface RegistryRemovableInterface{

		/**
		 * @param $key
		 * @return mixed
		 */
		public function remove($key);

	}
}

