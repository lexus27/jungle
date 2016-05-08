<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 05.05.2016
 * Time: 14:11
 */
namespace Jungle\Application\Dispatcher\Reference {

	use Jungle\Application\Dispatcher\ReferenceInterface;

	/**
	 * Interface ModularReferenceInterface
	 * @package Jungle\Application\Dispatcher\Direction
	 */
	interface ModularReferenceInterface extends ReferenceInterface{

		/**
		 * @param string $module
		 * @return $this
		 */
		public function setModule($module = null);

		/**
		 * @return string
		 */
		public function getModule();


		/**
		 * @param string $controller compatible with ([[:alpha:]][\w\-\.]*\w)
		 * char . determinate as namespace delimiter
		 * @return $this
		 */
		public function setController($controller);

		/**
		 * @return string
		 */
		public function getController();

		/**
		 * @param string $action
		 * @return $this
		 */
		public function setAction($action);

		/**
		 * @return string
		 */
		public function getAction();

	}
}

