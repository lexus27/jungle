<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.05.2016
 * Time: 12:54
 */
namespace Jungle\Application\Dispatcher {

	use Jungle\Di\Injectable;

	/**
	 * Class Controller
	 * @package Jungle\Application
	 */
	abstract class Controller extends Injectable implements ControllerInterface{

		/**
		 * @return void
		 */
		public function initialize(){}

		/**
		 * @return array
		 */
		public function getDefaultMetadata(){
			return [];
		}
	}

}

