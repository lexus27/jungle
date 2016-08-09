<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 13.02.2016
 * Time: 15:53
 */
namespace Jungle\User\Practical\Peripherial {

	interface IBirth{

		/**
		 * @return mixed
		 */
		public function getBirthLocation();

		/**
		 * @return int
		 */
		public function getBirthTime();

		/**
		 * @return int
		 */
		public function getBirthYear();

		/**
		 * @return int 1-12
		 */
		public function getBirthMoth();

		/**
		 * @return int - 0-31
		 */
		public function getBirthDay();

		/**
		 * @return int -
		 */
		public function getBirthConcreteTime();

	}
}

