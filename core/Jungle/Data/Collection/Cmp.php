<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 18:54
 */
namespace Jungle\Data\Collection {

	use Jungle\Util\Value\Callback;

	/**
	 * Class Cmp
	 * @package Jungle\Data\Collection
	 */
	class Cmp{

		/** @var callable[] */
		protected static $cmp_collection = [];

		/**
		 * @return callable|\Closure|null
		 */
		public static function getDefaultCmp(){
			return self::getCmpByAlias('default');
		}

		/**
		 * @param $alias
		 * @return null|callable
		 */
		public static function getCmpByAlias($alias){
			if(isset(self::$cmp_collection[$alias])){
				return self::$cmp_collection[$alias];
			}elseif($alias==='default'){
				self::$cmp_collection[$alias] = function($a,$b){
					if($a==$b){
						return 0;
					}
					return $a<$b?-1:1;
				};
				return self::$cmp_collection[$alias];
			}
			return null;
		}

		/**
		 * @param $alias
		 * @param callable $cmp
		 */
		public static function setCmpByAlias($alias, callable $cmp){
			self::$cmp_collection[$alias] = self::checkoutCmp($cmp);
		}

		/**
		 * @param $cmp
		 * @return callable|null
		 */
		public static function checkoutCmp($cmp = null){
			if($cmp === null){
				return self::getDefaultCmp();
			}
			return Callback::checkoutCallableInstanceOrString('Cmp',CmpInterface::class,function($string){
				return self::getCmpByAlias($string);
			},$cmp);
		}


	}
}

