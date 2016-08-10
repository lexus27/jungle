<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 21.07.2016
 * Time: 0:01
 */
namespace Jungle\Data {

	/**
	 * Interface RecordPublicInterface
	 * @package Jungle\Data
	 */
	interface RecordPublicInterface{

		/**
		 * @param string $route
		 * @param null $other_parameters
		 * @return string
		 */
		public function linkBy($route, $other_parameters = null);

		/**
		 * @param string|array $mca
		 * @param null $other_parameters
		 * @return string
		 */
		public function linkTo($mca, $other_parameters = null);

	}
}

