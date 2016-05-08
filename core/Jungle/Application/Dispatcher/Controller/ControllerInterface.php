<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.04.2016
 * Time: 21:18
 */
namespace Jungle\Application\Dispatcher\Controller {


	/**
	 * Interface ControllerInterface
	 * @package Jungle\Application
	 */
	interface ControllerInterface{

		/**
		 * @return
		 */
		public function initialize();

		/**
		 * @param $data
		 * @param $actionName
		 * @param $controllerName
		 * @return mixed
		 */
		public function beforeControl($data, $actionName, $controllerName);

		/**
		 * @param $data
		 * @param $result
		 * @param $actionName
		 * @param $controllerName
		 * @return mixed
		 */
		public function afterControl($data, $result, $actionName, $controllerName);



	}
}

