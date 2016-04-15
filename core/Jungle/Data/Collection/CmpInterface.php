<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.03.2016
 * Time: 6:33
 */
namespace Jungle\Data\Collection {

	/**
	 * Interface CmpInterface
	 * @package Jungle\Data\Collection
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

