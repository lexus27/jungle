<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 14.05.2015
 * Time: 22:48
 */

namespace Jungle\Smart\Value {

	use Jungle\Smart\Value\Measure\IUnit;
	use Jungle\Smart\Value\Measure\IUnitType;
	use Jungle\Smart\Value\Measure\Unit;

	/**
	 * Class Measure
	 * @package Jungle\Smart\Value
	 */
	class Measure extends Number implements IMeasure{


		/**
		 * @var IUnit
		 */
		protected $unit;

		/**
		 * @var IUnit
		 */
		protected $unit_dependency;


		/**
		 * @param IUnit $unit
		 * @return $this
		 */
		public function setUnit(IUnit $unit){
			if($this->unit !== $unit){
				$this->unit = $unit;
			}
			return $this;
		}

		/**
		 * Конвертация значения к другой единице измерения
		 * @param IUnit $unit
		 * @return $this
		 */
		public function changeUnit(IUnit $unit){
			if($this->unit){
				if($this->unit->equalType($unit)){
					$this->value = $this->unit->convertTo($unit,$this->value);
					$this->unit = $unit;
				}else{
					throw new \LogicException('Is not EQUAL TYPE');
					// error of type not compared
				}
			}else{
				$this->setUnit($unit);
			}
			return $this;
		}

		/**
		 * @return IUnit
		 */
		public function getUnit(){
			return $this->unit;
		}


		/**
		 * @param IUnit $unit
		 * @return $this
		 */
		public function setDepend(IUnit $unit = null){
			if($this->unit_dependency !== $unit){
				$this->unit_dependency = $unit;
			}
			return $this;
		}

		/**
		 * @param IUnit $unit
		 * @return $this
		 */
		public function changeDepend(IUnit $unit){
			if($this->unit_dependency){
				if($this->unit_dependency->equalType($unit)){
					$type = $unit->getType();
					$this->value = $type->convert($this->value,$unit,$this->unit_dependency);
					$this->unit_dependency = $unit;
				}else{
					throw new \LogicException('Is not EQUAL TYPE');
					// error of type not compared
				}
			}else{
				$this->setDepend($unit);
			}
			return $this;
		}

		/**
		 * @return IUnit
		 */
		public function getDepend(){
			return $this->unit_dependency;
		}



		/**
		 * @param $value
		 * @param IUnitType $uType1
		 * @param IUnitType $uType2
		 * @return $this
		 */
		public function setValue($value,IUnitType $uType1 = null,IUnitType $uType2=null){
			if(is_string($value)){
				$unitData = Unit::parseUnit($value);
				if($unitData){
					$value = $unitData[0];
					$unitData = explode('/',trim($unitData[1]));
					if(isset($unitData[0])){
						if(!$uType1){
							throw new \LogicException('Must measure unit type 1');
						}
						$unit1 = $uType1->getUnit($unitData[0]);
						if(!$unit1){
							throw new \LogicException('Unit "' . $unit1 . '" (value="'.$value.'") not found in passed MeasureType First "' . $uType1->getName() . '" ');
						}
						$this->setUnit($unit1);
					}

					if(isset($unitData[1])){
						if(!$uType2){
							throw new \LogicException('Must measure unit type 2');
						}
						$unit2 = $uType2->getUnit($unitData[1]);
						if(!$unit2){
							throw new \LogicException('Unit "' . $unit2 . '" (value="'.$value.'") not found in passed MeasureType Second "' . $uType2->getName() . '" ');
						}
						$this->setDepend($unit2);
					}
				}else{
					throw new \LogicException('Parse unit error "'.$value.'"');
				}
			}
			return parent::setValue($value);
		}

		/**
		 * @return string
		 */
		public function getValue(){
			$number = $this->getRaw();
			$unit   = $this->getUnit();
			return ($number . $unit . ($this->unit_dependency?'/'.$this->unit_dependency:''));
		}

		/**
		 * @return int
		 */
		public function getNumber(){
			return $this->value;
		}

		/**
		 * @param Measure $descendant
		 */
		protected function onDelivery(Measure $descendant){
			if($this->unit){
				$descendant->setUnit($this->unit);
			}
			if($this->unit_dependency){
				$descendant->setDepend($this->unit_dependency);
			}
		}

		/**
		 *
		 */
		protected function beforeExtenderCall(){
			if($this->ancestor instanceof Measure && !$this->exhibited){
				$this->unit             = $this->ancestor->getUnit();
				$this->unit_dependency  = $this->ancestor->getDepend();
			}
		}

		/**
		 * @return string
		 */
		public function __toString(){
			return $this->getValue();
		}
	}
}