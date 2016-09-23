<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.04.2016
 * Time: 19:56
 */
namespace Jungle\Application\Dispatcher {

	use Jungle\Application\Dispatcher;
	use Jungle\Application\Router\RoutingInterface;

	/**
	 * Interface ProcessInterface
	 * @package Jungle\Application
	 */
	interface ProcessInterface{

		/**
		 * @return bool
		 */
		public function hasErrors();


		/**
		 * @return Dispatcher
		 */
		public function getDispatcher();

		/**
		 * @return ModuleInterface|null
		 */
		public function getModule();

		/**
		 * @return object|ControllerInterface|ControllerManuallyInterface
		 */
		public function getController();

		/**
		 * @return string
		 */
		public function getActionName();

		/**
		 * HMVC-Architecture
		 * @return bool
		 */
		public function isExternal();

		/**
		 * @return array
		 */
		public function getMeta();

		/**
		 * @return RoutingInterface
		 */
		public function getRouting();

		/**
		 * @return ProcessInterface
		 */
		public function getRoot();

		/**
		 * @return ProcessInterface
		 */
		public function getParent();

		/**
		 * @return ProcessInterface|RoutingInterface|null
		 */
		public function getInitiator();

		/**
		 * @return mixed
		 */
		public function getReference();

		/**
		 * @return string
		 */
		public function getReferenceString();

		/**
		 * @return array
		 */
		public function getParams();

		/**
		 * @param $key
		 * @param null $default
		 * @return mixed
		 */
		public function getParam($key, $default = null);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasParam($key);

		/**
		 * @param $key
		 * @param $value
		 * @return mixed
		 */
		public function setParam($key, $value);

		/**
		 * @param $reference
		 * @param $data
		 * @return mixed
		 */
		public function call($reference, $data = null);

		/**
		 * @param $reference
		 * @param $data
		 * @return mixed
		 */
		public function callIn($reference, $data = null);

		/**
		 * @param $reference
		 * @param null $data
		 * @return mixed
		 */
		public function forward($reference, $data = null);

//
//		/**
//		 * @param $type - ['strategy'|'module'|'controller'|'action']
//		 * @param $container - ['system'|'session'|'user']
//		 * @return object
//		 */
//		public function getMemory($type, $container);
//
//		public function resetMemory($type, $container);
//
/*

        public function getStrategyMemory();

        public function getStrategySessionMemory();

        public function getStrategyUserMemory();

		public function getModuleMemory();

		public function getModuleSessionMemory();

		public function getModuleUserMemory();

		public function getControllerMemory();

		public function getControllerSessionMemory();

		public function getControllerUserMemory();

		public function getActionMemory();

		public function getActionSessionMemory();

		public function getActionUserMemory();
*/

		/**
		 * @param null $params
		 * @param null $reference
		 * @return string
		 */
		public function linkTo($reference = null, $params = null);

		/**
		 * @param $routeName
		 * @param null $params
		 * @param null $reference
		 * @return string
		 */
		public function linkBy($routeName, $params = null, $reference = null);

		/**
		 * @param $params
		 * @return string
		 */
		public function linkMain($params);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function __get($key);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function __isset($key);

		/**
		 * @param $key
		 * @param $value
		 * @return mixed
		 */
		public function __set($key, $value);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function __unset($key);

		/**
		 * @return bool
		 */
		public function isCompleted();

		/**
		 * @param bool|true $completed
		 * @return $this
		 */
		public function setCompleted($completed = true);

		/**
		 * @param $result
		 * @param bool $completed
		 * @return mixed
		 */
		public function setResult($result,$completed = true);

		/**
		 * @return mixed
		 */
		public function getResult();


		/**
		 * @return mixed
		 */
		public function startOutputBuffering();

		/**
		 * @return mixed
		 */
		public function endOutputBuffering();

		/**
		 * @return mixed
		 */
		public function getOutputBuffer();



		/**
		 * @param array $options
		 * @param bool|false $merge
		 * @return $this
		 */
		public function setOptions(array $options = [], $merge = false);


		/**
		 * @return array
		 */
		public function getOptions();


		/**
		 * @param $key
		 * @param $value
		 * @return $this
		 */
		public function setOption($key, $value);

		/**
		 * @param $key
		 * @param null|mixed $default
		 * @return mixed
		 */
		public function getOption($key, $default = null);

		/**
		 * @param $key
		 * @return bool
		 */
		public function hasOption($key);

		/**
		 * @return bool
		 */
		public function hasTasks();

		/**
		 * @param $key
		 * @return mixed
		 */
		public function getTask($key);

		/**
		 * @param $key
		 * @param $task
		 * @return mixed
		 */
		public function setTask($key, $task);

		/**
		 * @param bool|true $rendering
		 * @return $this
		 */
		public function setRendering($rendering = true);

		/**
		 * @return bool
		 */
		public function isRendering();

	}
}

