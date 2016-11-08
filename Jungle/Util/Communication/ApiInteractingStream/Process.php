<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.10.2016
 * Time: 20:12
 */
namespace Jungle\Util\Communication\ApiInteractingStream {

	/**
	 * Class Process
	 * @package Jungle\Util\Communication\ApiInteractingStream
	 */
	class Process extends \Jungle\Util\ProcessUnit\Process{

		/** @var  mixed */
		protected $code;

		/** @var  string */
		protected $command;

		/** @var  Action */
		protected $action;

		/** @var  bool */
		protected $waiting = true;

		/** @var  bool */
		protected $sent = false;

		/** @var  bool */
		protected $canceled = false;

		/**
		 * Process constructor.
		 * @param Action $action
		 */
		public function __construct(Action $action){
			$this->action = $action;
		}

		/**
		 * @return Action
		 */
		public function getAction(){
			return $this->action;
		}

		/**
		 * @param $command
		 * @return $this
		 */
		public function setCommand($command){
			$this->command = $command;
			$this->sent = true;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getCommand(){
			return $this->command;
		}

		/**
		 * @param $result
		 * @return $this
		 */
		public function setResult($result){
			$this->waiting = false;
			$this->result = $result;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isWaiting(){
			return $this->waiting;
		}

		/**
		 * @return bool
		 */
		public function isSent(){
			return $this->sent;
		}

		/**
		 * @return bool
		 */
		public function isCanceled(){
			return $this->canceled;
		}


		/**
		 * @param $code
		 * @return $this
		 */
		public function setCode($code){
			$this->code  = $code;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getCode(){
			return $this->code;
		}

	}
}

