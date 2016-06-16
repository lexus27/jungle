<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:45
 */
namespace Jungle\Data\Foundation\Schema\OuterInteraction\ValueAccessor {

	/**
	 * Interface GetterInterface
	 * @package Jungle\Data\Foundation\Schema\OuterInteraction\ValueAccesor
	 */
	interface GetterInterface{

		/**
		 * @param $data
		 * @param $key
		 * @return mixed
		 */
		public function __invoke($data, $key);

	}
}

