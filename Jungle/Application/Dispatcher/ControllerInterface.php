<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.04.2016
 * Time: 21:18
 */
namespace Jungle\Application\Dispatcher {

	/**
	 * Interface ControllerInterface
	 * @package Jungle\Application
	 */
	interface ControllerInterface{

		/**
		 * @return void
		 */
		public function initialize();

		/**
		 * @return array
		 */
		public function getDefaultMetadata();

	}
}

