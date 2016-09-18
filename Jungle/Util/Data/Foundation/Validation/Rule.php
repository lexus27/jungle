<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 15:06
 */
namespace Jungle\Util\Data\Foundation\Validation {

	/**
	 * Class Rule
	 * @package Jungle\Util\Data\Foundation\Schema\ValueType
	 */
	abstract class Rule implements ValueCheckerInterface{

		/** @var array */
		static $_not_parameters_properties = [ 'type', 'default_params'];

		/** @var  string */
		protected $type;

		/** @var array  */
		protected $default_params = [];

		/**
		 * @param $type
		 * @return $this
		 */
		public function setType($type){
			$this->type = $type;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getType(){
			return $this->type;
		}

		/**
		 * Rule constructor.
		 */
		public function __construct(){
			$default = [];
			foreach(get_object_vars($this) as $name){
				if(!in_array($name,static::$_not_parameters_properties, true)){
					$default[$name] = $this->{$name};
				}
			}
			$this->default_params = $default;
		}

		/**
		 * @return array
		 */
		public function getParams(){
			$params = [];
			foreach($this->default_params as $key => $val){
				$params[$key] = $this->{$key};
			}
			return $params;
		}

		/**
		 * @param $value
		 * @param array $parameters
		 * @return bool
		 */
		public function check($value,array $parameters = []){
			$parameters = $parameters?array_replace($this->default_params, $parameters):$this->default_params;
			foreach($parameters as $name => $value){
				$this->{$name} = $value;
			}
			return $this->_check($value);
		}

		/**
		 * @param $value
		 * @return bool
		 */
		abstract protected function _check($value);

	}
}

