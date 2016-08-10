<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 15.11.2015
 * Time: 1:50
 */
namespace Jungle\_DesignPatterns\ParadigmLabs {

	/**
	 * Class ArrayAccessBuilderItem
	 * @package Jungle\_DesignPatterns\ParadigmLabs
	 */
	class BaseNamed implements INamed{

		protected $name;

		/**
		 * @return string
		 */
		function getName(){
			return $this->name;
		}

		/**
		 * @param mixed $name
		 * @return $this
		 */
		function setName($name){
			$this->name = $name;
			return $this;
		}
	}
}

