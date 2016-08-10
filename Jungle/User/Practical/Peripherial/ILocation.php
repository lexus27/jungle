<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 13.02.2016
 * Time: 15:56
 */
namespace Jungle\User\Practical\Peripherial {

	/**
	 * Interface ILocation
	 * @package Jungle\User
	 */
	interface ILocation{

		/**
		 * @return int
		 */
		public function getLatitude();

		/**
		 * @return int
		 */
		public function getLongitude();


		
		/**
		 * @return string
		 */
		public function getCountry();
		
		/**
		 * @return string
		 */
		public function getRegion();
		
		/**
		 * @return string
		 */
		public function getCity();

		/**
		 * @return string
		 */
		public function getStreet();

		/**
		 * @return string
		 */
		public function getHouseNum();

		/**
		 * @return mixed
		 */
		public function getHousingNum();
		
		/**
		 * @return string
		 */
		public function getQuarter();

	}
}

