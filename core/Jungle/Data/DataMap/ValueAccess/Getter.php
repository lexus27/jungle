<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 19:25
 */
namespace Jungle\Data\DataMap\ValueAccess {

	/**
	 * Class Getter
	 * @package Jungle\Data\DataMap\ValueAccess
	 */
	class Getter{

		/**
		 * @param $data
		 * @param $key
		 * @return mixed
		 */
		public function __invoke($data, $key){
			if(is_object($data)){
				return $this->getFromObject($data,$key);
			}elseif(is_array($data)){
				return $this->getFromArray($data,$key);
			}else{
				return null;
			}
		}

		/**
		 * @param $object
		 * @param $key
		 * @return mixed
		 */
		protected function getFromObject($object, $key){
			if(isset($object->{$key})){
				return $object->{$key};
			}elseif($object instanceof \ArrayAccess && isset($object[$key])){
				return $object[$key];
			}elseif(method_exists($object,($methodName = 'get'.$key))){
				return call_user_func([$object,$methodName]);
			}
			return null;
		}

		/**
		 * @param $array
		 * @param $key
		 * @return mixed
		 */
		protected function getFromArray($array, $key){
			if(isset($array[$key])){
				return $array[$key];
			}
			return null;
		}

	}

}

