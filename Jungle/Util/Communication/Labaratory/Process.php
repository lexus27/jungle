<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 03.10.2016
 * Time: 17:31
 */
namespace Jungle\Util\Communication\Labaratory {

	/**
	 * Class Process
	 * @package Jungle\Util\Communication\Labaratory
	 */
	class Process implements ProcessInterface{

		/** @var  ActionInterface */
		protected $action;

		/** @var  bool */
		protected $waiting = true;

		/** @var  bool */
		protected $canceled = false;

		/** @var  mixed */
		protected $result;

		/** @var  array */
		protected $params = [];

		/**
		 * Process constructor.
		 * @param ActionInterface|null $action
		 */
		public function __construct(ActionInterface $action = null){
			$this->action = $action;
		}

		/**
		 * @return ActionInterface
		 */
		public function getAction(){
			return $this->action;
		}

		/**
		 * @param $result
		 * @return $this
		 */
		public function setResult($result){
			$this->result = $result;
			return $this;
		}


		/**
		 * @return mixed
		 */
		public function getResult(){
			return $this->result;
		}

		/**
		 * @param bool|true $wait
		 * @return $this
		 */
		public function setWaiting($wait = true){
			$this->waiting = $wait;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isWaiting(){
			return $this->waiting;
		}

		/**
		 * @param bool|true|true $canceled
		 * @return $this
		 */
		public function setCanceled($canceled = true){
			$this->canceled =$canceled;
			return $this;
		}


		/**
		 * @return bool
		 */
		public function isCanceled(){
			return $this->canceled;
		}

		/**
		 * @param array $params
		 * @return $this
		 */
		public function setParams(array $params){
			$this->params = $params;
			return $this;
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
	}
}

