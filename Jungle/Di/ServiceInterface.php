<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.06.2016
 * Time: 23:25
 */
namespace Jungle\Di {

	/**
	 * Interface ServiceInterface
	 * @package Jungle\Di
	 */
	interface ServiceInterface{

		/**
		 * ServiceInterface constructor.
		 * @param $name
		 * @param $definition
		 * @param $shared
		 */
		public function __construct($name, $definition, $shared);

		/**
		 * @return mixed
		 */
		public function getName();

		/**
		 * @param $name
		 * @return mixed
		 */
		public function setName($name);

		/**
		 * @param $definition
		 * @return mixed
		 */
		public function setDefinition($definition);

		/**
		 * @return mixed
		 */
		public function getDefinition();

		/**
		 * @param bool|true $shared
		 * @return mixed
		 */
		public function setShared($shared = true);

		/**
		 * @return mixed
		 */
		public function isShared();

		/**
		 * @return mixed
		 */
		public function getSharedInstance();

		/**
		 * @return mixed
		 */
		public function reset();

		/**
		 * @param DiInterface $di
		 * @param array|null $arguments
		 * @return mixed
		 */
		public function resolve(DiInterface $di, array $arguments = null);

	}
}

