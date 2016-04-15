<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 19:25
 */
namespace Jungle\Data\DataMap\ValueAccess {

	/**
	 * Class Setter
	 * @package Jungle\Data\DataMap\ValueAccess
	 */
	class Setter{

		protected $method_name      = false;

		protected $method_name_auto = true;

		/**
		 * @param null $method_name if null $method_name_auto to true, if false method_name_auto to false, if string
		 * use method_name
		 * @return $this
		 */
		public function setMethodName($method_name = null){
			if(is_string($method_name)){
				$this->method_name      = $method_name;
				$this->method_name_auto = false;
			}elseif($method_name === null){
				$this->method_name      = $method_name;
				$this->method_name_auto = true;
			}else{
				$this->method_name      = false;
				$this->method_name_auto = false;
			}
			return $this;
		}

		/**
		 * @param object|array $data
		 * @param $key
		 * @param $value
		 * @return object|array
		 */
		public function __invoke($data, $key, $value){
			if(is_object($data)){
				$this->setToObject($data,$key,$value);
			}elseif(is_array($data)){
				$this->setToArray($data,$key,$value);
			}
			return $data;
		}

		/**
		 * @param $object
		 * @param $key
		 * @param $value
		 * @return mixed
		 */
		protected function setToObject(& $object, $key, $value){
			if($this->method_name){

				if(method_exists($object,$this->method_name)){
					call_user_func([$object,$this->method_name],$value);
					return;
				}else{
					throw new \LogicException(
						'Method name "'.$this->method_name.'" no found in class "'.get_class($object) .'"'
					);
				}

			}

			if(isset($object->{$key})){
				$object->{$key} = $value;
				return;
			}elseif($this->method_name_auto){
				$method_name = is_string($this->method_name)?$this->method_name:
					($this->method_name_auto?('set' .$key):null);
				if(method_exists($object,$method_name)){
					call_user_func([$object,$method_name],$value);
					return;
				}
			}


			if($object instanceof \ArrayAccess && isset($object[$key])){
				$object[$key] = $value;
			}else{
				$object->{$key} = $value;
			}
		}

		/**
		 * @param $array
		 * @param $key
		 * @param $value
		 */
		protected function setToArray(& $array, $key, $value){
			$array[$key] = $value;
		}

	}

}

