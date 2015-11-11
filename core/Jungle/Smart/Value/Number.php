<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 12.05.2015
 * Time: 13:52
 */

namespace Jungle\Smart\Value;

/**
 * Class Number
 * @package Jungle\Smart\Value
 */
class Number extends Value{

    /**
     * @var int
     */
	protected static $default_value = 0;

	/**
	 * @param $amount
	 * @return $this
	 */
	public function increment($amount = 1){
		return $this->setValue(intval($this->value) + abs($amount));
	}

	/**
	 * @param $amount
	 * @return $this
	 */
	public function decrement($amount = 1){
		return $this->setValue(intval($this->value) - abs($amount));
	}

	/**
	 * @param int $amount increment|decrement signed value
	 * @return $this
	 */
	public function offset($amount = 0){
		return $this->setValue(intval($this->value) + $amount);
	}

	/**
	 * @param int $amount
	 * @return $this
	 */
	public function divide($amount = 2){
		return $this->setValue($this->value? intval($this->value) / $amount : 0 );
	}

	/**
	 * @param $amount
	 * @return $this
	 */
	public function factor($amount = 2){
		return $this->setValue($this->value && $amount?intval($this->value)*$amount:0);
	}

	/**
	 * @return $this
	 */
	public function factorial(){
		$factorial = $this->value;
		$current = $factorial;
		while($current > 2){
			$factorial*= --$current;
		}
		return $this->setValue($factorial);
	}

	/**
	 * @param $amount
	 * @return $this
	 */
	public function mod($amount = 2){
		return $this->setValue($this->value?intval($this->value)%$amount:0);
	}

	/**
	 * @return $this|Number|void
	 */
	public function invert(){
		$value = intval($this->value);
		return $this->setValue($value > 0?$value * -1:($value < 0?$value * 1:0));
	}


}