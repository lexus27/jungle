<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 11.05.2015
 * Time: 3:03
 */

namespace Jungle\XPlate\CSS\Media {

	use Jungle\XPlate\Interfaces\IType;

	/**
	 * Class Type
	 * @package Jungle\XPlate\CSS\Media
	 *
	 * Device Media viewport type  all, screen, braille and them
	 *
	 */
	class Type implements IType{

		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @return mixed
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}

		/**
		 * @return string
		 */
		public function __toString(){
			return $this->name;
		}

	}

}