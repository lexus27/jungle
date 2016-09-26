<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 26.09.2016
 * Time: 20:19
 */
namespace Jungle\User\AccessControl {

	/**
	 * Interface ContextInterface
	 * @package Jungle\User\AccessControl
	 */
	interface ContextInterface{

		/**
		 * @param Manager $manager
		 * @return $this
		 */
		public function setManager(Manager $manager);

		/**
		 * @return Manager
		 */
		public function getManager();


		/**
		 * @return array
		 */
		public function getProperties();


		/**
		 * @return mixed
		 */
		public function getUser();

		/**
		 * @return mixed
		 */
		public function getScope();

		/**
		 * @return mixed
		 */
		public function getAction();

		/**
		 * @return object
		 */
		public function getObject();


		/**
		 * @param $name
		 * @return mixed|null
		 */
		public function __get($name);

		/**
		 * @param $name
		 * @return bool
		 */
		public function __isset($name);

		/**
		 * @param $name
		 * @param $value
		 * @return $this
		 */
		public function __set($name, $value);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function __unset($key);


	}
}

