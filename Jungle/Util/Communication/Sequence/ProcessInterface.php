<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 19:12
 */
namespace Jungle\Util\Communication\Sequence {

	/**
	 * Interface ProcessInterface
	 * @package Jungle\Util\Communication\Sequence
	 */
	interface ProcessInterface{

		/**
		 * @param $definition
		 * @return $this
		 */
		public function setCommandText($definition);

		/**
		 * @return string
		 */
		public function getCommandText();


		/**
		 * @return CommandInterface
		 */
		public function getCommand();

		/**
		 * @return mixed
		 */
		public function getCode();

		/**
		 * @return mixed
		 */
		public function getResult();

		/**
		 * @param $name
		 * @param $task
		 * @return mixed
		 */
		public function setTask($name, $task);

		/**
		 * @param $name
		 * @return mixed
		 */
		public function getTask($name);

		/**
		 * @return mixed
		 */
		public function hasTasks();


		/**
		 * @param array $params
		 * @return mixed
		 */
		public function setParams(array $params);


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
		 * @return bool
		 */
		public function isCanceled();

	}
}

