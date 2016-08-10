<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 16.05.2015
 * Time: 8:51
 */

namespace Jungle\Util\Smart\Value\Measure;
use Jungle\Util\Smart\Value\Measure\CoefficientParser\Operating;
use Jungle\Util\Smart\Value\Measure\CoefficientParser\Simple;

/**
 * Реализация единицы измерения
 * Class Unit
 * @package Jungle\Util\Smart\Value\Measure
 */
class Unit implements IUnit{

	const UNIT_NAME_PATTERN = '(?!<\d)[\w\\\\/]+';

	/** @var CoefficientParser[] */
	private static $coefficient_parsers = [];

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var IUnitType
	 */
	protected $type;

	/**
	 * @var int
	 */
	protected $coefficient = 1;

	/**
	 * @param IUnitType $type
	 */
	public function __construct(IUnitType $type = null){
		if($type)$this->setType($type);
		if(!self::$coefficient_parsers){
			self::$coefficient_parsers = [new Simple(),new Operating()];
		}
	}



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
			if($this->type && $this->type->getUnit($name)){
				throw new \LogicException('Unit "'.$name.'" already exists in "'.$this->type->getName().'" type');
			}
			$this->name = $name;
		}

		return $this;
	}

	/**
	 * Выставить коеффициент по отношению к ведущей единице измерения
	 * @param int $coefficient
	 * @return $this
	 */
	public function associate($coefficient = 1){
		if($coefficient === null){
			$coefficient = 1;
		}
		if($this->coefficient !== $coefficient){
			$this->coefficient = $coefficient;
		}
		return $this;
	}

	/**
	 * Выставить коеффициент по отношению к ведущей единице измерения
	 * @param int $coefficient
	 * @return $this
	 */
	public function setCoefficient($coefficient = 1){
		return $this->associate($coefficient);
	}

	/**
	 * Получить коеффициент ведущей единицы измерения
	 * @return int
	 */
	public function getCoefficient(){
		if(is_string($this->coefficient)){
			$c = $this->parseCoefficientDefinition($this->coefficient);
			if(!$c){
				throw new \LogicException('Coefficient definition "'.$this->coefficient.'" unknown!');
			}
			$this->coefficient = $c;
		}
		if(!$this->coefficient){
			throw new \LogicException('Coefficient from "'.$this->getType()->getName().':'.$this.' does not exists!"');
		}
		return $this->coefficient;
	}

	/**
	 * @param $definition
	 * @return float
	 */
	protected function parseCoefficientDefinition($definition){
		foreach(self::$coefficient_parsers as $p){
			if(($c = $p->parse($definition,$this))!==false){
				return $c;
			}
		}
		return false;
	}





	/**
	 * @param IUnit $unit
	 * @param $number
	 * @return float
	 */
	public function convertTo(IUnit $unit,$number){
		return $this->getType()->convert($number,$this,$unit);
	}

	/**
	 * @param IUnit $unit
	 * @return bool
	 */
	public function equalType(IUnit $unit){
		return $this->getType() === $unit->getType();
	}

	/**
	 * Выставляет тип величины измерения
	 * @param IUnitType $type
	 * @param bool $addNew
	 * @param bool $rmOld
	 * @return $this
	 */
	public function setType(IUnitType $type = null,$addNew = true, $rmOld = true){
		$old= $this->type;
		if($old !== $type){
			$this->type = $type;
			if($type && $addNew)$type->addUnit($this,false);
			if($old && $rmOld)$old->removeUnit($this);
		}
		return $this;
	}

	/**
	 * Получить тип величины измерения
	 * @return IUnitType
	 */
	public function getType(){
		return $this->type;
	}

	/**
	 * Аналог INamedBase.getName() для строчного использования объекта
	 * @return string
	 */
	public function __toString(){
		return $this->getName();
	}


	/**
	 * @param $value
	 * @return array [value,unitName]
	 */
	public static function parseUnit($value){
		if(preg_match('@([\d]+(\.[\d]+)?)\s?(' . self::UNIT_NAME_PATTERN . ')?@', $value,$matches)){
			$num = floatval($matches[1]); list($main,$second) = explode('/',preg_replace(['@[/\\\\]+@','@\s+@'],['/',''],$matches[3]));
			return [$num,$main,$second];
		}else{
			return false;
		}
	}

	/**
	 * @param CoefficientParser $parser
	 */
	public static function addCoefficientParser(CoefficientParser $parser){
		if(self::searchCoefficientParser($parser)===false){
			self::$coefficient_parsers[] = $parser;
		}
	}

	/**
	 * @param CoefficientParser $parser
	 * @return mixed
	 */
	public static function searchCoefficientParser(CoefficientParser $parser){
		return array_search($parser,self::$coefficient_parsers,true);
	}

	/**
	 * @param CoefficientParser $parser
	 */
	public static function removeCoefficientParser(CoefficientParser $parser){
		if(($i=self::searchCoefficientParser($parser))===false){
			array_splice(self::$coefficient_parsers,$i,0);
		}
	}

}