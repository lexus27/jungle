<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.04.2016
 * Time: 16:19
 */

include( dirname(__DIR__) . DIRECTORY_SEPARATOR .'loader.php');
$t = microtime(true);
$manager = \Jungle\RegExp\Template\Manager::getDefault();
$manager->setTemplateDefaults([
	'soft' => false
]);
$template = $manager->template(
	'/users.id{data?</|>:object}/{user.id:int}{extension?<-|>:word}{parameters?</|>:array(/)}', [
	'extension' => [
		'default' => 'php'
	],
	'data' => [
		'setArguments' => [
			'assign_char' => ':',
			'separator' => ',',
			'val_pattern' => '[#int#]'
		]
	]
]);

/*
 * Compliant to line 46: $subjects[1], because default val_pattern = w[\w]
$template = $manager->template(
	'/users.id{data?</|>:object}/{user.id:int}{extension?<-|>:word}{parameters?</|>:array(/)}', [
	'extension' => [
		'default' => 'php'
	],
	'data' => [
		'setArguments' => [
			'assign_char' => ':',
			'separator' => ','
		]
	]
]);
 */
$subjects = [
	'/users.id/param1:1,param2:2/27/2256/hello/',
	'/users.id/param1:val1,param2:val2/27/2256/hello/',
	'/users.id/27/2256/hello/',
	'/users.id/27',
	'/users.id/27-php/2256/hello/',
	'/users.id/27-php',
];
foreach($subjects as $i => $s){
	$s = rtrim($s,'/');
	matching($template,$s,$i);
}

$template = $manager->template(
	'/user/{uid:array.fixed(3;,;[#int#])(;)}{parameters?</|>:array(/)}{last_parameter?</|>:float:(\w+\.\w+)}',[
	'uid' => [
		'setArguments' => [
			'item_pattern' => '[#int#]'
		]
	],
	'last_parameter' => [
		'default' => 1.2
	]
]);
matching($template,'/user/-1,2,-3/a/u',0);

$template = $manager->template(
	'/help/{date}',[
	'date' => [
		'type' => 'date',
		'setArguments' => [
			'pattern' => '[#int.unsigned#]-[#&moth#]-(?:[1-2]?[0-9]|31)'
		]
	]
]);
matching($template,'/help/2016-January-10',0);

$template = $manager->template('/game/{y-axis:array}/{x-axis:array}',[
	'y-axis' => [
		'setArguments' => [
			'limit'         => 10,
			'allow_empty'   => true,
			'item_default'  => 0,
			'item_pattern'  => '[#int#]'
		]
	],
	'x-axis' => [
		'setArguments' => [
			'limit'         => 10,
			'allow_empty'   => true,
			'item_default'  => 0,
			'item_pattern'  => '[#int#]'
		]
	]
]);
matching($template,'/game/1,2,3,,/1,2,3,,',0);


$template = $manager->template(
	'/game/{y-axis:array.fixed}/{x-axis:array.fixed}',[
	'y-axis' => [
		'setArguments' => [
			'count'         => 10,
			'allow_empty'   => true,
			'item_default'  => 0,
			'item_pattern'  => '[#int#]'
		]
	],
	'x-axis' => [
		'setArguments' => [
			'count'         => 10,
			'allow_empty'   => true,
			'item_default'  => 0,
			'item_pattern'  => '[#int#]'
		]
	]
]);
matching($template,'/game/1,2,3,4,5,6,7,8,9,10/1,2,3,4,5,6,7,8,9,10',0);
matching($template,'/game/,,,,,,,,,/1,2,3,4,5,6,7,8,9,10',0);

echo htmlspecialchars($template->render([

	'x-axis' => [
		1,2,4
	],
	'y-axis' => [
		1,2,3
	]

]))."<br/>";



echo sprintf('%.4F',microtime(true) - $t);
/*
echo '<pre>';
ob_start();
print_r($template);
echo htmlspecialchars(ob_get_clean());

echo '</pre>';
*/
function matching(\Jungle\RegExp\Template $template, $subject, $index){
	static $template_;
	if($template_ !== $template){
		$template_ = $template;

		echo '<div>';
		echo "<p> <b>Template</b>: <br/><span style='font-size:24px;color:#baff79;background:black;padding:3px;letter-spacing: 1px;'>" . htmlspecialchars($template->getDefinition()) . "</span></p>";
		echo "<p> <b>Options</b>: <br/><pre style='font-size:11px;color:#baff79;background:black;padding:1px;'>" . htmlspecialchars(print_r($template->getPlaceholderOptions(),1)) . "</pre></p>";
		echo "<p> <b>RegexDebug</b>: <br/><span style='color:#b5ff64;background:black;padding:3px;letter-spacing: 1px;'>" . htmlspecialchars($template->getRegex()) . "</span></p>";
		echo '</div>';


	}

	$result = $template->match($subject);
	$rendered = $result!==false?$template->render($result):false;
	$matched = $rendered?$template->match($rendered):false;

	echo "
	<div style='border:1px solid;padding:5px;margin:10px;'>
		<h2> <b>Subject[".($index+1)."]</b>: <br/><span style='color:cyan;background:black;padding:3px;letter-spacing: 1px;'>".$subject."</span></h2>
		<div style='border:1px solid green;padding:5px;'>
		<h3>Match</h3>
	";
	echo '<pre  style="color:#1b9826;background:black;padding:10px;">';
	ob_start();
	var_dump($result);
	echo htmlspecialchars(ob_get_clean());
	echo '</pre></div>';

	if($result === false){
		echo '<p style="color:#f20000;background:black;"> <b>Not Matched</b> </p>';
	}else{

		echo '<div style=\'border:1px solid cadetblue;padding:5px;\'><h3> <b>Render result: </b> </h3>';
		echo '<pre style="color:darkcyan;background:black;padding:10px;">';
		ob_start();
		var_dump($rendered);
		echo htmlspecialchars(ob_get_clean());
		echo '</pre></div>';


		echo '<div style="border:1px solid #8fcb49;padding:5px;"><h3> <b>Match rendered!: </b> </h3>';
		echo '<pre  style="color:#a1d211;background:black;padding:10px;">';
		ob_start();
		var_dump($matched);
		echo htmlspecialchars(ob_get_clean());
		echo '</pre></div>';

		if($matched === $result){
			echo '<h1>Result === Matched</h1>';
		}

	}
	echo '</div>';
}