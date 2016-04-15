<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 14.02.2016
 * Time: 22:14
 */
namespace Jungle\User\Access\ABAC {

	/**
	 * Class Context
	 * @package Jungle\User\Access\ABAC
	 */
	class Context{

		/** @var  Manager */
		protected $manager;

		/**
		 * @var array
		 */
		protected $properties = [];

		/**
		 * @param array $context_definition
		 */
		public function __construct(array $context_definition = []){
			$this->properties = array_change_key_case($context_definition,CASE_LOWER);
		}

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
		 * @return array
		 */
		public function toArray(){
			return $this->properties;
		}

		/**
		 * @param $name
		 * @return mixed|null
		 */
		public function __get($name){
			$name = strtolower($name);
			return isset($this->properties[$name])?$this->properties[$name]:null;
		}

		/**
		 * @param $name
		 * @return bool
		 */
		public function __isset($name){
			$name = strtolower($name);
			return isset($this->properties[$name]);
		}

		/**
		 * @param $name
		 * @param $value
		 * @return $this
		 */
		public function set($name, $value){
			$name = strtolower($name);
			$this->properties[$name] = $value;
			return $this;
		}


		/**
		 * @param $path
		 */
		public function query($path){




		}


		public function __clone(){}
	}
}

