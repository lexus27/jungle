<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 13.02.2016
 * Time: 15:56
 */
namespace Jungle\User\Peripheral {

	/**
	 * Interface ILocation
	 * @package Jungle\User
	 */
	interface ILocation{

		/**
		 * @return mixed
		 */
		public function getCountry();

		/**
		 * @return mixed
		 */
		public function getCity();

		/**
		 * @return mixed
		 */
		public function getStreet();

		/**
		 * @return mixed
		 */
		public function getHouse();

		/**
		 * @return mixed
		 */
		public function getQuarter();

	}
}

