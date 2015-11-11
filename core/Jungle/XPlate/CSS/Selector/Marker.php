<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 08.05.2015
 * Time: 3:00
 */

namespace Jungle\XPlate\CSS\Selector {

	use Jungle\Smart\Keyword\Keyword;
	use Jungle\XPlate\Interfaces\IMarker;


	/**
	 * Class Marker
	 * @package Jungle\XPlate\CSS\Selector
	 * Маркировка это ключевая состоявляющая такая как CSS Ид или Класс
	 * .class #id
	 */
	abstract class Marker extends Keyword implements IMarker{

		/**
		 * @var string
		 */
		protected $symbol;

		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @param string $name
		 * @return $this
		 */
		public function setName($name){
			$this->setIdentifier($name);
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getName(){
			return $this->getIdentifier();
		}

		/**
		 * @return string
		 */
		public function getQueryString(){
			return $this->getQuerySymbol() . $this->getName();
		}

		/**
		 * @return string
		 */
		public function getQuerySymbol(){
			return $this->symbol;
		}

		/**
		 * @return string
		 */
		public function __toString(){
			return $this->getQueryString();
		}


	}

}