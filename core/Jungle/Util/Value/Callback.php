<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 19:44
 */
namespace Jungle\Util\Value {

	/**
	 * Class Callback
	 * @package Jungle\Util\Value
	 */
	class Callback{


		/**
		 * @param $valueCategory
		 * @param $className
		 * @param callable|null $value
		 * @return callable|null
		 */
		public static function checkoutCallableInstance($valueCategory, $className, callable $value = null){
			if(is_callable($value) || is_null($value)){
				if(is_object($value) && !$value instanceof \Closure && !$value instanceof $className){
					throw new \LogicException(strtoupper($valueCategory) . ' is not valid '.strtolower($valueCategory).' object (not instanceof '.$className.' or \Closure)');
				}
				return $value;
			}else{
				throw new \LogicException($valueCategory.' invalid!');
			}
		}

		/**
		 * @param callable $callback
		 * @param ...$arguments
		 * @return mixed
		 */
		public static function call_user_func_ref(callable $callback, & ... $arguments){
			return call_user_func_array($callback,$arguments);
		}


		/**
		 * @param $valueCategory
		 * @param $className
		 * @param callable $onString
		 * @param null $value
		 * @return callable|null
		 */
		public static function checkoutCallableInstanceOrString($valueCategory, $className, callable $onString, $value = null){
			if(is_object($value) && !$value instanceof \Closure && !$value instanceof $className){
				throw new \LogicException(strtoupper($valueCategory) . ' is not valid '.strtolower($valueCategory).' object (not instanceof '.$className.' or \Closure)');
			}
			if(is_callable($value)){
				return $value;
			}elseif(is_string($value)){
				return call_user_func($onString,$value);
			}
			return $value;
		}


	}
}

