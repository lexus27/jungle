<?php
/**
 * Created by PhpStorm.
 * Project: jungle
 * Date: 09.04.2015
 * Time: 16:40
 */

namespace Jungle\Logic;

/**
 * Class Condition
 * @package Jungle\Logic
 */
class Condition {

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
	protected $comparable;

	/**
	 * @var mixed
	 */
	protected $processed_comparable;



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
	public function setComparable($value){
		if($this->comparable !== $value){
			$this->comparable = $value;
			$this->processed_comparable = null;
		}
		return $this;
	}

	/**
	 * @param bool $processed
	 * @return Condition|mixed
	 */
	public function getComparable($processed = true){
		return $processed?$this->_getProcessed('comparable'):$this->value;
	}

	/**
	 * @param $propertyKey
	 * @return mixed
	 */
	protected function _getProcessed($propertyKey){
		$processedKey = 'processed_'.$propertyKey;
		if(!isset($this->{$propertyKey}) || !isset($this->{$processedKey})){
			throw new \BadMethodCallException(
				__METHOD__.' property key "'.$propertyKey.'" not exists in '.get_called_class()
			);
		}
		$prop = & $this->{$propertyKey};
		$processed = & $this->{$processedKey};
		if(!is_null($prop) && !$processed){
			$processed = $this->{$propertyKey};
			while($processed instanceof Condition){
				$processed = $processed->execute();
			}
		}
		return $processed;
	}

	/**
	 * @return mixed value completed or for comparison in next condition operator
	 */
	public function execute(){
		$operator = $this->getOperator();
		$compared = $operator->check($this);
		while($compared instanceof Condition){$compared = $compared->execute();}
		return $compared;
	}

}