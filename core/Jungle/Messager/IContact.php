<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.12.2015
 * Time: 22:27
 */
namespace Jungle\Messager {

	/**
	 * Interface IDestination
	 * @package Jungle\Messager
	 */
	interface IContact{

		/**
		 * @param mixed $address
		 * @return mixed
		 */
		public function setAddress($address);

		/**
		 * @return mixed
		 */
		public function getAddress();


	}
}

