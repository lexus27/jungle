<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 06.05.2016
 * Time: 0:59
 */
namespace Jungle\Application\Dispatcher {

	/**
	 * Interface ModuleInterface
	 * @package Jungle\Application\Dispatcher
	 */
	interface ModuleInterface{

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name);

		/**
		 * @return string
		 */
		public function getName();

		/**
		 * @param $namespace
		 * @return $this
		 */
		public function setControllerNamespace($namespace);

		/**
		 * @return string
		 */
		public function getControllerNamespace();

		/**
		 * @param string $controller
		 * @return $this
		 */
		public function setDefaultController($controller);

		/**
		 * @return string
		 */
		public function getDefaultController();

		/**
		 * @param string $action
		 * @return $this
		 */
		public function setDefaultAction($action);

		/**
		 * @return string
		 */
		public function getDefaultAction();



		/**
		 * @param $suffix
		 * @return mixed
		 */
		public function setControllerSuffix($suffix);

		/**
		 * @return mixed
		 */
		public function getControllerSuffix();


		/**
		 * @param $prefix
		 * @return mixed
		 */
		public function setActionSuffix($prefix);

		/**
		 * @return mixed
		 */
		public function getActionSuffix();

		/**
		 * @param $params
		 * @param array|null $reference
		 * @return mixed
		 */
		public function execute(array $params, $reference = null);

		/**
		 * @return mixed
		 */
		public function getDi();

	}
}

