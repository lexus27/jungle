<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 19:27
 */
namespace Jungle\Util\Communication\Sequence {

	/**
	 * Class Process
	 * @package Jungle\Util\Communication\Sequence
	 */
	class Process implements ProcessInterface{

		/** @var   */
		protected $code;

		/** @var  Command */
		protected $command;

		/** @var  string */
		protected $command_text;

		/** @var  array */
		protected $params = [];

		/** @var   */
		protected $result;

		/** @var  bool  */
		protected $canceled = false;

		/** @var  mixed */
		protected $tasks = [];

		/**
		 * Process constructor.
		 * @param Command $command
		 */
		public function __construct(Command $command){
			$this->command = $command;
		}



		/**
		 * @param $definition
		 * @return $this
		 */
		public function setCommandText($definition){
			$this->command_text = $definition;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getCommandText(){
			return $this->command_text;
		}

		/**
		 * @return CommandInterface
		 */
		public function getCommand(){
			return $this->command;
		}

		/**
		 * @param $code
		 * @return $this
		 */
		public function setCode($code){
			$this->code = $code;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getCode(){
			return $this->code;
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
		 * @return bool
		 */
		public function isCanceled(){
			return $this->canceled;
		}

		/**
		 * @param bool|true $canceled
		 * @return $this
		 */
		public function setCanceled($canceled = true){
			$this->canceled = $canceled;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getResult(){
			return $this->result;
		}

		/**
		 * @param $name
		 * @param $task
		 * @return $this
		 */
		public function setTask($name, $task){
			$this->tasks[$name] = $task;
			if($task){
				$this->canceled = true;
			}
			return $this;
		}

		/**
		 * @param $name
		 * @return null
		 */
		public function getTask($name){
			return isset($this->tasks[$name])?$this->tasks[$name]:null;
		}

		/**
		 * @return bool
		 */
		public function hasTasks(){
			return !empty($this->tasks);
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

