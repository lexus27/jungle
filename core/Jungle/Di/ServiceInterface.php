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

		public function getName();

		public function setName($name);

		public function setDefinition($definition);

		public function getDefinition();

		public function setShared($shared = true);

		public function isShared();

		public function getSharedInstance();

		public function reset();

		public function resolve(DiInterface $di, array $parameters = null);

	}
}

