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

	use Jungle\Application\Dispatcher;
	use Jungle\Application\Dispatcher\Controller\ProcessInitiatorInterface;
	use Jungle\Application\Dispatcher\Controller\ProcessInterface;

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
		 * @return string[]
		 */
		public function getControllerNames();

		/**
		 * @return string
		 */
		public function getName();

		/**
		 * @param array $properties
		 * @return mixed
		 */
		public function fromArray(array $properties);

		/**
		 * @return string
		 */
		public function getCacheDirname();

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
		 * @param array|null $reference
		 * @param array $data
		 * @param array $options
		 * @param ProcessInitiatorInterface $initiator
		 * @return ProcessInterface
		 */
		public function control($reference = null,array $data, array $options = null, ProcessInitiatorInterface $initiator = null);

		/**
		 * @return mixed
		 */
		public function getDi();


		/**
		 * @param Dispatcher $dispatcher
		 * @return mixed
		 */
		public function initialize(Dispatcher $dispatcher);

		/**
		 * @return mixed
		 */
		public function getDispatcher();

		/**
		 * @param $controller
		 * @param $action
		 * @return mixed
		 */
		public function supportPublic($controller, $action);

		/**
		 * @param $controller
		 * @param $action
		 * @return bool
		 */
		public function supportHierarchy($controller, $action);

		/**
		 * @param $controller
		 * @param $action
		 * @return bool
		 */
		public function supportFormat($controller, $action);

	}
}

