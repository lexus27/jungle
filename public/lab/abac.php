<?php
use Jungle\User\AccessControl\Adapter\PolicyAdater\Memory as MemoryPolicyAdapter;
use Jungle\User\AccessControl\Context;
use Jungle\User\AccessControl\Manager;
use Jungle\User\AccessControl\Matchable;


/**
 *
 *
 *
 * Определения
 * ---------------------------------------------
 * Контекст         - это источник информации на момент вычисления доступа. (Что?(Объект) Кто?(Пользователь) Зачем?(Применяемое действие), Где?(Данные окружения))
 * Вычисляемый      - Объект который можно вычислить благодаря Контексту и получить Результат вычисления,
 *                    субъективно, имеется ввиду что то одно из Правила, Политики или Группы политик.
 * Комбинатор       - метод перебора множества Вычисляемых, комбинирующий решения дочерних Вычисляемых поголовно,
 *                    таким образом чтобы дать результирующее решение контейнера.
 * Агрегатор        - контейнер содержащий множество Вычисляемых объектов и использующий Комбинатор для результата вычисления.
 * Цель             - это абстрактный барьер перед выполнением Агрегации, является провайдером Условий.
 * Правило          - это конкретный объект вычисляющий решение, также является провайдером одного Условия.
 * Условие          - заданная сверка двух значений, значения каждого операнда(стороны сравнения)
 *                    могут представлятся статично или ссылкой на Контекст.
 * Политика         - Агрегатор Правил, проверяет применимость Цели к Контексту, после чего выполняет дальнейшую Агрегацию.
 * Группа политик   - Агрегатор Политик, проверяет применимость Цели к Контексту, после чего выполняет дальнейшую Агрегацию.
 *
 *
 * Статусы возращаемые Вычисляемым
 * ----------------------------------------------------------------------------------------
 * NOT_APPLICABLE   (Не применимо)      - Условия Целей или Правил не соответствуют Контексту.
 * INDETERMINATE    (Не определено)     - Ошибка помешавшая выявить решение
 * PERMIT           (Разрешить) TRUE    - Разрешение
 * DENY             (Заретить)  FALSE   - Запрет
 *
 *
 * Итоговое решение о предоставлении прав доступа
 * ----------------------------------------
 * Итоговое решение может быть только PERMIT или DENY
 *
 * NOT_APPLICABLE   - это означает что ни один Вычисляемый не имел соответствий с Контекстом, поэтому итоговое значение
 *                    будет назначено исходя из базирования менеджера(Разрешающий(Permit) или Запрещающий(Deny))
 * INDETERMINATE    - склонен к отказу, т.к вычислить всё до конца не удастся изза ошибки на этапах связанных с вычислениями или завершения оных.
 *
 * NOT_APPLICABLE - Возможно интерпретировать из NULL значения, т.к NOT_APPLICABLE является переопределяемым от контейнера (итоговое)
 * INDETERMINATE - Возможно реализовать при помощи Исключений
 *
 *
 * Комбинаторы и Поведение
 * ---------------------------------------------
 * Комбинатор нужно подбирать достаточно скурпулезно в зависимости от задач авторизации действий.
 *
 * Вводные:
 * Агрегатор использует Комбинатор для вычисления своего решения.
 * Агрегатор базируется на эффекте.
 * Агрегатор может быть Не применим, это вычисляется при помощи Комбинирования дочерних Вычисляющих
 *
 * Предполагаемые принципы комбинирования:
 * А. [delegate]        Решение первого применимого дочернего делегируется как результат вычисления Агрегатора
 * Б. [delegate_same]   Аналогия [A], но Агрегатор всегда является Применимым, при не применимости дочерних, его эффект будет в приоритете.
 * В. [same_only]       Решение Агрегатора если минимум 1 дочерний равен Эффекту Агрегатора, и ни одного Противоположного
 * Г. [same_soft]       Аналогия [В], но допускаются Не применимые
 *
 * Структура принципа комбинирования:
 * Принцип комбинирования ложится на пошаговый перебор, каждый раз нового Вычисляемого
 * и на основе его Решения происходит реакции способные выдать итоговое решение, заранее или в конце итераций
 *
 * Реакции определяются следующими событиями
 *  Поведение при "Не применимых"
 *  Поведение при "Не определенных"
 *  Поведение при "Разрешающих"
 *  Поведение при "Запрещающих"
 *
 */



include './loader.php';



echo '<pre>';

$manager = new Manager();


/**
$policyAdapter->build([

	'rules' => [ [
		'name' => 'Permit',
		'effect' => Matchable::PERMIT
	],[
		'name' => 'Deny',
		'effect' => Matchable::DENY
	],[
		'name' => 'Access in work time',
		'condition' => '[Scope.time.week_day] in [TIME.WORK_DAYS]',
		'effect' => Matchable::PERMIT
	],[
		'name' => 'Access in weekend',
		'condition' => '[Scope.time.week_day] in [TIME.WEEK_END_DAYS]',
		'effect' => Matchable::PERMIT
	],[
		'name' => 'Access in first half of year',
		'condition' => '[TIME.SEASONS.FIRST_HALF]',
		'effect' => Matchable::PERMIT
	]],


	'policies' => [[
		'name' => 'Запрещено разработчикам в выходные дни',
		'effect' => false,
		'target' => [
			'any_of' => '[Scope.time.week_day] in [TIME.WEEK_END_DAYS]',
			'all_of' => '[User.group] = Developer'
		],
		'obligation' => function(\Jungle\User\AccessControl\Matchable\Result $result){
			echo 'Доступ закрыт: ['.$result.']'.$result->getMatchable()->getName().' <h3>Разработчики должны отдыхать в это время</h3>';
		}
	],[
		'name' => 'Запрещено оффис менеджерам в выходные дни',
		'effect' => false,
		'target' => [
			'any_of' => '[Scope.time.week_day] in [TIME.WEEK_END_DAYS]',
			'all_of' => '[User.group] = OfficeManager'
		],
		'obligation' => function(\Jungle\User\AccessControl\Matchable\Result $result){
			echo 'Доступ закрыт: ['.$result.']'.$result->getMatchable()->getName().' <h3>Отдыхай пока можешь!</h3>';
		}
	],[
		'name' => 'Запрещено оффис менеджерам в выходные дни',
		'effect' => false,
		'target' => [
			'any_of' => '[Scope.time.week_day] in [TIME.WEEK_END_DAYS]',
			'all_of' => '[User.group] = OfficeManager'
		],
		'obligation' => function(\Jungle\User\AccessControl\Matchable\Result $result){
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

]);
*/






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
		'group' => 'A',
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
	'class' => 'Document',
	'phantom' => [
		'owner_id' => 2,
	]
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
