<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 04.11.2016
 * Time: 17:28
 */
namespace Jungle\Application\Criteria {

	/**
	 * Class ParameterScope
	 * @package Jungle\Application\Criteria
	 */
	class ParameterScope{

		/** @var  ParameterScope */
		protected $ancestor;

		/** @var array  */
		protected $params = [];

		/** @var array  */
		protected $bounded = [];

		/**
		 * ParameterScope constructor.
		 * @param array $params
		 */
		public function __construct(array $params = []){
			$this->params = $params;
		}

		public function __set($name, $value){
			$this->bounded[$name] = $value;
		}

		public function __get($name){
			if(isset($this->bounded[$name])){
				return $this->bounded[$name];
			}elseif(isset($this->params[$name])){
				return $this->params[$name];
			}elseif($this->ancestor){
				return $this->ancestor->__get($name);
			}else{
				return null;
			}
		}

		public function __unset($name){
			unset($this->bounded[$name]);
		}

		public function __isset($name){
			return isset($this->bounded[$name])
			       || isset($this->params[$name])
			       || $this->ancestor && $this->ancestor->__isset($name);
		}

		/**
		 * @return array
		 */
		public function getBoundedParams(){
			return $this->ancestor?array_replace($this->bounded,$this->ancestor->getBoundedParams()): $this->bounded;
		}

		/**
		 * @return array
		 */
		public function getParams(){
			return $this->ancestor?array_replace($this->params,$this->ancestor->getParams()) : $this->params;
		}

		/**
		 * @param array $bound
		 * @return ParameterScope
		 */
		public function extend(array $bound = []){
			$descendant = clone $this;
			$descendant->ancestor = $this;
			$descendant->bounded = $bound;
			$descendant->params = [];
			return $descendant;
		}


		/**
		 * @return array
		 */
		public function toArray(){
			return array_replace($this->getParams(), $this->getBoundedParams());
		}

	}
}

