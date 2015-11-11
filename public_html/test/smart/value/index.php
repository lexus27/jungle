<?php
use Jungle\Smart\Value\Measure;
use Jungle\Smart\Value\Measure\Unit;
use Jungle\Smart\Value\Measure\UnitType;
use Jungle\Smart\Value\Number;
use Jungle\Smart\Value\Value;

require __DIR__ . '/../../loader.php';


/**
 * Test Simple value
 */
$value = new Value();
$value->setValue('Hello');
$descendant = $value->extend(function(Value $value){
	$value->setValue($value->raw . ' World!');
});
echo '<p>Test Simple value ',$descendant->getValue(),'<p/>';

/**
 * Test number, factorial
 */
$value = new Number();
$value->setValue(6);
$descendant = $value->extend(function(Number $value){
	$value->factorial();
});
$descendant1 = $descendant->extend(function(Number $value){
	$value->divide(5);
});
echo '<p>Test number, factorial ' , $descendant->getValue() , ' - ' , $descendant1->getValue() ,'<p/>';



/**
 * Test measurement
 */

$unitType = new UnitType();
$unitType->setName('distance');
$unitType
	->setBase((new Unit())->setName('millimeters')->setCoefficient(1))
	->addUnit((new Unit())->setName('centimeters')->setCoefficient('millimeters * 10'))
	->addUnit((new Unit())->setName('inches')->setCoefficient('centimeters * 2.4'))
	->addUnit((new Unit())->setName('decimeter')->setCoefficient('centimeters * 10'))
	->addUnit((new Unit())->setName('meters')->setCoefficient('centimeters * 100'))
	->addUnit((new Unit())->setName('kilometers')->setCoefficient('meters * 1000'))
	->addUnit((new Unit())->setName('miles')->setCoefficient('kilometers * 1.345'));


$unitType = new UnitType();
$unitType->setName('time');
$unitType
	->setBase((new Unit())->setName('nanoseconds')->setCoefficient(1))
	->addUnit((new Unit())->setName('microsecond')->setCoefficient('nanoseconds * 1000'))
	->addUnit((new Unit())->setName('millisecond')->setCoefficient('microsecond * 1000'))
	->addUnit((new Unit())->setName('seconds')->setCoefficient('millisecond * 1000'))
	->addUnit((new Unit())->setName('minutes')->setCoefficient('seconds * 60'))
	->addUnit((new Unit())->setName('hours')->setCoefficient('minutes * 60'))
	->addUnit((new Unit())->setName('days')->setCoefficient('hours * 24'))
	->addUnit((new Unit())->setName('week')->setCoefficient('days * 7'))
	->addUnit((new Unit())->setName('months')->setCoefficient('days * 30,4167'))
	->addUnit((new Unit())->setName('years')->setCoefficient('month * 12'))
	->addUnit((new Unit())->setName('decades')->setCoefficient('month * 12'))
	->addUnit((new Unit())->setName('month')->setCoefficient('days * 30,4167'));



$value = new Measure();
$value->setValue('6 kilometers',$unitType);
$descendant = $value->extend(function(Measure $value) use ($unitType){
	$value->changeUnit($unitType->getUnit('miles'));
});
$descendant1 = $descendant->extend(function(Measure $value){
	//$value->factor(2);
});
echo '<p>Test measurement ' , $value->getValue() , ' - ' , $descendant1->getValue() ,'<p/>';