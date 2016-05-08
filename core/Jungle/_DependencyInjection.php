<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 27.01.2016
 * Time: 20:52
 */
namespace Jungle {

	use Jungle\DependencyInjection\DependencyInjectionInterface;

	/**
	 * Class DependencyInjection
	 * @package Jungle
	 */
	class DependencyInjection implements DependencyInjectionInterface{

		/**
		 * @param $service_key
		 */
		public function get($service_key){

		}

		/**
		 * @param $service_key
		 * @param $service_definition
		 */
		public function set($service_key, $service_definition){

		}

		/**
		 * @param $service_key
		 */
		public function getShared($service_key){

		}

		/**
		 * @param $service_key
		 * @param $service_definition
		 */
		public function setShared($service_key, $service_definition){

		}



	}

}

