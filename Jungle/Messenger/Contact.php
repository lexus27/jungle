<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 30.12.2015
 * Time: 22:30
 */
namespace Jungle\Messenger {


	/**
	 * Class Destination
	 * @package Jungle\Messenger
	 */
	abstract class Contact implements IContact{

		/** @var  string */
		protected $address;

		/**
		 * @param mixed $address
		 * @return mixed
		 */
		public function setAddress($address){
			$this->address = $address;
		}

		/**
		 * @return mixed
		 */
		public function getAddress(){
			return $this->address;
		}
	}
}

