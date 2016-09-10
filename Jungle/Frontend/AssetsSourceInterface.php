<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 21.08.2016
 * Time: 14:50
 */
namespace Jungle\Frontend {

	/**
	 * Interface AssetsSourceInterface
	 * @package Jungle\Frontend
	 */
	interface AssetsSourceInterface{

		/**
		 * @param $absolute_path
		 * @return mixed
		 */
		public function relative($absolute_path);

		/**
		 * @param $relative_path
		 * @return mixed
		 */
		public function absolute($relative_path);

	}
}

