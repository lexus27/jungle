<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.10.2016
 * Time: 17:46
 */
namespace Jungle\Util\ProcessUnit {

	/**
	 * Interface ProcessInterface
	 * @package Jungle\Util\ProcessUnit
	 */
	interface ProcessInterface{

		/**
		 * @param array $params
		 * @param bool $merge
		 * @return $this
		 */
		public function setParams(array $params, $merge = false);

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
		 * @return mixed
		 */
		public function getResult();

		/**
		 * @param $result
		 * @return $this
		 */
		public function setResult($result);

		/**
		 * @param $key
		 * @param $value
		 * @return $this
		 */
		public function addTask($key, $value);

		/***
		 * @param $key
		 * @return null
		 */
		public function getTask($key);

		/**
		 * @return bool
		 */
		public function hasTasks();
	}
}

