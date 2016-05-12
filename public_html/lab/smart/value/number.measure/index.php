<?php require __DIR__ . '/../base.php';

use Jungle\Smart\Value\Measure;
use Jungle\Smart\Value\Measure\Unit;
use Jungle\Smart\Value\Measure\UnitType;

/**
 * Test measurement
 */

echo '<section>';
echo '<h2>Test Measurement conversion</h2>';
echo '<pre>
<h2>ООП определение:</h2>
$distance = (new UnitType())
	->setName(\'distance\')
	->addUnit((new Unit())->setName(\'mm\'))
	->addUnit((new Unit())->setName(\'cm\')->associate(\'10mm\'))
	->addUnit((new Unit())->setName(\'inch\')->associate(\'2.4cm\'))
	->addUnit((new Unit())->setName(\'dm\')->associate(\'10cm\'))
	->addUnit((new Unit())->setName(\'m\')->associate(\'100cm\'))
	->addUnit((new Unit())->setName(\'km\')->associate(\'1000m\'))
	->addUnit((new Unit())->setName(\'nmi\')->associate(\'1852m\'));
<h2>Упрощенное определение единиц измерения в типе:</h2>
$distance = (new UnitType())->setName(\'distance\');
$distance([
	"mm"    => "",
	"cm"    => "10mm",
	"inch"  => "24mm",
	"dm"    => "10cm",
	"m"     => "100cm",
	"km"    => "1000m",
	"nmi"   => "1852m",
]);
<h2>Или:</h2>
$distance = (new UnitType())->setName(\'distance\');
$distance["mm"]   = 1;
$distance["cm"]   = "10mm";
$distance["inch"] = "2.4cm";
$distance["dm"]   = "10cm";
$distance["m"]    = "100cm";
$distance["km"]   = "1000m";
$distance["nmi"]  = "1852m";
</pre>';


/**
 * ООП определение единиц измерения в типе
 */
$distance = (new UnitType())
	->setName('distance')
	->addUnit((new Unit())->setName('mm'))
	->addUnit((new Unit())->setName('cm')->associate('10mm'))
	->addUnit((new Unit())->setName('inch')->associate('2.4cm'))
	->addUnit((new Unit())->setName('dm')->associate('10cm'))
	->addUnit((new Unit())->setName('m')->associate('100cm'))
	->addUnit((new Unit())->setName('km')->associate('1000m'))
	->addUnit((new Unit())->setName('nmi')->associate('1852m'));

$time = (new UnitType())
	->setName('time')
	->addUnit((new Unit())->setName('ns'))
	->addUnit((new Unit())->setName('us')->associate('1000ns'))
	->addUnit((new Unit())->setName('ms')->associate('1000us'))
	->addUnit((new Unit())->setName('s')->associate('1000ms'))
	->addUnit((new Unit())->setName('min')->associate('60s'))
	->addUnit((new Unit())->setName('h')->associate('60min'))
	->addUnit((new Unit())->setName('d')->associate('24h'))
	->addUnit((new Unit())->setName('w')->associate('7d'))
	->addUnit((new Unit())->setName('m')->associate('30.4167d'))
	->addUnit((new Unit())->setName('y')->associate('12m'));

$extenders = [
	'Конвертируем в Сантиметры'   => function(Measure $measure){
		$measure->primary('cm');
	},
	'Конвертируем в Метры'   => function(Measure $measure){
		$measure->primary('m');
	},
	'Конвертируем вторичную величину в Секунду'    => function(Measure $measure){
		$measure->secondary('s');
	},
	'Конвертируем вторичную величину в Час'      => function(Measure $measure){
		$measure->secondary('h');
	},
	'Прибавим 20 Метров в секунду (20м/с)'   => function(Measure $measure){
		$measure->increment('20m/s');
	},
	'Прибавим просто 30 сантиметров' => function(Measure $measure){
		$measure->increment('30cm');
	},
	'Прибавить просто 10 единиц в значение'  => function(\Jungle\Smart\Value\Number $measure){
		$measure->increment(10);
	},
	'Конвертировать в Метры в Секунду (м/с)'       => function(Measure $measure){
		$measure->convert('m/s');
	},
];

/** @var Measure[] $m */
$base = new Measure('16m/s',$distance,$time);
$base->setRoundPrecision(4);
$m = [];
$m[] = [
	'value'     => $base,
	'title'     => 'Базовое значение от которого мы сделаем первого потомка'
];
$m[] = 'Прибавить просто 10 единиц в значение';
$m[] = 'Прибавить просто 10 единиц в значение';
$m[] = 'Прибавить просто 10 единиц в значение';
$m[] = 'Прибавить просто 10 единиц в значение';
$m[] = 'Конвертируем вторичную величину в Час';
$m[] = 'Конвертируем в Сантиметры';
$m[] = 'Конвертируем в Метры';
$m[] = 'Прибавим 20 Метров в секунду (20м/с)';
$m[] = 'Конвертируем вторичную величину в Секунду';
$m[] = 'Конвертировать в Метры в Секунду (м/с)';
$m[] = 'Прибавим просто 30 сантиметров';
foreach($m as $i => & $a){
	if(is_string($a)){
		$r = [];
		$r['title'] = $a;
		$r['value'] = $m[$i-1]['value']->extend($extenders[$a]);
		$a = $r;
	}
}
echo '<div style="position:relative;width:100%;text-align: center;">';
echo '<div style="display:inline-block;">';
echo '<style>td{padding:4px 10px;}</style>';
echo '<table><caption><h1>Таблица наследственности!</h1></caption><thead><tr><th>Уровень</th><th>Действие</th><th>Результат</th></thead></tr><tbody>';
foreach($m as $i => $v){
	echo '<tr>
			<td>'.($i+1).'</td>
			<td><strong>'.$v['title'].'</strong></td>
		    <td><span style="font-size:24px;font-weight:bold;">'.$v['value'].'</span></td>
		 </tr>';
}
echo '</tbody></table>';
echo '</div></div>';
echo '</section>';


$o = function($arg1,$arg2){
	return $arg1 === true;
};
$reflection = new ReflectionObject($o);