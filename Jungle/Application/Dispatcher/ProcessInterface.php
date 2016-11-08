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
	use Jungle\Application\Notification\Responsible\NeedIntroduce;
	use Jungle\Application\Router\RoutingInterface;

	/**
	 * Interface ProcessInterface
	 * @package Jungle\Application
	 */
	interface ProcessInterface extends ProcessInitiatorInterface{

		const STAGE_PREPARE         = 'prepare';    //stage    processing | failure | success
		const STAGE_EXECUTE         = 'execute';    //stage    processing | failure | success
		const STAGE_DONE            = 'done';       //stage    failure | success
		const STAGE_RENDERING       = 'rendering';  //stage    failure | success
		const STAGE_COMPLETE        = 'complete';   //stage    failure | success

		const STATE_FAILURE         = 'failure';
		const STATE_SUCCESS         = 'success';
		const STATE_PROCESSING      = 'processing';

		const CALL_ROUTING          = 'routing';
		const CALL_FORWARD          = 'forward';
		const CALL_HIERARCHY        = 'hierarchy';



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
		 * @return array
		 */
		public function getMeta();


		/**
		 * @param $stage
		 * @return mixed
		 */
		public function setStage($stage);

		/**
		 * @return mixed
		 */
		public function getStage();

		/**
		 * @param $state
		 * @return mixed
		 */
		public function setState($state);

		/**
		 * @return mixed
		 */
		public function getState();


		/**
		 * @return mixed
		 */
		public function getInitiatorType();

		/**
		 * @return ProcessInterface|RoutingInterface|null
		 */
		public function getInitiator();



		/**
		 * @return RoutingInterface
		 */
		public function getRouting();

		/**
		 * @return \Jungle\Application\RouterInterface|null
		 */
		public function getRouter();

		/**
		 * @return ProcessInterface
		 */
		public function getBase();

		/**
		 * @return ProcessInterface
		 */
		public function getForwarder();


		/**
		 * @return ProcessInterface
		 */
		public function getRoot();
		/**
		 * @return ProcessInterface
		 */
		public function getParent();



		/**
		 * @return mixed
		 */
		public function getReference();

		/**
		 * @return string
		 */
		public function getReferenceString();


		/**
		 * @param $key
		 * @param $value
		 * @return mixed
		 */
		public function setParam($key, $value);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function hasParam($key);

		/**
		 * @param $key
		 * @param null $default
		 * @return mixed
		 */
		public function getParam($key, $default = null);

		/**
		 * @param $key
		 * @param bool $allowNull
		 * @throws NeedIntroduce
		 */
		public function requireParam($key, $allowNull = true);

		/**
		 * @return array
		 */
		public function getParams();






		/**
		 * @param $reference
		 * @param null $params
		 * @return mixed
		 */
		public function forward($reference, $params = null);

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
		 * @param $result
		 * @param null $stage
		 * @param null $state
		 * @return $this
		 */
		public function setResult($result, $stage = null, $state = null);

		/**
		 * @return mixed
		 */
		public function getResult();






		/**
		 * @return mixed
		 */
		public function startBuffering();
		/**
		 * @return mixed
		 */
		public function endBuffering();
		/**
		 * @return mixed
		 */
		public function getBuffered();







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
		 * @param $key
		 * @param $task
		 * @return mixed
		 */
		public function setTask($key, $task);
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
		 * @return array[]
		 */
		public function getTasks();



		/**
		 * @return bool
		 */
		public function hasErrors();












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

// TODO concept
//		const GEN_STRATEGY    = 'strategy';
//		const GEN_MODULE      = 'module';
//		const GEN_CONTROLLER  = 'controller';
//		const GEN_ACTION      = 'action';
//
//		const LINKING_USER    = 'user';
//		const LINKING_SESSION = 'session';
//
//		/**
//		 * @param $generalization
//		 * @param $linking
//		 * @return mixed
//		 */
//		public function getMemory($generalization, $linking);


	}
}

