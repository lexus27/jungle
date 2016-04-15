<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 19:24
 */
namespace Jungle\Data\DataMap {

	use Jungle\Data\DataMap\ValueAccess\Getter;
	use Jungle\Data\DataMap\ValueAccess\Setter;
	use Jungle\Util\Value\Callback;

	/**
	 * Class ValueAccess
	 * @package Jungle\Data\DataMap
	 */
	class ValueAccess{

		/** @var callable[]|Getter[] */
		protected static $setter_collection = [];

		/** @var callable[]|Setter[] */
		protected static $getter_collection = [];

		/**
		 * @return callable|Setter
		 */
		public static function getDefaultSetter(){
			return self::getSetter('default');
		}

		/**
		 * @param $key
		 * @param callable|Setter $setter
		 */
		public static function setSetter($key, callable $setter){
			self::$setter_collection[$key] = $setter;
		}

		/**
		 * @param $key
		 * @return callable|Setter
		 */
		public static function getSetter($key){
			if(!isset(self::$setter_collection[$key])){
				if($key === 'default'){
					self::$setter_collection[$key] = new Setter();
				}else{
					return null;
				}
			}
			return self::$setter_collection[$key];
		}



		/**
		 * @return callable|Getter
		 */
		public static function getDefaultGetter(){
			return self::getGetter('default');
		}

		/**
		 * @param $key
		 * @param callable|Getter $accessor
		 */
		public static function setGetter($key, callable $accessor){
			self::$getter_collection[$key] = $accessor;
		}

		/**
		 * @param $key
		 * @return callable|Getter
		 */
		public static function getGetter($key){
			if(!isset(self::$getter_collection[$key])){
				if($key === 'default'){
					self::$getter_collection[$key] = new Getter();
				}else{
					return null;
				}
			}
			return self::$getter_collection[$key];
		}

		/**
		 * @param callable|Getter|string|null $getter
		 * @return callable|Getter|null
		 */
		public static function checkoutGetter($getter = null){
			return Callback::checkoutCallableInstanceOrString('Getter',Getter::class,function($key){
				$getter = ValueAccess::getGetter($key);
				if(!$getter){
					throw new \LogicException('Not found getter by key "'.$key.'"');
				}
				return $getter;
			},$getter);
		}

		/**
		 * @param callable|Setter|string|null $setter
		 * @return callable|Setter|null
		 */
		public static function checkoutSetter($setter = null){
			return Callback::checkoutCallableInstanceOrString('Setter',Setter::class,function($key){
				$setter = ValueAccess::getSetter($key);
				if(!$setter){
					throw new \LogicException('Not found setter by key "'.$key.'"');
				}
				return $setter;
			},$setter);
		}

		/**
		 * @param $definition
		 * @param $subject
		 * @return mixed
		 */
		public static function resolveGetter($definition, $subject){

			if(is_array($definition)){
				if($definition['type'] === 'method' && method_exists($subject, $definition['method'])){
					return call_user_func_array([$subject, $definition['method']],$definition['arguments']);
				}


			}

		}

		/**
		 * @param $definition
		 * @param $subject
		 * @param $value
		 * @return mixed
		 */
		public static function resolveSetter($definition, $subject, $value){
			if(is_array($definition)){
				if($definition['type'] === 'method' && method_exists($subject, $definition['method'])){
					$definition['arguments'] = (array)$definition['arguments'];
					array_splice($definition['arguments'],$definition['value_offset'],0,[$value]);
					return call_user_func_array([$subject, $definition['method']],$definition['arguments']);
				}


			}
		}



	}

}

