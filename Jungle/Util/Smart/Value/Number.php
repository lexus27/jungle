<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 12.05.2015
 * Time: 13:52
 */

namespace Jungle\Util\Smart\Value;

/**
 * Class Number
 * @package Jungle\Util\Smart\Value
 */
class Number extends Value{

    /**
     * @var int
     */
	protected static $default_value = 0;

	protected $round_precision = null;

	protected $round_mode      = PHP_ROUND_HALF_UP;

	/**
	 * @param int $round
	 * @return mixed
	 */
	public function setRoundPrecision($round = null){
		$this->round_precision = is_integer($round)?$round:null;
	}


	/**
	 * @param int $round
	 * @return mixed
	 */
	public function setRoundMode($round = PHP_ROUND_HALF_UP){
		$this->round_mode = $round;
	}

	/**
	 * @param $amount
	 * @return $this
	 */
	public function increment($amount = 1){
		$val = $this->getRaw();
		return $this->setValue($val + abs($amount));
	}

	/**
	 * @param $amount
	 * @return $this
	 */
	public function decrement($amount = 1){
		$val = $this->getRaw();
		return $this->setValue($val - abs($amount));
	}

	/**
	 * @param int $amount increment|decrement signed value
	 * @return $this
	 */
	public function offset($amount = 0){
		$val = $this->getRaw();
		return $this->setValue($val + $amount);
	}

	/**
	 * @param int $amount
	 * @return $this
	 */
	public function divide($amount = 2){
		$val = $this->getRaw();
		return $this->setValue($val? $val / $amount : 0 );
	}

	/**
	 * @param $amount
	 * @return $this
	 */
	public function factor($amount = 2){
		$val = $this->getRaw();
		return $this->setValue($val && $amount?$val*$amount:0);
	}

	/**
	 * @return $this
	 */
	public function factorial(){
		$factorial = $this->getRaw();
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
		$val = $this->getRaw();
		return $this->setValue($val?$val%$amount:0);
	}

	/**
	 * @return $this|Number|void
	 */
	public function invert(){
		$val = $this->getRaw();
		return $this->setValue($val > 0?$val * -1:($val < 0?$val * 1:0));
	}

	/**
	 * @return float|mixed
	 */
	public function getValue(){
		$number = $this->getRaw();
		return $this->round_precision!==null?round($number,$this->round_precision,$this->round_mode):$number;
	}

	/**
	 * @param Value|Number|static $descendant
	 */
	protected function onDelivery($descendant){
		$descendant->round_precision = $this->round_precision;
		$descendant->round_mode      = $this->round_mode;
		parent::onDelivery($descendant);
	}

	/**
	 *
	 */
	protected function beforeExtenderCall(){
		if($this->ancestor instanceof Number){
			$this->round_precision  = $this->ancestor->round_precision;
			$this->round_mode       = $this->ancestor->round_mode;
		}
		parent::beforeExtenderCall();
	}


}