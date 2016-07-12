<?php
/**
 * Created by PhpStorm.
 * Project: jungle
 * Date: 09.04.2015
 * Time: 16:40
 */

namespace Jungle\Code\LogicConstruction;

/**
 * Class Condition
 * @package Jungle\Code\LogicConstruction
 */
class Condition {

	/**
	 * @var bool
	 */
	protected $closures_process = false;

	/**
	 * @var Operator
	 */
	protected $operator;

	/**
	 * @var mixed|Condition
	 */
	protected $value;

	/**
	 * @var mixed
	 */
	protected $processed_value;



	/**
	 * @var mixed|Condition
	 */
	protected $secondary;

	/**
	 * @var mixed
	 */
	protected $processed_secondary;


	/**
	 * @param bool|false $process
	 * @return $this
	 */
	public function setClosuresAsCondition($process = false){
		$this->closures_process = $process;
		return $this;
	}

	/**
	 * @param Operator $operator
	 * @return $this
	 */
	public function setOperator(Operator $operator){
		$this->operator = $operator;
		return $this;
	}

	/**
	 * @return Operator
	 */
	public function getOperator(){
		return $this->operator;
	}



	/**
	 * @param mixed|Condition $value
	 * @return $this;
	 */
	public function setValue($value){
		if($this->value !== $value){
			$this->value = $value;
			$this->processed_value = null;
		}

		return $this;
	}

	/**
	 * @param bool $processed
	 * @return Condition|mixed
	 */
	public function getValue($processed = true){
		return $processed?$this->_getProcessed('value'):$this->value;
	}



	/**
	 * @param mixed|Condition $value
	 * @return $this;
	 */
	public function setSecondary($value){
		if($this->secondary !== $value){
			$this->secondary = $value;
			$this->processed_secondary = null;
		}
		return $this;
	}

	/**
	 * @param bool $processed
	 * @return Condition|mixed
	 */
	public function getSecondary($processed = true){
		return $processed?$this->_getProcessed('secondary'):$this->secondary;
	}

	/**
	 *
	 */
	public function reset(){
		$this->processed_secondary = null;
		$this->processed_value = null;
		return $this;
	}

	/**
	 * @param $propertyKey
	 * @return mixed
	 */
	protected function _getProcessed($propertyKey){
		$processedKey = 'processed_'.$propertyKey;
		if(!property_exists($this,$propertyKey) || !property_exists($this,$processedKey)){
			throw new \BadMethodCallException(
				__METHOD__.' property key "'.$propertyKey.'" not exists in '.get_called_class()
			);
		}
		$prop = & $this->{$propertyKey};
		$processed = & $this->{$processedKey};
		if(!is_null($prop) && !$processed){
			$processed = $this->{$propertyKey};
			while($processed instanceof Condition || ($this->closures_process?$processed instanceof \Closure:false)){
				$processed = $processed instanceof \Closure?call_user_func($processed):$processed->execute();
			}
		}
		return $processed;
	}

	/**
	 * @return mixed value completed or for comparison in after_sequence condition operator
	 */
	public function execute(){
		$operator = $this->getOperator();
		$compared = $operator->check($this->getValue(),$this->getSecondary());
		while(
			$compared instanceof Condition ||
			($this->closures_process?$compared instanceof \Closure:false)
		){
			$compared = $compared instanceof \Closure?call_user_func($compared):$compared->execute();
		}
		return $compared;
	}


	/**
	 * @param mixed $a
	 * @param Operator|string $operator
	 * @param mixed $b
	 * @param Operator|string $operatorClass
	 * @return Condition
	 */
	public static function getCondition($a, $operator,$b, $operatorClass = null){
		if(!$operatorClass){
			$operatorClass = Operator::class;
		}
		return (new Condition())
			->setOperator(
				$operator instanceof Operator?$operator:$operatorClass::getOperator($operator)
			)->setValue($a)->setSecondary($b);
	}

	/**
	 * @param $a
	 * @param $operator
	 * @param $b
	 * @param Operator|string $operatorClass
	 * @return bool
	 */
	public static function collateRaw($a, $operator, $b, $operatorClass = null){
		if(!$operatorClass){
			$operatorClass = Operator::class;
		}
		$operator = $operatorClass::getOperator($operator);
		return $operator->check($a,$b);
	}

}