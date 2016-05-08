<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 07.05.2016
 * Time: 16:51
 */
namespace Jungle\Application\Dispatcher\Controller {
	
	use Jungle\Application\Dispatcher;
	use Jungle\Application\Dispatcher\Router\Routing;
	use Jungle\Application\Dispatcher\Router\RoutingInterface;

	/**
	 * Class Process
	 * @package Jungle\Application\Dispatcher\Controller
	 */
	class Process implements ProcessInterface, ProcessInitiatorInterface{

		/** @var  Dispatcher */
		protected $dispatcher;

		/** @var  bool */
		protected $initiator_external;

		/** @var  ProcessInitiatorInterface|RoutingInterface|ProcessInterface|null */
		protected $initiator;

		/** @var  object|ControllerInterface|ManuallyControllerInterface */
		protected $controller;

		/** @var  mixed */
		protected $reference;

		/** @var  array  */
		protected $params = [];

		/**
		 * Process constructor.
		 * @param Dispatcher $dispatcher
		 * @param object|ControllerInterface|ManuallyControllerInterface $controller
		 * @param array $params
		 * @param mixed $reference
		 * @param ProcessInitiatorInterface|RoutingInterface|ProcessInterface $initiator
		 */
		public function __construct(Dispatcher $dispatcher, $controller,array $params, $reference, ProcessInitiatorInterface $initiator = null){
			$this->dispatcher = $dispatcher;
			$this->initiator_external 	= $initiator instanceof RoutingInterface;
			$this->initiator 			= $initiator;
			$this->controller 			= $controller;
			$this->params 				= $params;
			$this->reference 			= $reference;
		}

		/**
		 * @return mixed
		 */
		public function getReference(){
			return $this->reference;
		}

		/**
		 * @return array
		 */
		public function getParams(){
			return $this->params;
		}

		/**
		 * HMVC-Architecture
		 * @return mixed
		 */
		public function isExternal(){
			return $this->initiator_external;
		}

		/**
		 * @return Routing|null
		 */
		public function getRouting(){
			return $this->initiator_external? $this->initiator : null;
		}

		/**
		 * @return ProcessInterface|null
		 */
		public function getProcess(){
			return $this->initiator_external? null : $this->initiator;
		}

		/**
		 * @return ProcessInterface|Routing|null
		 */
		public function getInitiator(){
			return $this->initiator;
		}

		/**
		 * @param $reference
		 * @param $data
		 * @return mixed
		 * @throws Dispatcher\Exception\Control
		 */
		public function call($reference, $data){
			$reference = $this->_normalizeReference($reference);
			return $this->dispatcher->control($reference, $data, $this);
		}

		/**
		 * @param $reference
		 * @param $data
		 * @return mixed
		 * @throws Dispatcher\Exception\Control
		 */
		public function callIn($reference, $data){
			$reference = $this->_normalizeReference($reference);
			$reference['module'] = $this->reference['module'];
			$reference['controller'] = $this->reference['controller'] . '.' . $reference['controller'];
			return $this->dispatcher->control($reference, $data, $this);
		}

		/**
		 * @param $action
		 * @param $data
		 * @return mixed
		 * @throws Dispatcher\Exception\Control
		 */
		public function callCurrent($action, $data){
			$reference = $this->reference;
			if(strcasecmp($reference['action'],$action)===0){
				throw new \LogicException('Executing current action not allowed');
			}
			$reference['action'] = $action;
			return $this->dispatcher->control($reference, $data, $this);
		}

		/**
		 * @param $action
		 * @param $data
		 * @return mixed
		 * @throws Dispatcher\Exception\Control
		 */
		public function callParent($data, $action = null){
			if($this->initiator instanceof Process){
				$reference = $this->initiator->reference;
				if($action!==null){
					$reference['action'] = $action;
				}
				return $this->dispatcher->control($reference, $data, $this);
			}else{
				throw new \LogicException('Call Parent: initiator is not Process');
			}
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function __get($key){
			return isset($this->params[$key])?$this->params[$key]:null;
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function __isset($key){
			return isset($this->params[$key]);
		}

		/**
		 * @param $key
		 * @param $value
		 * @return mixed
		 */
		public function __set($key, $value){
			throw new \LogicException('Not Effect');
		}

		/**
		 * @param $key
		 * @return mixed
		 */
		public function __unset($key){
			throw new \LogicException('Not Effect');
		}



		/**
		 * @param mixed $reference
		 * @return array|mixed
		 */
		protected function _normalizeReference($reference){
			if(is_string($reference)){

				if(preg_match('@(?:#(?<module>[[alpha]]\w*))?(?::(?<controller>[[:alpha:]][\w\.\-]*)(?::(?<action>[[alpha]]\w*))?)?@',$reference, $_)){
					$reference = [
							'module'		=> null,
							'controller'	=> null,
							'action'		=> null
					];

					foreach($reference as $k => $v){
						if($_[$k]){
							$reference[$k] = $_[$k];
						}
					}
				}else{
					throw new \LogicException('Wrong string reference');
				}

			}
			return $reference;
		}


	}
}

