<?php

require dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'loader.php';
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 21.03.2016
 * Time: 22:07
 */


/**
 *
 *
 *
 */
$persons = new \Jungle\DataOldRefactoring\DataMap\Collection();
$persons->setSchema((new \Jungle\DataOldRefactoring\DataMap\Schema())->setFields([

	(new \Jungle\DataOldRefactoring\DataMap\Schema\Field('id'))      ->setType('int'),
	(new \Jungle\DataOldRefactoring\DataMap\Schema\Field('name'))    ->setType('string'),
	(new \Jungle\DataOldRefactoring\DataMap\Schema\Field('family'))  ->setType('string'),
	(new \Jungle\DataOldRefactoring\DataMap\Schema\Field('city'))    ->setType('string'),
	/*
		(new \Jungle\Data\DataMap\Schema\Field('messages'))->many([
			'collection'    => $messages,
			'key'           => 'person_id'
		]),
	*/
],true)->setIndexes([

	(new \Jungle\DataOldRefactoring\DataMap\Schema\Index())
		->setField('id')
		->setType(\Jungle\DataOldRefactoring\DataMap\Schema\Index::TYPE_PRIMARY),

]));
$persons->setSource(new \Jungle\DataOldRefactoring\DataMap\Collection\Source\ArraySource([
	[   1,  'Виктор',      'Петров',       'Николаевск-на-амуре'   ],
	[   2,  'Елизавета',   'Григорьева',   'Владимир'              ],
	[   3,  'Илья',        'Пупкин',       'Хабаровск'             ],
	[   4,  'Николай',     'Велешуров',    'Казань'                ],
	[   5,  'Юлия',        'Шашлыкова',    'Владивосток'           ],
	[   6,  'Снежана',     'Бубкова',      'Николаевск-на-амуре'   ],
	[   7,  'Анастасия',   'Варыткова',    'Владимир'              ],
	[   8,  'Бельсажа',    'Елкина',       'Хабаровск'             ],
	[   9,  'Дмитрий',     'Нагиев',       'Казань'                ],
	[   10, 'Людмила',     'Башлыжева',    'Владивосток'           ],
	[   11, 'Виктория',    'Волкова',      'Москва'                ],
	[   12, 'Александр',   'Кутузов',      'Екатеринбург'          ],
	[   13, 'Константин',  'Резник',       'Новосибирск'           ],
	[   14, 'Алена',       'Бельженко',    'Благовещенск'          ],
	[   15, 'Елена',       'Погодин',      'Амурск'                ],
	[   16, 'Светлана',    'Кожушная',     'Николаевск-на-амуре'   ],
	[   17, 'Мария',       'Сибирякова',   'Брянск'                ],
	[   18, 'Пётр',        'Решетников',   'Луганск'               ],
	[   19, 'Элина',       'Гуртовая',     'Киев'                  ],
	[   20, 'Валерия',     'Лебедева',     'New york'              ],
	[   21, 'Диана',       'Попова',       'Париж'                 ],
	[   22, 'Валентина',   'Козловская',   'Лондон'                ],
	[   23, 'Евгения',     'Ткаченко',     'Берлин'                ],
	[   24, 'Карина',      'Тарасенко',    'Казань'                ],
	[   25, 'Иван',        'Смолькина',    'Владивосток'           ],
	[   26, 'Анна',        'Филлипова',    'Николаевск-на-амуре'   ],
	[   27, 'Сергей',      'Прелепин',     'Владимир'              ],
	[   28, 'Екатерина',   'Розумей',      'Хабаровск'             ],
	[   29, 'Влад',        'Боровиков',    'Казань'                ],
	[   30, 'Владимир',    'Шашлыкин',     'Владивосток'           ],
	[   31, 'Владислав',   'Приходько',    'Николаевск-на-амуре'   ],
	[   32, 'Вадим',       'Чернышев',     'Владимир'              ],
	[   33, 'Егор',        'Черезниченко', 'Хабаровск'             ],
	[   34, 'Евгений',     'Лапин',        'Казань'                ],
	[   35, 'Павел',       'Кобяков',      'Владивосток'           ],
]));

$persons2 = $persons->extend('id > 20');
echo '<pre>';
echo 'COUNT: '.$persons2->count();
echo '</pre>';
/**
 * Пример генерации страниц
 */
$total_count = $persons->count();
echo '<pre>';
echo '[ '.implode(', ',array_map(function($v){return var_export($v,true);},$persons->getFieldNames()))."]\r\n";
$i = 0;
$persons->sortBy('name');
$limit = 5;
$offset = 0;
$criteria = null;//\Jungle\Data\DataMap\Criteria::build('city = Хабаровск');
$total_rendered = 0;
do{
	$i++;
	$collection = $persons->collect($criteria,$limit,$offset);
	$count = count($collection);
	$total_rendered+= $count;
	echo "\r\nPage: $i (PageSize: $limit)\r\n".
	     'Count:'.$count."\r\n";
	foreach($collection as $person){
		echo '[ '.implode(', ',array_map(function($v){return var_export($v,true);},$persons->getRow($person)))."]\r\n";
	}
	$offset+= $limit;
}while($total_rendered < $total_count);

/**
 * @param $limit
 * @param $total_count
 * @return int
 */
function calculatePages($limit, $total_count){
	if($limit >= $total_count){
		return 1;
	}
	return ceil($total_count / $limit);
}

/**
 * @param int $page_index
 * @param $limit
 * @return int
 */
function calculateOffset($page_index = 0, $limit){
	return $page_index * $limit;
}












