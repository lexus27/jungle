<?php
/**
 *
 * Задача:
 *  Access Control/
 *  Role - Resource - Action (Allow|Deny)
 *
 *  Реальность:
 *      Пользователь состоит в группе пользователей.
 *          Role{Name,Description}
 *
 *      Resources:
 *
 *          Пользователь просит доступ к Объекту ORM.
 *              Model-Specify-Name(ClassName)
 *              Concrete-Object:
 *                  CRUD:
 *                      Объект может быть CREATE
 *                      Объект может быть READ
 *                      Объект может быть UPDATE
 *                      Объект может быть DELETE
 *                  OWNED:
 *                      Объект может быть OWNED_BY(User_id)
 *              Access-From:
 *                  Владелец.
 *                  Группа владельца.
 *                  Группа пользователей(GROUP, ... , ...).
 *
 *
 *          Пользователь просит доступ к контроллеру.
 *
 *              Controller_FCN - Action_Name - Access(Allow|Deny)
 *
 *
 *
 *
 */
/**
 * @TargetTable
 * Interface IResource
 */
interface IResource{

	public function getName();

	public function getDescription();

}

/**
 * @User
 * Interface IRole
 */
interface IRole{

	public function getName();

	public function getDescription();

}

interface IAccess{

}



$permissions = [
	'User' => [

		'note'      => ['create','update','remove','get','list'],

		'message'   => ['create','update','remove','get','list']

	],
	'Anonymous' => [

		'note'      => ['get','list'],

		'message'   => ['get','list']

	]
];


/**
 * Interface IRule
 */
interface IRule{

	/**
	 * Цель
	 * @return mixed
	 */
	public function getTarget();

	/**
	 * Условие
	 * @return mixed
	 */
	public function getCondition();

	/**
	 * Эффект
	 * @return mixed
	 */
	public function getEffect();

	/**
	 * Обязательства
	 * @return mixed
	 */
	public function getObligation();

	/**
	 * Рекоминдации
	 * @return mixed
	 */
	public function getAdvice();


}

/**
 * Interface IPolicy
 */
interface IPolicy{

	public function getTarget();

	public function getRules();

	public function getAlgorithm();

	public function getObligation();

	public function getAdvice();

}
