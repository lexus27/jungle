<?php
/**
 * Created by PhpStorm.
 * Project: MobileCasino
 * Date: 18.03.2015
 * Time: 14:46
 */

namespace Jungle\Util\Smart\Value;

/**
 * Class EcoMemoryValue
 * @package Jungle\Util\Smart\Value
 */
class EcoMemoryValue implements IValue{

	/**
	 * @var EcoMemoryValue[]
	 */
	protected static $stack = [];

	/**
	 * @var EcoMemoryValue[]
	 */
	protected static $idle = [];

	/**
	 * @var int
	 */
	protected $usages = 0;

	/**
	 * @var
	 */
	protected $value;

	/**
	 * Protected
	 */
	protected function __construct(){}

	/**
	 *
	 */
	protected function __clone(){}

	/**
	 * @return mixed
	 */
	public function getValue(){
		return $this->value;
	}

	/**
	 * @return int
	 */
	public function countUsages(){
		return $this->usages;
	}

	/**
	 * @param int $on
	 */
	protected function incrementUsage($on = 1){
		$this->usages+= $on;
	}

	/**
	 * @param int $on
	 */
	protected function decrementUsage($on = 1){
		if($this->usages && $this->usages >= $on){
			$this->usages-= $on;
		}else{
			$this->usages = 0;
		}
	}

	/**
	 * @return mixed
	 */
	public function __invoke(){
		return $this->getValue();
	}

	/**
	 * @return string
	 */
	public function __toString(){
		return (string)$this->getValue();
	}


	/**
	 * @param $raw
	 * @param null $old
	 * @return EcoMemoryValue|null|static
	 */
	public static function get($raw,$old = null){
		if($old instanceof EcoMemoryValue && $old() === $raw){
			return $old;
		}
		$eco = static::findEco($raw);
		if(!$eco){
			$eco = static::getIdle();
			if(!$eco){
				$eco = new static();
			}
			$eco->value = $raw;
			self::$stack[] = $eco;
		}
		$eco->incrementUsage();
		return $eco;
	}

	/**
	 * @return $this
	 */
	public function remove(){
		$this->decrementUsage();
		if(!$this->countUsages()){
			self::addIdle($this);
		}
		return $this;
	}

	/**
	 * @param EcoMemoryValue $eco
	 */
	public static function staticRemove(EcoMemoryValue $eco){
		$eco->decrementUsage();
		if(!$eco->countUsages()){
			self::addIdle($eco);
		}
	}

	/**
	 * @param $raw
	 * @return bool|int|mixed|string
	 */
	protected static function searchEco($raw){
		if($raw instanceof EcoMemoryValue){
			return array_search($raw,self::$stack,true);
		}else{
			foreach(self::$stack as $i => $eco){
				if($eco()===$raw){
					return $i;
				}
			}
			return false;
		}
	}

	/**
	 * @param $raw
	 * @return EcoMemoryValue|null
	 */
	protected static function findEco($raw){
		$i = static::searchEco($raw);
		if($i!==false){
			return self::$stack[$i];
		}
		return null;
	}


	/**
	 * @return EcoMemoryValue
	 */
	protected static function getIdle(){
		if(self::$idle){
			return array_shift(self::$idle);
		}
		return null;
	}

	/**
	 * @param EcoMemoryValue $value
	 * @return mixed
	 */
	protected static function searchIdle(EcoMemoryValue $value){
		return array_search($value,self::$idle,true);
	}

	/**
	 * @param EcoMemoryValue $eco
	 */
	protected static function addIdle(EcoMemoryValue $eco){
		if(static::searchIdle($eco)===false){
			$i = static::searchEco($eco);
			if($i!==false){
				unset(self::$stack[$i]);
			}
			$eco->value = null;
			self::$idle[] = $eco;
		}
	}

	/**
	 * @return int
	 */
	public static function ecoCount(){
		return count(self::$stack);
	}

	/**
	 * @return int
	 */
	public static function idleCount(){
		return count(self::$idle);
	}


	/**
	 * @param IValue|mixed $value
	 * @return bool
	 */
	public function equal($value){
		return ($value instanceof IValue)?$value->equal($this):$value === $this->getValue();
	}

}