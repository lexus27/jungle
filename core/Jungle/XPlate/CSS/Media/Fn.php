<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 11.05.2015
 * Time: 15:00
 */

namespace Jungle\XPlate\CSS\Media {

	use Jungle\Smart\Value\IMeasure;
	use Jungle\Smart\Value\IValue;
	use Jungle\Smart\Value\Measure\IUnit;
	use Jungle\Smart\Value\String;

	/**
	 * Class Condition
	 * @package Jungle\XPlate\CSS\Media
	 *
	 * CSS Медиа-функция @media all and mediaFn and mediaFn
	 *
	 */
	class Fn implements IFn{

		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var bool
		 */
		protected $allowedBool;

		/**
		 * @var bool
		 */
		protected $allowedMeasure;

		/**
		 * @var bool
		 */
		protected $allowedMeasureRange;

		/**
		 * @var bool|string[]
		 */
		protected $allowedString;

		/**
		 * @var IUnit[]|bool
		 */
		protected $allowedMeasureUnits = true;


		/**
		 * @param $name
		 * @return mixed|void
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
		 * @param $string
		 * @return bool
		 */
		public function isStringAllowed($string){
			return is_array($this->allowedString) ? in_array($string,$this->allowedString,true):$this->allowedString;
		}

		/**
		 * @param bool $allowed
		 * @return $this
		 */
		public function setStringAllowed($allowed = true){
			$this->allowedString = is_array($allowed)?$allowed:boolval($allowed);
			return $this;
		}


		/**
		 * @return bool
		 */
		public function isBooleanAllowed(){
			return $this->allowedBool;
		}

		/**
		 * @param bool $allowed
		 * @return $this
		 */
		public function setBooleanAllowed($allowed = true){
			$this->allowedBool = boolval($allowed);
			return $this;
		}


		/**
		 * @return bool
		 */
		public function isMeasureAllowed(){
			return $this->allowedMeasure;
		}

		/**
		 * @param bool $allowed
		 * @return $this
		 */
		public function setMeasureAllowed($allowed = true){
			$this->allowedMeasure = boolval($allowed);
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isRangeAllowed(){
			return $this->allowedMeasureRange;
		}

		/**
		 * @param bool $allowed
		 * @return $this
		 */
		public function setRangeAllowed($allowed = true){
			$this->allowedMeasureRange = boolval($allowed);
			return $this;
		}

		/**
		 * @param IUnit $unit
		 * @return bool
		 */
		public function isMeasureUnitAllowed(IUnit $unit){
			return is_array($this->allowedMeasureUnits)?in_array($unit,$this->allowedMeasureUnits,true):true;
		}

		/**
		 * @param IUnit $unit
		 * @return $this
		 */
		public function addAllowedMeasureUnit(IUnit $unit){
			if($this->searchAllowedMeasureUnit($unit) === false){
				if(!is_array($this->allowedMeasureUnits)){
					$this->allowedMeasureUnits = [];
				}
				$this->allowedMeasureUnits[] = $unit;
			}
			return $this;
		}

		/**
		 * @param IUnit $unit
		 * @return bool|mixed
		 */
		public function searchAllowedMeasureUnit(IUnit $unit){
			if(!is_array($this->allowedMeasureUnits)){
				return false;
			}
			return array_search($unit,$this->allowedMeasureUnits,true);
		}

		/**
		 * @param IUnit $unit
		 * @return $this
		 */
		public function removeAllowedMeasureUnit(IUnit $unit){
			if(($i =$this->searchAllowedMeasureUnit($unit)) !== false){
				array_splice($this->allowedMeasureUnits,$i,1);
				if(!$this->allowedMeasureUnits)$this->allowedMeasureUnits = true;
			}
			return $this;
		}



		/**
		 * @param $value
		 * @return bool
		 */
		public function isPassedValueAllowed($value){
			if($value instanceof IValue){

				if(
					(
						$value instanceof IMeasure &&
						$this->allowedMeasure &&
						$this->isMeasureUnitAllowed($value->getPrimaryUnit())
					) || (
						$value instanceof String &&
						$this->isStringAllowed($value->getValue())
					)
				)return true;

			}elseif(is_string($value)){
				return $this->isStringAllowed($value);
			}elseif(is_bool($value) && $value === true){
				return $this->isBooleanAllowed();
			}

			return false;
		}

		/**
		 * @return string
		 */
		public function __toString(){
			return $this->getName();
		}

		/**
		 * @param string|int $value
		 * @return array [measureNumber,unitName]
		 */
		public static function splitParseMeasureUnit($value){
			$value = trim($value);
			$f = substr($value,0,1);
			if($f==='.' || is_numeric($f)){
				$value = preg_replace('@\s+@',' ',$value);
				$value = preg_replace('@[\.,]+@','.',$value);
				preg_match('@([\d\.]+)\s?([\w]+)?@',$value,$m);
				$measureNumber = strpos($m[2],'.')!==false?(floatval($m[2])):intval($m[2]);
				$unitName = isset($m[1]) && $m[1]?strtolower($m[1]):null;
				return [$measureNumber,$unitName];
			}
			return false;
		}

	}
}