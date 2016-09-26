<?php
use Jungle\User\AccessControl\Context;
use Jungle\User\AccessControl\Manager;

include './loader.php';

echo '<pre>';

$manager = new Manager();
$manager->setBasedEffect(\Jungle\User\AccessControl\Matchable::PERMIT, true);
$policyAdapter = new \Jungle\User\AccessControl\Adapter\PolicyAdater\Memory();
/*
$policyAdapter->fromArray([

	'rules' => [ [
		'name' => 'Permit',
		'effect' => Rule::PERMIT
	],[
		'name' => 'Deny',
		'effect' => Rule::DENY
	],[
		'name' => 'Access in work time',
		'condition' => '[Scope.time.week_day] in [TIME.WORK_DAYS]',
		'effect' => Rule::PERMIT
	],[
		'name' => 'Access in weekend',
		'condition' => '[Scope.time.week_day] in [TIME.WEEK_END_DAYS]',
		'effect' => Rule::PERMIT
	],[
		'name' => 'Access in first half of year',
		'condition' => '[TIME.SEASONS.FIRST_HALF]',
		'effect' => Rule::PERMIT
	]],


	'policies' => [[
		'name' => 'Запрещено разработчикам в выходные дни',
		'effect' => false,
		'target' => [
			'any_of' => '[Scope.time.week_day] in [TIME.WEEK_END_DAYS]',
			'all_of' => '[User.group] = Developer'
		],
		'obligation' => function(\Jungle\User\AccessControl\Policy\MatchResult $result){
			echo 'Доступ закрыт: ['.$result.']'.$result->getMatchable()->getName().' <h3>Разработчики должны отдыхать в это время</h3>';
		}
	],[
		'name' => 'Запрещено оффис менеджерам в выходные дни',
		'effect' => false,
		'target' => [
			'any_of' => '[Scope.time.week_day] in [TIME.WEEK_END_DAYS]',
			'all_of' => '[User.group] = OfficeManager'
		],
		'obligation' => function(\Jungle\User\AccessControl\Policy\MatchResult $result){
			echo 'Доступ закрыт: ['.$result.']'.$result->getMatchable()->getName().' <h3>Отдыхай пока можешь!</h3>';
		}
	],[
		'name' => 'Запрещено оффис менеджерам в выходные дни',
		'effect' => false,
		'target' => [
			'any_of' => '[Scope.time.week_day] in [TIME.WEEK_END_DAYS]',
			'all_of' => '[User.group] = OfficeManager'
		],
		'obligation' => function(\Jungle\User\AccessControl\Policy\MatchResult $result){
			echo 'Доступ закрыт: ['.$result.']'.$result->getMatchable()->getName().' <h3>Отдыхай пока можешь!</h3>';
		}
	],[
		'name' => 'Work with Documents',
		'target' => [ 'any_of' => '[Object::class] = Document' ],
		'combiner' => 'first_applicable_delegate',
		'policies' => [[
			'name' => 'Administrators',
			'effect' => true,
			'target' => ['any_of' => '[User.group] = Administrator'],
			'combiner' => 'first_applicable_delegate',
			'obligation' => function(){
				echo 'Привет Админ!';
			},
			'policies' => [[
				'name' => 'Create',
				'effect' => true,
				'target' => ['any_of' => '[Action.name] = Create']
			],[
				'name' => 'Read',
				'effect' => true,
				'target' => ['any_of' => '[Action.name] = Read']
			],[
				'name' => 'Update',
				'effect' => true,
				'target' => ['any_of' => '[Action.name] = Update']
			],[
				'name' => 'Delete',
				'effect' => true,
				'target' => ['any_of' => '[Action.name] = Delete']
			]]
		],[
			'name' => 'Moderators',
			'effect' => true,
			'target' => ['any_of' => '[User.group] = Moderator'],
			'combiner' => 'first_applicable_delegate',
			'obligation' => function(){
				echo 'Привет Модер!';
			},
			'policies' => [[
				'name' => 'Create',
				'effect' => true,
				'target' => ['any_of' => '[Action.name] = Create']
			],[
				'name' => 'Read',
				'effect' => true,
				'target' => ['any_of' => '[Action.name] = Read'] // Target: ([ref] = val || [ref] = val) && ([ref] = val && [ref] = val)
			],[
				'name' => 'Update',
				'effect' => true,
				'target' => ['any_of' => '[Action.name] = Update']
			],[
				'name' => 'Delete',
				'effect' => true,
				'target' => ['any_of' => '[Action.name] = Delete']
			]]
		],[
			'name' => 'Anonymous',
			'effect' => true,
			'advice' => function(){
				Echo 'Конечно, лучше авторизоваться!';
			},
			'requirements' => function(){
				Echo 'Вы неможете совершить это действие, пожалуйста пройдите авторизацию';
			},
			'target' => [
				'any_of' => ['[User.group] = Anonymous','![User]','![User.id]']
			],
			'rules' => [[
				'condition' => '[Action.name] = Read'
			]]
		],[
			'name' => 'Developers',
			'effect' => true,
			'target' => ['any_of' => '[User.group] = Developer'],
			'combiner' => 'first_applicable_delegate',
			'obligation' => function(){
				echo 'Привет Разраб!';
			},
			'policies' => [[
				'name' => 'Create',
				'effect' => true,
				'target' => ['any_of' => '[Action.name] = Create']
			],[
				'name' => 'Read',
				'effect' => true,
				'target' => ['any_of' => '[Action.name] = Read']
			],[
				'name' => 'Update',
				'effect' => true,
				'target' => ['any_of' => '[Action.name] = Update']
			],[
				'name' => 'Delete',
				'effect' => true,
				'target' => ['any_of' => '[Action.name] = Delete']
			]]
		]],
	]]

]);*/

