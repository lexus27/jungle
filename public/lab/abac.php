<?php
use Jungle\User\AccessControl\Adapter\PolicyAdater\Memory as MemoryPolicyAdapter;
use Jungle\User\AccessControl\Context;
use Jungle\User\AccessControl\Manager;
use Jungle\User\AccessControl\Matchable;

include './loader.php';

$manager = new Manager();

$combiner_settings = [

	'delegate' => [
		'default' => 'not_applicable',
		'applicable' => [
			'early'     => true,
			'effect'    => '{current}'
		],
	],

	'delegate_same' => [
		'default' => '{same}',
		'applicable' => [
			'early'     => true,
			'effect'    => '{current}'
		],
	],

	'dispute' => [
		'default'   => '{same}',
		'empty'     => '{same}',
		'applicable' => [
			'check'     => '{!same}',
			'early'     => true,
			'effect'    => '{current}'
		],
	],
	'dispute_all' => [
		'default'   => '{same}',
		'empty'     => '{same}',
		'applicable' => [
			'check' => [[
				'check'     => '{!same}',
				'effect'    => '{current}'
			],[
				'check'     => '{same}',
				'early'     => true,
				'effect'    => '{current}'
			]]
		],
	],


	'permit_by_permit' => [
		'return_only'   => 'permit',
		'default'       => 'not_applicable',
		'empty'         => 'not_applicable',
		'deny'          => [
			'early'         => true,
			'effect'        => 'not_applicable'
		],
		'permit'        => [
			'effect'        => '{current}'
		],
	],

	'deny_by_deny' => [
		'return_only'   => 'deny',
		'default'       => 'not_applicable',
		'empty'         => 'not_applicable',
		'deny'          => [
			'effect'        => '{current}'
		],
		'permit'        => [
			'early'         => true,
			'effect'        => 'not_applicable'
		],
	],


	'same_by_same' => [
		'default'       => 'not_applicable',
		'empty'         => 'not_applicable',
		'applicable' => [
			'check'     => [[
				'check'     => '{!same}',
				'early'     => true,
				'effect'    => 'not_applicable'
			],[
				'check'     => '{same}',
				'effect'    => '{same}'
			]]
		],
	],

	'same' => [

		'default'   => '{same}',
		'empty'     => '{same}',
		'applicable' => [
			'check'     => '{!same}',
			'early'     => true,
			'effect'    => '{current}'
		],

	],

	'same_only' => [
		'default'=> '{same}',
		'not_applicable' => [
			'effect'    => '{current}'
		],
		'applicable' => [
			'check'     => '{!same}',
			'early'     => true,
			'effect'    => '{current}'
		]
	],




];

$resolver = new Matchable\Resolver\ConditionResolver();
$manager->setConditionResolver($resolver);

$manager->setCombiner('delegate',  new Matchable\Combiner($combiner_settings['delegate']));
$manager->setCombiner('delegate_same',  new Matchable\Combiner($combiner_settings['delegate_same']));
$manager->setCombiner('same',  new Matchable\Combiner($combiner_settings['same']));
$manager->setCombiner('same_only',  new Matchable\Combiner($combiner_settings['same_only']));
$manager->setCombiner('same_by_same',  new Matchable\Combiner($combiner_settings['same_by_same']));
$manager->setCombiner('permit_by_permit',  new Matchable\Combiner($combiner_settings['permit_by_permit']));
$manager->setCombiner('deny_by_deny',  new Matchable\Combiner($combiner_settings['deny_by_deny']));
$manager->setCombiner('dispute',  new Matchable\Combiner($combiner_settings['dispute']));
$manager->setCombiner('dispute_all',  new Matchable\Combiner($combiner_settings['dispute_all']));

$manager->setDefaultCombiner('dispute_all');
$manager->setMainCombiner('dispute_all');

$manager->setDefaultEffect(Matchable::PERMIT);
$manager->setSameEffect(Matchable::DENY);

