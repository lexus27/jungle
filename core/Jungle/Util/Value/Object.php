<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 15:25
 */
namespace Jungle\Util\Value{

	/**
	 * Class Object
	 * @package Jungle\Util\ValueHelp
	 */
	class Object{

		protected function __construct(){}

		/**
		 * @param $object
		 * @param $method
		 * @param ...$arguments
		 * @return mixed
		 */
		public static function callMethod($object, $method, ...$arguments){
			return call_user_func_array([$object,$method],$arguments);
		}

		/**
		 * @param $object
		 * @param $method
		 * @param array $arguments
		 * @return mixed
		 */
		public static function callMethodArray($object, $method,array $arguments = []){
			return call_user_func_array([$object,$method],$arguments);
		}

		/**
		 * @param $object
		 * @param $property
		 * @param $prefix
		 * @return bool
		 */
		public static function methodExistsPrefix($object, $property, $prefix){
			return method_exists($object, $prefix . $property);
		}

		/**
		 * @param $object
		 * @param $property
		 * @return mixed
		 */
		public static function getProperty($object,$property){
			return $object->{$property};
		}

		/**
		 * @param $object
		 * @param $property
		 * @param $value
		 */
		public static function setProperty($object,$property, $value){
			$object->{$property} = $value;
		}


	}
}

