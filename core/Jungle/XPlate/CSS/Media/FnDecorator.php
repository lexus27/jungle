<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 11.05.2015
 * Time: 16:12
 */

namespace Jungle\XPlate\CSS\Media {

	use Jungle\Smart\Value\IMeasure;
	use Jungle\Smart\Value\IValue;

	/**
	 * Class FnDecorator
	 * @package Jungle\XPlate\CSS\Media
	 */
	class FnDecorator implements IFnDecorator{

		/**
		 * @var IFn
		 */
		protected $fn;


		/**
		 * @var IMeasure|null
		 */
		protected $min;

		/**
		 * @var IMeasure|null
		 */
		protected $max;


		/**
		 * @var IMeasure|string|null
		 */
		protected $value;


		/**
		 * @param IFn $fn
		 * @return $this
		 */
		public function setFn(IFn $fn){
			if($this->fn !== $fn){
				$this->fn = $fn;
				$this->begin();
			}
			return $this;
		}

		/**
		 * @return IFn
		 */
		public function getFn(){
			return $this->fn;
		}

		/**
		 * @return $this
		 */
		public function begin(){
			$this->resetRange();
			$this->value = null;
			return $this;
		}



		/**
		 * @param $v
		 * @return $this
		 */
		public function setValue($v){
			if(!$this->getFn()->isPassedValueAllowed($v)){
				throw new \LogicException('Passed value is not allowed in Combo.setValue');
			}
			$this->value = $v;
			return $this;
		}

		/**
		 * @return bool|int|string|IValue
		 */
		public function getValue(){
			return $this->value;
		}

		/**
		 * @return bool
		 */
		public function isRange(){
			return $this->min || $this->max;
		}

		/**
		 * @return $this
		 */
		public function resetRange(){
			$this->min = null;
			$this->max = null;
			return $this;
		}


		/**
		 * @param IMeasure $value
		 * @return $this
		 */
		public function setMin(IMeasure $value){
			if($this->min !== $value){
				$fn = $this->getFn();
				if($this->max && $this->max->equal($value)){
					throw new \LogicException('FnCombo.setMin invalid, value is already set in setMax');
				}
				if(!$this->getFn()->isPassedValueAllowed($value)){
					throw new \LogicException('Passed value in Combo.setMin error is not allowed');
				}
				if(!$fn->isRangeAllowed()){
					throw new \LogicException('Combo.setMin, Fn(owner) range is not allowed');
				}
				$this->min = $value;
			}
			return $this;
		}

		/**
		 * @param IMeasure $value
		 * @return $this
		 */
		public function setMax(IMeasure $value){

			if($this->max !== $value){
				if($this->min && $this->min->equal($value)){
					throw new \LogicException('FnCombo.setMax invalid, value is already set in setMin');
				}
				$fn = $this->getFn();
				if(!$fn->isPassedValueAllowed($value)){
					throw new \LogicException('Passed value in Combo.setMax error is not allowed');
				}
				if(!$fn->isRangeAllowed()){
					throw new \LogicException('Combo.setMax, Fn(owner) range is not allowed');
				}
				$this->max = $value;
			}

			return $this;
		}

		/**
		 * @param IMeasure $min
		 * @param IMeasure $max
		 * @return $this
		 */
		public function setMinMax(IMeasure $min, IMeasure $max){
			return $this->setMin($min)->setMax($max);
		}

		/**
		 * @return IMeasure|null
		 */
		public function getMin(){
			return $this->min;
		}

		/**
		 * @return IMeasure|null
		 */
		public function getMax(){
			return $this->max;
		}


		/**
		 * @return bool|string
		 */
		public function toString(){
			$fn = $this->getFn();
			$combo = [];
			if($this->isRange()){
				if(($min = $this->getMin())){
					$combo[] = '(min-' . $fn->getName() . ':' . $min->getValue() . ')';
				}
				if(($max = $this->getMax())){
					$combo[] = '(max-' . $fn->getName() . ':' . $max->getValue() . ')';
				}
				return implode(' and ',$combo);
			}elseif($this->value !== null){
				if(is_bool($this->value) && $this->value === true){
					return '('.$fn->getName().')';
				}else{
					return '(' . $fn->getName() . ':'. $this->value.')';
				}
			}else{
				return '';
			}
		}

		public function __toString(){
			return $this->toString();
		}
	}
}