$aggregator = new MemoryPolicyAdapter(null);
$aggregator->build([

	'policies' => [[
		'effect' => true,
		'name' => 'Работа с Записками',
		'target' => [
			'all_of' => '[object::class] = Note',
		],
		'combiner' => 'delegate',
		'rules' => [[
			'condition' => '[user.group] = Administrators'
		],[
			'condition' => '[object.owner_id] = [user.id]'
		],[
			'condition' => '[object.public] = true'
		]]
	],[
		'effect'    => true,
		'name'      => 'Администраторы',
		'combiner'  => 'dispute',
		'obligation' => function(){
			echo 'Вседозволенные Администраторы';
		},
		'target'    => [
			'all_of'    => [
				'[user.group] = Administrators',
			]
		],
	],[
		'effect'    => false,
		'name' => 'Анонимы',
		'combiner'  => 'dispute',
		'target' => [
			'all_of' => [
				'[object::class] = Document',
				'[user.group] = Anonymous',
			]
		],
		'obligation' => function(){
			echo 'Мы запрещаем работать здесь анонимам';
		},
		'policies' => [[
			'effect' => true,
			'rules' => [[
				'condition' => '[action.name] = Read'
			]],
			'obligation' => function(){
				echo 'Просматривать можно';
			},
		],[
			'effect' => true,
			'rules' => [[
				'condition' => '[scope.time.week_day] in [TIME.WORK_DAYS]'
			]],
			'obligation' => function(){
				echo 'Слава богу будние дни';
			},
		]],


	],[
		'effect'    => false,
		'name'      => 'CurrentToken',
		'obligation' => function(){
			echo 'По токену Есть ограничения';
		},
		'combiner'  => 'same_by_same',
		'policies' => [[
			'effect' => true,
			'target' => [ 'all_of' => [ '[user.group] = Administrators', ], ],
			'obligation' => function(){
				echo 'Администраторам можно';
			},
		],[
			'effect' => true,
			'target' => [ 'all_of' => [ '[action.name] = Read', ], ],
			'obligation' => function(){
				echo 'Просмотр для токенов доступен';
			},
		],[
			'effect' => true,
			'combiner' => 'same_by_same',
			'rules' => [[ 'condition' => '[scope.time.week_day] in [TIME.WORK_DAYS]' ]],
			'obligation' => function(){
				echo 'В рабочие дни токен работает';
			},
		]],
	],/*[

		'name' => 'Deny',
		'effect' => false,
		'target' => [
			'all_of' => [
				'[user.group] != Administrators'
			]
		],
		'obligation' => function(){
			echo 'Технические работы';
		},
	]*/]

]);
$manager->setAggregator($aggregator);

$context = new Context();
$context->setProperties([

	'user' => [
		'id'    => 1,
		'name'  => 'Alexey',
		'login' => 'Kutuz27',
		'group' => 'Anonymous',
		'email' => 'lexus.1995@mail.ru',
		'photo' => '/user/123223/avatar.jpg'
	],

	'route' => [
		'module'        => null,
		'controller'    => null,
		'action'        => null,
		'params'        => null
	],

	'client' => [
		'request' => & $_REQUEST,
		'server'  => & $_SERVER,
		'cookies' => & $_COOKIE,
		'session' => & $_SESSION
	]

], true);
$manager->setContext($context);


echo '<br/>';


$object = new Context\ObjectAccessor([
	'class' => 'Note',
	'phantom' => [
		'owner_id' => 2,
	],
	'predicate_effect' => true,
]);


$result = $manager->enforce('Read',$object, true);
if($result->isAllowed() === $object->getPredicateEffect()){
	echo '<p><pre>';
	var_dump($object->getSelectConditions());
	echo '</pre></p>';
}else{
	echo '<p><pre>';
	var_dump($result->getEffect());
	echo '</pre></p>';
}
