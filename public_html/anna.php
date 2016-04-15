<?php

$peoples_columns = [
	'ФИО',
	'IQ',
	'Кол-во Ног',
	'Кол-во Рук',
	'Голов',
	'Титьки',
	'Месячные',
	'Предметы',
	'Дополнительно'
];
$peoples = [

	[
		'Анна Александровна',
		'1000',
		2,
		2,
		1,
		2.7,
		20,
		[
			'Помада',
			'Тушь',
			'Тональный крем',
			'Ножницы',
			'Барахло'
		],
		'Дополнительно 1'
	],
	[
		'Алексей Кутузов',
		'1000',
		2,
		2,
		1,
		2.7,
		20,
		[
			'Помада',
			'Тушь',
			'Тональный крем',
			'Ножницы',
			'Барахло'
		],
		'Дополнительно 3'
	],
	[
		'Леонид Кутузов',
		'1000',
		2,
		2,
		1,
		2.7,
		20,
		[
			'Помада',
			'Тушь',
			'Тональный крем',
			'Ножницы',
			'Барахло'
		],
		'Дополнительно 2'
	]

];

$html = '<!DOCTYPE HTML>
<html>
 <head>
  <meta charset="utf-8">
  <title>Человек</title>
  <style type="text/css">
   * {
    font-family: Impact, fantasy, sans-serif;
    font-stretch: ultra-expanded;
    color: #483D8B;
   }
  </style>
 </head>
 <body>';


$html.="<table>";


$html.="<caption>Железные люди икс</caption>";
$html.="<tr>";
foreach($peoples_columns as $i => $item){
	$html.="<th>$item</th>";
}
$html.="</tr>";
foreach($peoples as $i => $item){
	$html.="<tr>";
	foreach($item as $key => $value){
		if(is_array($value)){
			$html.="<td>".implode(', ',$value)."</td>";
		}else{
			$html.="<td>$value</td>";
		}
	}
	$html.="</tr>";
}






$html.="</table>";



 $html.='</body>
</html>';
echo $html;

/*
foreach($anna['kosmetica'] as $i => $item){
	echo $item." $i\r\n";
}
*/


/**
$kosmetica = [
	'Помада',
	'Тушь',
	'Тональный крем',
	'Ножницы',
	'Барахло'
];




//

$kosmetica[] = 'Мамин подарок';

//

echo '<pre>';

foreach($kosmetica as $i => $item){
	echo $item." $i\r\n";
}

$kosmetica[] = 'Украшение';
*/
