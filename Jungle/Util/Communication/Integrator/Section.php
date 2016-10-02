<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 13:24
 */
namespace Jungle\Util\Communication\Integrator {

	/**
	 * Class Section
	 * @package Jungle\Util\Communication\Integrator\Integration
	 */
	class Section implements ConfigurableInterface{

		/** @var  string|null  */
		protected $host;

		/** @var  Section */
		protected $parent;

		/** @var  MethodInterface[]  */
		protected $methods = [];

		/** @var  Section[]  */
		protected $sections = [];

		/** @var array  */
		protected $options = [];


		/**
		 * @param $alias
		 * @param Section $bundle
		 * @return $this
		 */
		public function setSection($alias, Section $bundle){
			$this->sections[$alias] = $bundle;
			$bundle->parent = $this;
			return $this;
		}

		/**
		 * @param $alias
		 */
		public function removeSection($alias){
			unset($this->sections[$alias]);
		}


		/**
		 * @param null $host
		 * @return $this
		 */
		public function setHost($host = null){
			$this->host = $host;
			return $this;
		}

		/**
		 * @return null|string
		 */
		public function getHost(){
			return !$this->host && $this->parent?$this->parent->getHost():$this->host;
		}

		/**
		 * @param $methodName
		 */
		public function __call($methodName){
			if(isset($this->methods[$methodName])){
				call_user_func($this->methods[$methodName]);
			}
		}

		/**
		 * @return array
		 */
		public function getOptions(){
			if($this->parent){
				return array_replace($this->parent->getOptions(), $this->options);
			}
			return $this->options;
		}

		/**
		 * @param $sectionName
		 * @return null
		 */
		public function __get($sectionName){
			return isset($this->sections[$sectionName])?$this->sections[$sectionName]:null;
		}

		/**
		 * @return array
		 */
		public function getNestedSegments(){
			$a = $this->parent?$this->parent->getNestedSegments():[];
			$a[] = $this;
			return $a;
		}


		public function getOption($key){
			// TODO: Implement getOption() method.
		}
	}
}

