<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 08.05.2015
 * Time: 2:32
 */

namespace Jungle\XPlate\CSS\Selector {

	use Jungle\Basic\INamed;
	use Jungle\Smart\Keyword\Keyword;


	/**
	 * Class Modifier
	 * @package Jungle\XPlate\CSS\Selector
	 * Модификатор , используется в компбинации пресет + его модификаторы
	 */
	class Modifier extends Keyword implements INamed{

		/**
		 * (parameter)
		 * @var bool
		 */
		protected $parametric = false;

		/**
		 * @var bool
		 */
		protected $pseudo = false;

		/**
		 * @return string
		 */
		public function getName(){
			return $this->getIdentifier();
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			$this->setIdentifier($name);
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isPseudo(){
			return $this->getOption('pseudo',false);
		}

		/**
		 * @return bool
		 */
		public function isParametric(){
			return $this->getOption('parametric',false);
		}

		/**
		 * @param bool|true $pseudo
		 * @return $this
		 */
		public function setPseudo($pseudo = true){
			$this->setOption('pseudo',$pseudo);
			return $this;
		}

		/**
		 * @param bool|true $parametric
		 * @return $this
		 */
		public function setParametric($parametric = true){
			$this->setOption('parametric',$parametric);
			return $this;
		}


		/**
		 * @return string
		 */
		public function __toString(){
			return $this->getName();
		}

	}
}