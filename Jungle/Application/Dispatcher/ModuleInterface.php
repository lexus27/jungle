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
	use Jungle\Di\DiInterface;

	/**
	 * Interface ModuleInterface
	 * @package Jungle\Application\Dispatcher
	 */
	interface ModuleInterface extends DiInterface{

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
		 * @param $controllerName
		 * @param $actionName
		 * @return bool
		 */
		public function hasControl($controllerName, $actionName);

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
		 * @return array
		 */
		public function getDefaultMetadata();

		/**
		 * @param array $metadata
		 * @return $this
		 */
		public function setDefaultMetadata(array $metadata);

		/**
		 * @param $controllerName
		 * @param $actionName
		 * @return array
		 */
		public function getMetadata($controllerName, $actionName);

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
		 * @param array $params
		 * @param \Jungle\Application\Dispatcher\ProcessInitiatorInterface $initiator
		 * @param $initiator_type
		 * @param null $forwarder
		 * @param array $options
		 * @return ProcessInterface
		 */
		public function control(array $reference,array $params, ProcessInitiatorInterface $initiator, $initiator_type,ProcessInitiatorInterface $forwarder = null, array $options = null);

		/**
		 * @return mixed
		 */
		public function getDi();


		/**
		 * @param Dispatcher $dispatcher
		 * @return void
		 */
		public function initialize(Dispatcher $dispatcher);

		/**
		 * @return Dispatcher
		 */
		public function getDispatcher();

		/**
		 * @return mixed
		 */
		public function getMemory();

	}
}

