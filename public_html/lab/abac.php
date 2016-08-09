<?php
use Jungle\User\AccessControl\Context;
use Jungle\User\AccessControl\Manager;
use Jungle\User\AccessControl\Policy\PolicyElement;
use Jungle\User\AccessControl\Policy\PolicyGroup;
use Jungle\User\AccessControl\Policy\Rule;
use Jungle\User\AccessControl\Policy\Target;

include './loader.php';

echo '<pre>';
/**
 *
 * Normal: Work with documents
 * Camelize: WorkWithDocuments
 * Uncamelize: work_with_documents
 *
 */
$rules = [
	(new Rule())->setName('Permit')->setEffect(Rule::PERMIT),
	(new Rule())->setName('Deny')->setEffect(Rule::DENY),
	(new Rule())->setName('Access in work time')
		->setCondition('[Scope.time.week_day] in [TIME.WORK_DAYS]')
		->setEffect(Rule::PERMIT),
	(new Rule())->setName('Access in weekend')
		->setCondition('[Scope.time.week_day] in [TIME.WEEK_END_DAYS]')
		->setEffect(Rule::PERMIT),
	(new Rule())->setName('Access in first half of year')
		->setCondition('[TIME.SEASONS.FIRST_HALF]')
		->setEffect(Rule::PERMIT)
];
$policies = [

	(new PolicyElement('Запрещено разработчикам в выходные дни'))
		->setTarget((new Target())
			->anyOf('[Scope.time.week_day] in [TIME.WEEK_END_DAYS]')
			->allOf(['[User.group] = Developer'])
		)
		->setObligation(function(\Jungle\User\AccessControl\Policy\MatchResult $result){
			echo 'Доступ закрыт: ['.$result.']'.$result->getMatchable()->getName().' <h3>Разработчики должны отдыхать в это время</h3>';
		})
		->setEffect(false),

	(new PolicyElement('Запрещено оффис менеджерам в выходные дни'))
		->setTarget((new Target())
			->anyOf('[Scope.time.week_day] in [TIME.WEEK_END_DAYS]')
			->allOf(['[User.group] = OfficeManager'])
		)
		->setObligation(function(\Jungle\User\AccessControl\Policy\MatchResult $result){
			echo 'Доступ закрыт: ['.$result.']'.$result->getMatchable()->getName().' <h3>Отдыхай пока можешь!</h3>';
		})
		->setEffect(false),




	(new PolicyGroup('Work with Documents'))
		->setTarget(
			(new Target())->anyOf('[Object::class] = Document')
		)
		->setCombiner('first_applicable_delegate')
		->addPolicy(
			(new PolicyGroup('Administrators'))
				->setTarget((new Target())->anyOf('[User.group] = Administrator'))
				->setObligation(function(){
					echo 'Вы администратор! и вы успешно вошли на сайт!';
				})
				->setCombiner('first_applicable_delegate')
				->addPolicy(
					(new PolicyElement('Create'))->setEffect(true)
						->setTarget((new Target())->anyOf('[Action.name] = Create'))
				)

				->addPolicy(
					(new PolicyElement('Read'))->setEffect(true)
						->setTarget((new Target())->anyOf('[Action.name] = Read'))
				)

				->addPolicy(
					(new PolicyElement('Update'))->setEffect(true)
						->setTarget((new Target())->anyOf('[Action.name] = Update'))
				)

				->addPolicy(
					(new PolicyElement('Delete'))->setEffect(true)
						->setTarget((new Target())->anyOf('[Action.name] = Delete'))
				)
		)
		->addPolicy(
			(new PolicyGroup('Moderators'))
				->setTarget((new Target())->anyOf('[User.group] = Moderator'))
				->setCombiner('first_applicable_delegate')
				->addPolicy(
					(new PolicyElement('Create'))->setEffect(true)
						->setTarget((new Target())->anyOf('[Action.name] = Create'))
				)

				->addPolicy(
					(new PolicyElement('Read'))->setEffect(true)
						->setTarget((new Target())->anyOf('[Action.name] = Read'))
				)

				->addPolicy(
					(new PolicyElement('Update'))->setEffect(true)
						->setTarget((new Target())->anyOf('[Action.name] = Update'))

				)

				->addPolicy(
					(new PolicyElement('Delete'))->setEffect(true)
						->setTarget((new Target())->anyOf('[Action.name] = Delete'))
				)
		)
		->addPolicy(
			(new PolicyGroup('Anonymous'))->setEffect(true)
				->setTarget(
					(new Target())
						->anyOf(['[User.group] = Anonymous','![User]','![User.id]'])
						->allOf(['[Action.name] = Read'])
				)
		)->addPolicy(
			(new PolicyGroup('Developers'))->setEffect(true)
				->setTarget((new Target())->anyOf('[User.group] = Developer'))
				->setCombiner('first_applicable_delegate')
				->addPolicy(
					(new PolicyElement('Create'))->setEffect(true)
						->setTarget((new Target())->anyOf('[Action.name] = Create'))
				)

				->addPolicy(
					(new PolicyElement('Read'))->setEffect(true)
						->setTarget((new Target())->anyOf('[Action.name] = Read'))
				)

				->addPolicy(
					(new PolicyElement('Update'))->setEffect(true)
						->setTarget((new Target())->anyOf('[Action.name] = Update'))
				)

				->addPolicy(
					(new PolicyElement('Delete'))->setEffect(true)
						->setTarget((new Target())->anyOf('[Action.name] = Delete'))
				)
		)
];


$manager = new Manager();
$policyAdapter = new \Jungle\User\AccessControl\Adapter\PolicyAdater\Memory();
foreach($rules as $rule){
	$policyAdapter->addRule($rule);
}
foreach($policies as $policy){
	$policyAdapter->addPolicy($policy);
}
$manager->setPolicyAdapter($policyAdapter);

$manager->getContextAdapter()->setUser([
	'id'    => 1,
	'name'  => 'Alexey',
	'login' => 'Kutuz27',
	'group' => 'Administrator',
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
var_dump($manager->enforce('Create','[class: Document]'));