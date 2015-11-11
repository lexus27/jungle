<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 08.05.2015
 * Time: 2:33
 */

namespace Jungle\XPlate\CSS\Selector {

	use Jungle\XPlate\CSS\Selector\Marker\Cls,
		Jungle\XPlate\CSS\Selector\Marker\Identifier;

	/**
	 * Class Subject
	 * @package Jungle\XPlate\CSS\Selector
	 * Subject - это класс отвечающий за вариант компонтовки классов и идентификатора если он есть
	 *
	 * subject = #id1.class1.class2 = совокупность компонтовки маркеров
	 *
	 */
	class Subject{

		protected

			/**
			 * @var Identifier|null
			 */
			$identifier = null,

			/**
			 * @var Cls[]
			 */
			$classes = [];


		/**
		 * @return Cls[]
		 */
		public function getClasses(){
			return $this->classes;
		}

		/**
		 * @param Cls $class
		 * @return $this
		 */
		public function addClass(Cls $class){
			$i = $this->searchClass($class);
			if($i === false){
				$this->classes[] = $class;
			}
			return $this;
		}

		/**
		 * @param Cls $class
		 * @return int|bool
		 */
		public function searchClass(Cls $class){
			return array_search($class, $this->classes, true);
		}

		/**
		 * @param Cls $class
		 * @return $this
		 */
		public function removeClass(Cls $class){
			$i = $this->searchClass($class);
			if($i !== false){
				array_splice($this->classes, $i, 1);
			}
			return $this;
		}

		/**
		 * @param Identifier $identifier
		 * @return $this
		 */
		public function setIdentifier(Identifier $identifier){
			$this->identifier = $identifier;
			return $this;
		}

		/**
		 * @return Identifier
		 */
		public function getIdentifier(){
			return $this->identifier;
		}

		/**
		 * @return string
		 */
		public function __toString(){
			return $this->identifier . implode('',$this->classes);
		}


	}


}