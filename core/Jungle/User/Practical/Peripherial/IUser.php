<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.02.2016
 * Time: 11:49
 */
namespace Jungle\User\Practical\Peripherial {

	/**
	 *
	 *
	 * @Experimental-IUser
	 *
	 *
	 *
	 * Interface IUser
	 * @package Jungle\User
	 *
	 * @property $first_name
	 * @property $last_name
	 *
	 * @property $birth_time
	 *
	 *
	 */
	interface IUser{

		public function getName();

		public function getSurname();

		/** @made-method        */

		public function getBirthTime();

		/** @location-method    */

		public function getCountry();

		public function getCity();

		public function getStreet();

		public function getHouseNumber();

		public function getQuarterNumber();

		public function getMobilephone();

		/** Money info */
		public function getCreditCardNumber();

		public function getMoneyCash();


	}
}

