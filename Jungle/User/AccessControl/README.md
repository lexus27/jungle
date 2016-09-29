# ABAC: Attribute Based Access Control
Определения
---------------------------------------------
**Контекст**         - это источник информации на момент вычисления доступа. (Что?(Объект) Кто?(Пользователь) Зачем?(Применяемое действие), Где?(Данные окружения))
**Вычисляемый**      - Объект который можно вычислить благодаря Контексту и получить Результат вычисления, субъективно, имеется ввиду что то одно из Правила, Политики или Группы политик.
**Комбинатор**       - метод перебора множества Вычисляемых, комбинирующий решения дочерних Вычисляемых поголовно, таким образом чтобы дать результирующее решение контейнера.
**Агрегатор**        - контейнер содержащий множество Вычисляемых объектов и использующий Комбинатор для результата вычисления.
**Цель**             - это абстрактный барьер перед выполнением Агрегации, является провайдером Условий.
**Правило**          - это конкретный объект вычисляющий решение, также является провайдером одного Условия.
**Условие**          - заданная сверка двух значений, значения каждого операнда(стороны сравнения) могут представлятся статично или ссылкой на Контекст.
**Политика**         - Агрегатор Правил, проверяет применимость Цели к Контексту, после чего выполняет дальнейшую Агрегацию.
**Группа политик**   - Агрегатор Политик, проверяет применимость Цели к Контексту, после чего выполняет дальнейшую Агрегацию.


Статусы возращаемые Вычисляемым
----------------------------------------------------------------------------------------
_**NOT_APPLICABLE**_   (Не применимо)      - Условия Целей или Правил не соответствуют Контексту.
_**INDETERMINATE**_    (Не определено)     - Ошибка помешавшая выявить решение
_**PERMIT**_           (Разрешить) TRUE    - Разрешение
_**DENY**_             (Заретить)  FALSE   - Запрет


Итоговое решение о предоставлении прав доступа
----------------------------------------
Итоговое решение может быть только _PERMIT_ или _DENY_

_**NOT_APPLICABLE**_   - это означает что ни один Вычисляемый не имел соответствий с Контекстом, поэтому итоговое значение будет назначено исходя из базирования менеджера(Разрешающий(Permit) или Запрещающий(Deny))
_**INDETERMINATE**_    - склонен к отказу, т.к вычислить всё до конца не удастся изза ошибки на этапах связанных с вычислениями или завершения оных.

_**NOT_APPLICABLE**_ - Возможно интерпретировать из NULL значения, т.к NOT_APPLICABLE является переопределяемым от контейнера (итоговое)
_**INDETERMINATE**_ - Возможно реализовать при помощи Исключений

Примеры основного определения:
------------------------------

    <?php
    use Jungle\User\AccessControl\Adapter\PolicyAdater\Memory as MemoryPolicyAdapter;
    use Jungle\User\AccessControl\Context;
    use Jungle\User\AccessControl\Manager;
    use Jungle\User\AccessControl\Matchable;
    
    include './loader.php';
    
    $manager = new Manager();
    
    $resolver = new Matchable\Resolver\ConditionResolver();
    $manager->setConditionResolver($resolver);
    
    // .........$combiner_set declaration
    
    $manager->setCombiner('delegate',  new Matchable\Combiner($combiner_set['delegate']));
    $manager->setCombiner('delegate_same',  new Matchable\Combiner($combiner_set['delegate_same']));
    $manager->setCombiner('same',  new Matchable\Combiner($combiner_set['same']));
    $manager->setCombiner('same_only',  new Matchable\Combiner($combiner_set['same_only']));
    $manager->setCombiner('same_by_same',  new Matchable\Combiner($combiner_set['same_by_same']));
    $manager->setCombiner('permit_by_permit',  new Matchable\Combiner($combiner_set['permit_by_permit']));
    $manager->setCombiner('deny_by_deny',  new Matchable\Combiner($combiner_set['deny_by_deny']));
    $manager->setCombiner('dispute',  new Matchable\Combiner($combiner_set['dispute']));
    $manager->setCombiner('dispute_all',  new Matchable\Combiner($combiner_set['dispute_all']));
    
    $manager->setDefaultCombiner('dispute_all');
    $manager->setMainCombiner('dispute_all');
    
    $manager->setDefaultEffect(Matchable::PERMIT);
    $manager->setSameEffect(Matchable::DENY);
    
    
    // .........$aggregator main declaration
    // ............$context main declaration
    
    $manager->setAggregator($aggregator);
    $manager->setContext($context);

Примеры Набора политик доступа
------------------------------

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
    	],[
    
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
    	]]
    
    ]);

Контекст
--------

    $context = new Context();
    $context->setProperties([
    
        'user' => [
            'id'    => 1,
            'name'  => 'John',
            'login' => 'john.mail@site.com',
            'group' => 'Anonymous',
            'email' => 'john.mail@site.com',
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


Комбинаторы и Поведение
---------------------------------------------
_Комбинатор нужно подбирать достаточно скурпулезно в зависимости от задач авторизации действий._

#### Вводные:
`Агрегатор использует Комбинатор для вычисления своего решения.`
`Агрегатор базируется на эффекте.`
`Агрегатор может быть Не применим, это вычисляется при помощи Комбинирования дочерних Вычисляющих`

#### Предполагаемые принципы комбинирования:
А. `delegate`        Решение первого применимого дочернего делегируется как результат вычисления Агрегатора
Б. `delegate_same`   Аналогия [A], но Агрегатор всегда является Применимым, при не применимости дочерних, его эффект будет в приоритете.
В. `same_only`       Решение Агрегатора если минимум 1 дочерний равен Эффекту Агрегатора, и ни одного Противоположного
Г. `same_soft`       Аналогия [В], но допускаются Не применимые

#### Структура принципа комбинирования:
Принцип комбинирования ложится на пошаговый перебор, каждый раз нового Вычисляемого
и на основе его Решения происходит реакции способные выдать итоговое решение, заранее или в конце итераций

Реакции определяются следующими событиями
 `Поведение при "Не применимых"`
 `Поведение при "Не определенных"`
 `Поведение при "Разрешающих"`
 `Поведение при "Запрещающих"`
 
##### Примеры в массивах:

    $combiner_set = [
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

**Принцип был интерпретирован, переосмыслен и реализован по данной [статье](https://habrahabr.ru/company/custis/blog/258861)**

ORM и ABAC 
----------
ABAC предоставляет возможность, проверить права доступа на объект, до непосредственной выборки таковых из базы данных, посредством предварительной подготовки WHERE условий, которые должны совпадать для соответствия нужному решению ABAC Менеджера, таким образам результатом выборки будут объекты удовлетворяющие нужный эффект
Все решается путем сбора Предикатов из Условий которые были проверены.
##### Пример:
    
    $object = new Context\ObjectAccessor([
        'class' => 'Note',
        'phantom' => [
            'owner_id' => 2,
        ],
        'predicate_effect' = true,
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