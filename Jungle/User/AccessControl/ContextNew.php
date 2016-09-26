<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 26.09.2016
 * Time: 21:17
 */
namespace Jungle\User\AccessControl {

	/**
	 * Class ContextNew
	 * @package Jungle\User\AccessControl
	 */
	abstract class ContextNew implements ContextInterface{

		/** @var  Manager */
		protected $manager;

		/** @var  array */
		protected $properties = [];

		/**
		 * @param Manager $manager
		 * @return $this
		 */
		public function setManager(Manager $manager){
			$this->manager = $manager;
			return $this;
		}

		/**
		 * @return Manager
		 */
		public function getManager(){
			return $this->manager;
		}

		/**
		 * @param array $properties
		 * @param bool|false $merge
		 */
		public function setProperties(array $properties = [ ], $merge = false){
			$this->properties = $merge?array_replace($this->properties, $properties):$this->properties;
		}

		/**
		 * @return array
		 */
		public function getProperties(){
			return $this->properties;
		}


		/**
		 * @param $name
		 * @return mixed|null
		 */
		public function __get($name){
			if(isset($this->properties[$name])){
				return $this->properties[$name];
			}
			switch($name){
				case 'user':    return $this->getUser();
				case 'scope':   return $this->getScope();
				case 'action':  return $this->getAction();
				case 'object':  return $this->getObject();
			}
			return null;
		}

		/**
		 * @param $name
		 * @return bool
		 */
		public function __isset($name){
			if(isset($this->properties[$name])){
				return true;
			}
			switch($name){
				case 'user':    return !!$this->getUser();
				case 'scope':   return !!$this->getScope();
				case 'action':  return !!$this->getAction();
				case 'object':  return !!$this->getObject();
			}
			return false;
		}

		/**
		 * @param $name
		 * @param $value
		 * @return $this
		 */
		public function __set($name, $value){
			$this->properties[$name] = $value;
		}


		/**
		 * @param $name
		 * @return $this
		 */
		public function __unset($name){
			unset($this->properties[$name]);
		}

		/**
		 * @return mixed
		 */
		public function getUser(){
			// TODO: Implement getUser() method.
		}

		/**
		 * @return mixed
		 */
		public function getScope(){
			// TODO: Implement getScope() method.
		}

		/**
		 * @return mixed
		 */
		public function getAction(){
			// TODO: Implement getAction() method.
		}

		/**
		 * @return mixed
		 */
		public function getObject(){
			// TODO: Implement getObject() method.
		}


	}
}

