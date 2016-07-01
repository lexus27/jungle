<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:16
 */
namespace Jungle\Util\Data\Foundation {

	/**
	 * Interface CmpInterface
	 * @package Jungle\Data
	 */
	interface CmpInterface{

		/**
		 * @param $current_value
		 * @param $selection_each
		 * @return int
		 */
		public function __invoke($current_value, $selection_each);

	}
}

