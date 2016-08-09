<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.08.2016
 * Time: 23:54
 */
namespace Jungle\Application\Dispatcher\Controller {

	/**
	 * Interface MetadataInterface
	 * @package Jungle\Application\Dispatcher\Controller
	 */
	interface MetadataInterface{

		/**
		 * @param $module
		 * @param $controller
		 * @param $action
		 * @return mixed
		 */
		public function getMetadata($module = null, $controller = null, $action = null);

	}
}

