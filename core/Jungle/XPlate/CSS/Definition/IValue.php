<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 19.05.2015
 * Time: 22:31
 */

namespace Jungle\XPlate\CSS\Definition;


use Jungle\Util\Smart\Value\IValue as SmartValue;

/**
 * Interface ICssPropertyValue
 * @package Jungle\XPlate\Interfaces
 *
 * Класс добавляет в разновидность возможностей,
 * функцию множества вариантов значаний,
 * нацеленная в основном на кроссбраузерность и контроль типов
 *
 * Этот класс не предусматривает распределение имен свойств (CSSProperty name)
 * в отличие от ICssProperty который должен это делать
 */
interface IValue extends SmartValue {

	/**
	 * @param IProperty $property
	 * @return array
	 *
	 * Должен выдать массив, каждый элемент которого будет представлять собой значение
	 * которое будет в последствии подставленно в таком духе:
	 * $returnedValue = [
	 * 		"-webkit-gradient(blah blah)",
	 * 		"-moz-gradient(blah blah)"
	 * ];
	 * $cssString = '';
	 * $propertyName = 'background';
	 * foreach($returnedValue as $val){
	 * 		$cssString.= $propertyName . ': ' . $val. ";\r\n";
	 * }
	 *
	 * $cssString complete!!!..
	 *
	 * ICssPropertyValue добавляет возможность
	 *
	 */
	public function processEval(IProperty $property);

}