<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 13.02.2016
 * Time: 15:51
 */
namespace Jungle\User\Peripheral {

	/**
	 * Interface ICompany
	 * @package Jungle\User
	 */
	interface ICompany{

		/**
		 * @return mixed
		 */
		public function getName();

		/**
		 * Номер ИНН
		 * @return mixed
		 */
		public function getTaxNumber();

		/**
		 * Представительство
		 * @return mixed
		 */
		public function getRepresentation();

		/**
		 * Владелец
		 * @return mixed
		 */
		public function getOwner();

		/**
		 * Бухгалтерия
		 * @return mixed
		 */
		public function getAccounting();

		/**
		 * Время регистрации компании
		 * @return mixed
		 */
		public function getMadeTime();

		/**
		 * Описание компании
		 * @return mixed
		 */
		public function getDescription();

		/**
		 * Сфера деятельности
		 * @return mixed
		 */
		public function getActivitySphere();

	}
}

