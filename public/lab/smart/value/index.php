<?php require __DIR__ . '/base.php';

use Jungle\Smart\Value\Value;

/**
 * Test Simple value
 */
echo '<section>';
echo '<h2>Test Simple value</h2>';
$value = new Value();
$value->setValue('Hello');
$descendant = $value->extend(function(Value $value){
	$value->setValue($value->raw . ' World!');
});
echo '<p>Base:',$value,' > Concatenate extend via(\' World!\'):',$descendant->getValue(),'</p>';
echo '</section>';






