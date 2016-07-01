<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 23.02.2016
 * Time: 7:08
 */
namespace Jungle\_DesignPatterns\EAV {

	/**
	 * Class Attribute
	 * @package Jungle\_DesignPatterns\EAV
	 */
	class Attribute{

		/** @var  string */
		protected $name;

		// protected $collection = false;



		/**
		 * @param string $name
		 * @return $this
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @return string
		 */
		public function __toString(){
			return (string)$this->getName();
		}

	}
}

