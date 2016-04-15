<?php require __DIR__ . '/../base.php';


use Jungle\Smart\Value\Number;

/**
 * Test number, factorial
 */
echo '<section>';
echo '<h2>Test number</h2>';
$value = new Number();
$value->setValue(6);
$descendant = $value->extend(function(Number $value){
	$value->factorial();
});
$descendant1 = $descendant->extend(function(Number $value){
	$value->divide(5);
});
echo '<p>Base:' ,$value,' > Factorial:', $descendant->getValue() , ' > Divide(6):' , $descendant1->getValue() ,'</p>';
echo '</section>';

