<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 30.05.2016
 * Time: 22:55
 */
namespace Jungle\Data\Record {

	/**
	 * Interface Exportable
	 * @package Jungle\Data\Record
	 */
	interface Exportable{

		/**
		 * @return array
		 */
		public function export();

	}
}

