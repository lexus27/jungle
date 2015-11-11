<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 12.09.2015
 * Time: 23:33
 */
namespace Jungle\XPlate\CSS {

	use Jungle\Smart\Value\Value;
	use Jungle\XPlate\CSS\Selector\Marker\Cls;

	/**
	 * Class ClassList
	 * @package Jungle\XPlate\CSS
	 */
	class ClassList extends Value{

		/**
		 * @var Cls[]
		 */
		protected $value = [];

		/**
		 * @var Cls[]
		 */
		protected static $default_value = [];

		/**
		 * @param Cls $class
		 * @return $this
		 */
		public function up(Cls $class){
			if(($i = $this->searchClass($class))!==false){
				array_splice($this->value,$i,1);
				array_unshift($this->value, $class);
			}
			return $this;
		}

		/**
		 * @param Cls $class
		 * @return $this
		 */
		public function down(Cls $class){
			if(($i = $this->searchClass($class))!==false){
				array_splice($this->value,$i,1);
				$this->value[] = $class;
			}
			return $this;
		}

		/**
		 * @param Cls $class
		 * @return $this
		 */
		public function addClass(Cls $class){
			if($this->searchClass($class)===false){
				$this->value[] = $class;
			}
			return $this;
		}

		/**
		 * @param Cls $class
		 * @return mixed
		 */
		public function searchClass(Cls $class){
			return array_search($class,$this->value,true);
		}

		/**
		 * @param $className
		 * @return bool
		 */
		public function hasClass($className){
			foreach($this->value as $class){
				if($class->getIdentifier() === $className){
					return true;
				}
			}
			return false;
		}

		/**
		 * @param Cls $class
		 * @return $this
		 */
		public function removeClass(Cls $class){
			if(($i = $this->searchClass($class))!==false){
				array_splice($this->value,$i,1);
			}
			return $this;
		}


		/**
		 * @return string
		 */
		public function __toString(){
			$s = [];
			foreach($this->value as $class){
				$s[] = $class->getName();
			}
			return implode(' ',$s);
		}

	}
}

