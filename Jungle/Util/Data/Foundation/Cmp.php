<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:15
 */
namespace Jungle\Util\Data\Foundation {

	use Jungle\Util\Value\Callback;

	/**
	 * Class Cmp
	 * @package Jungle\Util\Data\Foundation
	 */
	class Cmp implements CmpInterface{

		/** @var callable[] */
		protected static $cmp_collection = [ ];

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
			}elseif($alias === 'default'){
				self::$cmp_collection[$alias] = function ($a, $b){
					if($a == $b){
						return 0;
					}
					return $a < $b ? -1 : 1;
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
			return Callback::checkoutCallableInstanceOrString(
				'Cmp',
				CmpInterface::class,
				function ($string){
					return self::getCmpByAlias($string);
				},
				$cmp
			);
		}


		/**
		 * @param $current_value
		 * @param $selection_each
		 * @return int
		 */
		public function __invoke($current_value, $selection_each){
			if($current_value == $selection_each){
				return 0;
			}else{
				return $current_value > $selection_each ? 1 : -1 ;
			}
		}
	}
}

