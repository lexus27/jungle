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
	class Context implements ContextInterface{

		/** @var  Manager */
		protected $manager;

		/** @var  array */
		protected $properties = [];


		/**
		 * Context constructor.
		 */
		public function __construct(){
			$this->properties = [

				'TIME' => [
					'WORK_DAYS'     => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
					'WEEK_END_DAYS' => ['Friday', 'Saturday'],
					'SEASONS'       => [
						'FIRST_HALF'    => in_array(date('n'),[1,2,3,4,5,6]),
						'SECOND_HALF'   => in_array(date('n'),[7,8,9,10,11,12]),

						'WINTER' => ['December','January', 'February'],
						'SPRING' => ['March', 'April' , 'May'],
						'SUMMER' => ['June' , 'July', 'August'],
						'AUTUMN' => ['September', 'October', 'November']
					]
				]
			];
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
		 * @param array $properties
		 * @param bool|false $merge
		 */
		public function setProperties(array $properties = [ ], $merge = true){
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
			switch($name){
				case 'user':    return $this->getUser();
				case 'scope':   return $this->getScope();
				case 'action':  return $this->getAction();
				case 'object':  return $this->getObject();
				default: return isset($this->properties[$name])?$this->properties[$name]:null;
			}
		}

		/**
		 * @param $name
		 * @return bool
		 */
		public function __isset($name){
			switch($name){
				case 'user':    return !!$this->getUser();
				case 'scope':   return !!$this->getScope();
				case 'action':  return !!$this->getAction();
				case 'object':  return !!$this->getObject();
				default: return isset($this->properties[$name]);
			}
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
			return isset($this->properties['user'])?$this->properties['user']:null;
		}

		/**
		 * @return mixed
		 */
		public function getScope(){
			return array_replace_recursive([
				'time'      => [
					'hour'      => date('H'),
					'minutes'   => date('i'),

					'week_day'          => date('l'),
					'week_day_number'   => date('n'),
					'meridiem'          => date('a'),
					'moth'              => date('F'),
					'moth_number'       => date('n'),
					'year'      => intval(date('Y')),
					'time'      => time()
				],
			],isset($this->properties['scope'])?$this->properties['scope']:[]);
		}

		/**
		 * @return mixed
		 */
		public function getAction(){
			return isset($this->properties['action'])?$this->properties['action']:null;
		}

		/**
		 * @return mixed
		 */
		public function getObject(){
			return isset($this->properties['object'])?$this->properties['object']:null;
		}


	}
}

