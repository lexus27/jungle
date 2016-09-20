<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 13:49
 */
namespace Jungle\Util\Data\Validation {

	/**
	 * Class ExpertizeAbstract
	 * @package Jungle\Util\Data\Validation
	 */
	abstract class ExpertizeAbstract implements ExpertizeInterface{

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
		 * @param array $default_params
		 */
		public function __construct(array $default_params = []){
			$default = [];
			foreach(get_object_vars($this) as $name => $value){
				if(!in_array($name,static::$_not_parameters_properties, true)){
					$default[$name] = $value;
				}
			}
			$this->default_params = array_replace($default, $default_params);
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
		 * @return mixed
		 */
		public function expertize($value,array $parameters = []){
			$parameters = $parameters?array_intersect_key(array_replace($this->default_params, $parameters),$this->default_params):$this->default_params;
			foreach($parameters as $name => $value){
				$this->{$name} = $value;
			}
			$result = $this->_expertize($value);
			if($result !== true){
				return $this->_prepareMessage($result);
			}
			return $result;
		}


		/**
		 * @param $result
		 * @return \Jungle\Util\Data\Validation\MessageInterface
		 */
		abstract protected function _prepareMessage($result);

		/**
		 * @param $value
		 * @return mixed
		 */
		abstract protected function _expertize($value);

	}
}

