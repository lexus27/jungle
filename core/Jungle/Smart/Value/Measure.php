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
	use Jungle\Smart\Value\Measure\UnitType;

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
		protected $unit_second = null;

		/**
		 * @var array
		 */
		protected $_manipulating_tmp = [];

		/**
		 * @param null $value
		 * @param IUnitType $mainUnitType
		 * @param IUnitType $secondUnitType
		 * @param callable|null $configurator
		 */
		public function __construct(
			$value = null,
			IUnitType $mainUnitType = null,
			IUnitType $secondUnitType = null,
			callable $configurator = null
		){
			if($value === null){
				$this->value = static::$default_value;
			}else{
				$this->setValue($value,$mainUnitType,$secondUnitType);
			}
			$this->apply($configurator);
		}




		/**
		 * @param IUnit $unit
		 * @return $this
		 */
		public function setPrimaryUnit(IUnit $unit){
			if($this->unit !== $unit){
				$this->unit = $unit;
				$this->refresh();
			}
			return $this;
		}

		/**
		 * Конвертация значения к другой единице измерения
		 * @param IUnit|string $to
		 * @return $this
		 */
		public function primary($to){
			if($this->unit){
				$val = $this->getRaw();
				$changed = false;
				if($to instanceof IUnit){
					if($this->unit->equalType($to)){
						if($to !== $this->unit){
							$val = $this->unit->convertTo($to,$val);
							$changed = true;
						}
					}else{
						throw new \LogicException('Is not EQUAL TYPE main unit "'.$this->unit->getType()->getName().'"."'.$this->unit.'" to "'.$to->getType()->getName().'"."'.$to.'"');
						// error of type not compared
					}
				}elseif(is_string($to)){
					$type = $this->unit->getType();
					if(!isset($type[$to])){
						$this->throwMainUnitError($to,$val,$type);
					}
					return $this->primary($type[$to]);
				}else{
					throw new \LogicException('convertUnit( IUnit|string $to): $to is Invalid argument');
				}

				if($changed){
					$this->setValue(floatval($val));
					$this->unit = $to;
					$this->refresh();
				}
			}else{
				$this->setPrimaryUnit($to);
			}
			return $this;
		}

		/**
		 * @return IUnit
		 */
		public function getPrimaryUnit(){
			return $this->unit;
		}


		/**
		 * @param IUnit $unit
		 * @return $this
		 */
		public function setSecondaryUnit(IUnit $unit = null){
			if($this->unit_second !== $unit){
				$this->unit_second = $unit;
				$this->refresh();
			}
			return $this;
		}

		/**
		 * @param IUnit|string $to
		 * @return $this
		 */
		public function secondary($to){
			if($this->unit_second){
				$val        = $this->getRaw();
				$changed    = false;
				if($to instanceof IUnit){
					if($this->unit_second->equalType($to)){
						if($to !== $this->unit_second){
							$val  = UnitType::convert($val,$to,$this->unit_second);
							$changed = true;
						}
					}else{
						throw new \LogicException('Is not EQUAL TYPE Second unit "'.$this->unit_second->getType()->getName().'"."'.$this->unit.'" to "'.$to->getType()->getName().'"."'.$to.'"');
						// error of type not compared
					}
				}elseif(is_string($to)){
					$type = $this->unit_second->getType();
					if(!isset($type[$to])){
						$this->throwSecondUnitError($to,$val,$type);
					}
					return $this->secondary($type[$to]);
				}else{
					throw new \LogicException('convertSecondUnit( IUnit|string $to): $to is Invalid argument');
				}
				if($changed){
					$this->setValue(floatval($val));
					$this->unit_second = $to;
					$this->refresh();
				}
			}else{
				$this->setSecondaryUnit($to);
			}
			return $this;
		}

		/**
		 * @return IUnit
		 */
		public function getSecondaryUnit(){
			return $this->unit_second;
		}

		/**
		 * @param IUnit|string $primary
		 * @param IUnit|string|null $secondary
		 * @return $this
		 */
		public function convert($primary,$secondary = null){
			if(is_string($primary)){
				$primary = explode('/',$primary);
			}else if(!is_array($primary) && $secondary){
				$m = $primary;
				$s = $secondary;
			}

			if(!is_array($primary) && ((!isset($m) || !$m) && (!isset($s) || !$s))){
				throw new \LogicException('passed params is invalid!');
			}

			if(is_array($primary)){
				list($m,$s) = $primary;
			}

			if(isset($m)){
				$this->primary($m);
			}

			if(isset($s)){
				$this->secondary($s);
			}
			return $this;
		}



		/**
		 * @param $value
		 * @param IUnitType $mainType
		 * @param IUnitType $secondType
		 * @return $this
		 */
		public function setValue(
			$value,
			IUnitType $mainType   = null,
			IUnitType $secondType = null
		){
			if(is_string($value)){
				$unitData = Unit::parseUnit($value);
				if($unitData){
					list($value,$mainName,$secondName) = $unitData;
					if($mainName){
						if(!$mainType) throw new \LogicException('Main measure type not passed, but parsed');
						$main = $mainType->getUnit($mainName);
						if(!$main){
							$this->throwMainUnitError($mainName,$value,$mainType);
						}
						$this->setPrimaryUnit($main);
					}
					if($secondName){
						if(!$secondType) throw new \LogicException('Second measure type not passed, but parsed');
						$second = $secondType->getUnit($secondName);
						if(!$second){
							$this->throwSecondUnitError($secondName,$value,$secondType);
						}
						$this->setSecondaryUnit($second);
					}
				}else{
					throw new \LogicException('Parse unit error "'.$value.'"');
				}
			}
			return parent::setValue($value);
		}


		/**
		 * @param $name
		 * @param $val
		 * @param IUnitType $type
		 */
		protected function throwMainUnitError($name,$val,IUnitType $type){
			throw new \LogicException(
				'Unit main "' . $name . '" (value="'.$val.'") not found'.
				' in passed MeasureType main "' . $type->getName() . '" '
			);
		}

		/**
		 * @param $name
		 * @param $val
		 * @param IUnitType $type
		 */
		protected function throwSecondUnitError($name,$val,IUnitType $type){
			throw new \LogicException(
				'Unit second "' . $name . '" (value="'.$val.'") not found'.
				' in passed MeasureType second "' . $type->getName() . '" '
			);
		}


		/**
		 * @return string
		 */
		public function getValue(){
			$this->checkout();
			return (parent::getValue().' '.$this->unit.($this->unit_second?'/'.$this->unit_second:''));
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
			$descendant->unit = $this->unit;
			$descendant->unit_second = $this->unit_second;
			parent::onDelivery($descendant);
		}

		/**
		 *
		 */
		protected function beforeExtenderCall(){
			if($this->ancestor instanceof Measure){
				$this->unit             = $this->ancestor->unit;
				$this->unit_second  = $this->ancestor->unit_second;
			}
			parent::beforeExtenderCall();
		}


		/**
		 * @param int|string $amount
		 * @return $this
		 */
		public function increment($amount = 1){
			$this->beforeManipulation($amount);
			parent::increment($amount);
			$this->afterManipulation();
			return $this;
		}

		/**
		 * @param int|string $amount
		 * @return $this
		 */
		public function decrement($amount = 1){
			$this->beforeManipulation($amount);
			parent::decrement($amount);
			$this->afterManipulation();
			return $this;
		}

		/**
		 * @param int|string $amount
		 * @return $this
		 */
		public function offset($amount = 0){
			$this->beforeManipulation($amount);
			parent::offset($amount);
			$this->afterManipulation();
			return $this;
		}

		/**
		 * @param int|string $amount
		 * @return $this
		 */
		public function divide($amount = 2){
			$this->beforeManipulation($amount);
			parent::divide($amount);
			$this->afterManipulation();
			return $this;
		}

		/**
		 * @param int|string $amount
		 * @return $this
		 */
		public function factor($amount = 2){
			$this->beforeManipulation($amount);
			parent::factor($amount);
			$this->afterManipulation();
			return $this;
		}

		/**
		 * @param int|string $amount
		 * @return $this
		 */
		public function mod($amount = 2){
			$this->beforeManipulation($amount);
			parent::mod($amount);
			$this->afterManipulation();
			return $this;
		}


		/**
		 * @param string|int|float $amount
		 */
		protected function beforeManipulation(& $amount){
			if(is_string($amount)){
				list($amount, $main, $second) = Unit::parseUnit($amount);
				if($main && ($u = $this->getPrimaryUnit())){
					$t = $u->getType();
					if(!isset($t[$main])){
						$this->throwMainUnitError($main, $amount, $t);
					}
					$this->primary($t[$main]);
					$this->_manipulating_tmp['toChangeMainUnit'] = $u;
				}

				if($second && ($u = $this->getSecondaryUnit())){
					$t = $u->getType();
					if(!isset($t[$second])){
						$this->throwSecondUnitError($second, $amount, $t);
					}
					$this->secondary($t[$second]);
					$this->_manipulating_tmp['toChangeSecondUnit'] = $u;
				}
			}
		}

		/**
		 *
		 */
		protected function afterManipulation(){
			if($this->_manipulating_tmp['toChangeMainUnit']){
				$this->primary($this->_manipulating_tmp['toChangeMainUnit']);
			}
			if($this->_manipulating_tmp['toChangeSecondUnit']){
				$this->secondary($this->_manipulating_tmp['toChangeSecondUnit']);
			}
			$this->_manipulating_tmp = [];
		}

	}
}