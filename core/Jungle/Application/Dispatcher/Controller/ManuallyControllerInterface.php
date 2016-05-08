<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.05.2016
 * Time: 17:57
 */
namespace Jungle\Application\Dispatcher\Controller {

	/**
	 * Interface ManuallyControllerInterface
	 * @package Jungle\Application\Dispatcher\Controller
	 */
	interface ManuallyControllerInterface extends ControllerInterface{

		/**
		 * @param string $actionName
		 * @return bool
		 */
		public function has($actionName);

		/**
		 * @param string $actionName
		 * @param ProcessInterface $process
		 * @return mixed
		 */
		public function call($actionName, ProcessInterface $process);



		/**
		 * Является ли контроллер приватным,
		 * такой контроллер разрешается использовать только из другого контроллера
		 * @return bool
		 */
		public function isPrivate();

		/**
		 * Является форматирующим, берет на себя контроль форматирования вывода
		 * @return bool
		 */
		public function isFormatter();


	}
}

