<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 11.11.2015
 * Time: 15:00
 */
namespace Jungle\Smart\Value\Measure\CoefficientParser {

	use Jungle\Smart\Value\Measure\CoefficientParser;
	use Jungle\Smart\Value\Measure\IUnit;
	use Jungle\Smart\Value\Measure\IUnitType;

	/**
	 * Class Simple
	 * @package Jungle\Smart\Value\Measure\CoefficientParser
	 */
	class Simple extends CoefficientParser{

		/** @var  string */
		protected $regex = '@(?<factor>[\d]+\.?[\d]+)\s?(?<unit>[\w]+)@';

		/**
		 * @param string $definition
		 * @param IUnit $unit
		 * @return bool
		 */
		public function parse($definition, IUnit $unit){
			if( ($m = $this->match($definition))===false){
				return false;
			}else{
				$type = $unit->getType();
				$factor = floatval($m['factor']);
				$targetUnit = $type->getUnit($m['unit']);
				if(!$targetUnit){
					$this->throwParserError('Unit by name "'.$m['unit'].'" not found in "'.$type->getName().'"');
				}
				return $targetUnit->getCoefficient() * $factor;
			}
		}
	}
}

