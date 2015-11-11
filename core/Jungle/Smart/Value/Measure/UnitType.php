<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 16.05.2015
 * Time: 11:04
 */

namespace Jungle\Smart\Value\Measure;

/**
 * Class UnitType
 * @package Jungle\Smart\Value\Measure
 */
class UnitType implements IUnitType, \ArrayAccess{

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var IUnit[]
	 */
	protected $units = [];

	/**
	 * Получить имя объекта
	 * @return mixed
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * Выставить имя объекту
	 * @param $name
	 * @return $this
	 */
	public function setName($name){
		if($this->name !== $name){
			$this->name = $name;
		}
		return $this;
	}

	/**
	 * Добавить еденицу измерения к данному типу
	 * @param IUnit $unit
	 * @param bool $setType
	 * @return $this
	 */
	public function addUnit(IUnit $unit, $setType = true){
		if($this->searchUnit($unit)===false){
			$this->units[] = $unit;
			if($setType){
				$unit->setType($this,false);
			}
		}
		return $this;
	}

	/**
	 * Поиск единицы измерения в данном типе
	 * @param IUnit $unit
	 * @return int|bool
	 */
	public function searchUnit(IUnit $unit){
		return array_search($unit,$this->units,true);
	}

	/**
	 * Удаление единицы измерения из данного типа
	 * @param IUnit $unit
	 * @return $this
	 */
	public function removeUnit(IUnit $unit,$setTypeNull = true){
		$i = $this->searchUnit($unit);
		if($i !== false){
			array_splice($this->units,$i,1);
			if($setTypeNull)$unit->setType(null,false,false);
		}
		return $this;
	}

	/**
	 * @param float $number
	 * @param IUnit $from
	 * @param IUnit $to
	 * @return float
	 */
	public static function convert($number, IUnit $from, IUnit $to){
		if($from !== $to){
			$fromCoefficient = $from->getCoefficient();
			$toCoefficient = $to->getCoefficient();
			$number*= $fromCoefficient;
			$number/= $toCoefficient;
		}
		return $number;
	}

	/**
	 * @param $name
	 * @return IUnit|null
	 */
	public function getUnit($name){
		foreach($this->units as $unit){
			if($unit->getName() === $name){
				return $unit;
			}
		}
		return null;
	}

	/**
	 * @param string $unitName
	 * @return bool
	 */
	public function offsetExists($unitName){
		return (bool)$this->getUnit($unitName);
	}

	/**
	 * @param string $unitName
	 * @return IUnit|null
	 */
	public function offsetGet($unitName){
		return $this->getUnit($unitName);
	}

	/**
	 * @param string $unitName
	 * @param string $coefficient
	 */
	public function offsetSet($unitName, $coefficient){
		if(!$unitName){
			throw new \BadMethodCallException(
				'ArrayAccessible UnitType use UnitType[NULL] not allowed - offset must be unit key, '.
				'value must be coefficient definition'
			);
		}
		$unit = $this->getUnit($unitName);
		if(!$unit){
			$unit = new Unit();
			$this->addUnit($unit);
		}
		$unit->setCoefficient($coefficient);
	}

	/**
	 * @param string $unitName
	 */
	public function offsetUnset($unitName){
		$unit = $this->getUnit($unitName);
		if($unit){
			$this->removeUnit($unit);
		}
	}
}