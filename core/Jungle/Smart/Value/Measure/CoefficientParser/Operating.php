<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 11.11.2015
 * Time: 15:01
 */
namespace Jungle\Smart\Value\Measure\CoefficientParser {

	use Jungle\Smart\Value\Measure\CoefficientParser;
	use Jungle\Smart\Value\Measure\IUnit;
	use Jungle\Smart\Value\Measure\IUnitType;

	/**
	 * Class Operating
	 * @package Jungle\Smart\Value\Measure\CoefficientParser
	 *
	 * {UnitName:string} {Operator:string(+,-,*,/)} {Subject:numeric|UnitName:string}
	 */
	class Operating extends CoefficientParser{

		/** @var string */
		protected $regex = '@([\w\d\\\\/]+)\s?([\+\-\*/])\s?([\d\.]+|[\w\d]+)@';

		/**
		 * @param string $definition
		 * @param IUnit $unit
		 * @return bool
		 */
		public function parse($definition, IUnit $unit){
			if(($m = $this->match($definition))===false){
				return false;
			}else{
				$type = $unit->getType();

				$mainDefinition     = $m[1];
				$operatorDefinition = $m[2];
				$secondDefinition   = $m[3];

				if(is_numeric($secondDefinition)){
					$second = floatval($secondDefinition);
				}else{
					$secondUnit = $type->getUnit($secondDefinition);
					if(!$secondUnit){
						$this->throwParserError('Unit by secondary name "'.$secondDefinition.'" not found in "'.$type->getName().'"');
						return false;
					}
					$second = $secondUnit->getCoefficient();
				}

				if(is_numeric($mainDefinition)){
					$main = floatval($mainDefinition);
				}else{
					$mainUnit = $type->getUnit($mainDefinition);
					if($mainUnit){
						$main = $mainUnit->getCoefficient();
					}else{
						$this->throwParserError('Unit by main name "'.$mainDefinition.'" not found in "'.$type->getName().'"');
						return false;
					}
				}

				switch($operatorDefinition){
					case '+': return  $main + $second; break;
					case '-': return  $main - $second; break;
					case '/': return  $main / $second; break;
					case '*': return  $main * $second; break;
					default: throw new \LogicException('Operator "'.$operatorDefinition.'" not supported'); break;
				}
			}
		}
	}
}

