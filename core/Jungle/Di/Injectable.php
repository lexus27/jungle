<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.06.2016
 * Time: 18:07
 */
namespace Jungle\Di {

	/**
	 * Class Injectable
	 * @package Jungle\Di
	 *
	 * @property $application
	 * @property $dispatcher
	 * @property $router
	 * @property $cache
	 * @property $event
	 *
	 * @property $filesystem
	 * @property $database
	 * @property $schema
	 *
	 * @property $user
	 * @property $access
	 * @property $session
	 * @property $messenger
	 */
	class Injectable implements InjectionAwareInterface{

		/** @var  DiInterface */
		protected $_dependency_injector;

		/**
		 * @return DiInterface
		 */
		public function getDi(){
			return $this->_dependency_injector;
		}

		/**
		 * @param DiInterface $di
		 * @return $this
		 */
		public function setDi(DiInterface $di){
			$this->_dependency_injector = $di;
			return $this;
		}

		/**
		 * @param $name
		 * @return mixed
		 */
		public function __get($name){
			return $this->_dependency_injector->get($name);
		}

	}
}

