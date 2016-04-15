<?php require __DIR__ . '/../base.php';
use Jungle\Smart\Value\Color;

/**
 * Color value example
 */

Color::addColorType((new Jungle\Smart\Value\Color\HEX()));
Color::addColorType((new Jungle\Smart\Value\Color\HSL()));
Color::addColorType((new Jungle\Smart\Value\Color\RGB()));

echo '<section style="">';
echo '<h2>Color example </h2>';
echo '<p style="color:#333333">
Extending colors<br/>
Наследование цветов, 36 оттенков, по 2 типа светлый/темный.(и того 72 панели)
У каждой панели 1 основной цвет(Фон), и как мы видим 3 дополнительных: Тень, Границы, Текст
основной цвет , это единственный цвет заданый от "руки" человека,
остальное вычисляется посредством наследственного конфигуратора
тоесть все 3 доп. цвета наследуются каждый от основного цвета текущего оттенка
(всего 36 оттенков, эти оттенки были сгенерированы то же по принципу наследования,
для создания видимости многочисленности)
</p>';
$funcBorder = function(Color $color){
	$light = $color->getLight();
	if($light > 40){
		$color->darken(30);
	}else{
		$color->lighten(30);
	}
};
$funcColor = function(Color $color){
	$light = $color->getLight();
	if($light > 40){
		$color->darken(45);
	}else{
		$color->lighten(50);
	}
};

/** @var Color[] $c */
$max = 360;
$step = 10;
$darkLevel = 20;
$lightLevel = 60;
$maxIteration = $max / $step;


$hueFunc = function(Color $c) use($step){
	$c->hueIncrement($step);
};
$bsFunc = function(Color $c){
	$oldLight = $c->getLight();
	$c->setLight(100 - $oldLight);
	$c->desaturate(20);
	$c->hueDecrement(30);
	$light = $c->getLight();
	if($light > 40){
		$c->darken(10);
	}
};


$base = new Color('hsla(0,100%,10%,1)');
$c = [];

for($i=0;$i<$maxIteration;$i++){
	$lastI = $i-1;
	if(!isset($c[$lastI])){
		$last = $base;
	}else{
		$last = $c[$lastI];
	}
	$c[$i] = $last->extend($hueFunc);
}
function tpl($heading,$bg,$border,$font,$boxShadow){
	return <<<HTML
		<div
			style="
				box-shadow:-50px 0 0 {$boxShadow};
				color:{$font};
				padding:10px;
				margin:5px 50px;
				background: {$bg};
				border:3px solid {$border};
		">
			<h2>{$heading}</h2>
			background[BASED]: {$bg},</br>
			border: {$border},</br>
			text: {$font}
		</div>
HTML;
}
foreach($c as $i => $a){

	$bg      = $a->setLight($darkLevel);
	$border  = $bg->extend($funcBorder);
	$font    = $bg->extend($funcColor);
	$bs      = $bg->extend($bsFunc);

	echo '<h1>Complex '.($i+1).'</h1>';
	echo tpl('Dark',$bg,$border,$font,$bs);
	$bg      = clone $a;
	$bg      = $bg->setLight($lightLevel);
	$border  = $bg->extend($funcBorder);
	$font    = $bg->extend($funcColor);
	$bs      = $bg->extend($bsFunc);
	echo tpl('Light',$bg,$border,$font,$bs),'';
}

echo '</section>';


