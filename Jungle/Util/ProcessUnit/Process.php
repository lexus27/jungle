<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.10.2016
 * Time: 15:49
 */
namespace Jungle\Util\ProcessUnit {

	/**
	 * Class Process
	 * @package Jungle\Util\ProcessUnit
	 */
	class Process implements ProcessInterface{

		/** @var array  */
		protected $params = [];

		/** @var  mixed */
		protected $result;

		/** @var array  */
		protected $tasks = [];


		/**
		 * @param array $params
		 * @param bool $merge
		 * @return $this
		 */
		public function setParams(array $params, $merge = false){
			$this->params = $merge?array_replace($this->params, $params):$params;
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

		/**
		 * @return mixed
		 */
		public function getResult(){
			return $this->result;
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
		 * @param $key
		 * @param $value
		 * @return $this
		 */
		public function addTask($key, $value){
			$this->tasks[$key] = $value;
			return $this;
		}

		/***
		 * @param $key
		 * @return null
		 */
		public function getTask($key){
			return $this->tasks[$key]?$this->tasks[$key]:null;
		}

		/**
		 * @return bool
		 */
		public function hasTasks(){
			return !empty($this->tasks);
		}


	}
}