$policyAdapter->fromArray([

	'policies' => [[

		'name' => 'Анонимы',
		'target' => [
			'all_of' => [
				'[Object::class] = Document',
				'[User.group] = Anonymous',
			]
		],
		'obligation' => function(){
			echo 'Мы запрещаем работать здесь анонимам';
		},
		'effect'    => false,
		'combiner'  => 'effect_same_only',
		'policies' => [[
             'target' => [
                 'all_of' => '[Action.name] = Read'
             ],
             'effect' => true,
             'obligation' => function(){
                 echo 'Просматривать можно';
             },
             'rules' => [[
                 'condition' => '[Scope.time.week_day] in [TIME.WORK_DAYS]'
             ]],
		],[
			'effect' => true,
			'obligation' => function(){
				echo 'Слава богу будние дни';
			},
			'rules' => [[
				'condition' => '[Scope.time.week_day] in [TIME.WORK_DAYS]'
			]],
		]]

	],[
		'name'      => 'CurrentToken',
		'obligation' => function(){
			echo 'По токену Есть ограничения';
		},
		'effect'    => false,
		'combiner'  => 'EffectSameIfNotApplicable',
		'policies' => [[
			'obligation' => function(){
				echo 'Просмотр для токенов доступен';
			},
			'target' => [
				'all_of' => [
					'[Action.name] = Read',
				],
			],
			'effect' => true
		],[
			'obligation' => function(){
				echo 'В рабочие дни токен работает';
			},
			'rules' => [[
				'condition' => '[Scope.time.week_day] in [TIME.WORK_DAYS]'
			]],
			'effect' => true
		]],
	]]

]);

$manager->setPolicyAdapter($policyAdapter);

$manager->getContextAdapter()->setUser([
	'id'    => 1,
	'name'  => 'Alexey',
	'login' => 'Kutuz27',
	'group' => 'Anonymous',
	'email' => 'lexus.1995@mail.ru',
	'photo' => '/user/123223/avatar.jpg'
])->setAdditionVariables([
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
]);
echo '<br/>';
var_dump($manager->enforce('Update','[class: Document]'));