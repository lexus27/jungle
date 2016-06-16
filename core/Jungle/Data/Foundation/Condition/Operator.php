<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.06.2016
 * Time: 2:28
 */
namespace Jungle\Data\Foundation\Condition {

	use Jungle\Util\Value\String;

	/**
	 * Class Operator
	 * @package Jungle\CodeForm\LogicConstruction
	 */
	class Operator extends \Jungle\CodeForm\LogicConstruction\Operator{

		/**
		 *
		 */
		protected static function _initializeDefaultOperators(){
			self::$_operators = [
				(new Operator())->setName(false)->setHandler(function($a,$b=null){return !$a;}),
				(new Operator())->setName(true)->setHandler(function($a,$b=null){return (bool)$a;}),
				(new Operator())->setName('=')->setHandler(function($a,$b){return $a==$b;}),
				(new Operator())->setName('!=')->setHandler(function($a,$b){return $a!==$b;}),
				(new Operator())->setName('>')->setHandler(function($a,$b){return $a>$b;}),
				(new Operator())->setName('>=')->setHandler(function($a,$b){return $a>=$b;}),
				(new Operator())->setName('<')->setHandler(function($a,$b){return $a<$b;}),
				(new Operator())->setName('<=')->setHandler(function($a,$b){return $a<=$b;}),

				(new Operator())->setName('+')->setHandler(function($a,$b){return $a+$b;}),
				(new Operator())->setName('-')->setHandler(function($a,$b){return $a-$b;}),
				(new Operator())->setName('*')->setHandler(function($a,$b){return $a*$b;}),
				(new Operator())->setName('/')->setHandler(function($a,$b){return $a/$b;}),
				(new Operator())->setName('^')->setHandler(function($a,$b){return $a^$b;}),
				(new Operator())->setName('%')->setHandler(function($a,$b){return $a%$b;}),

				(new Operator())->setName('in')->setHandler(function($a,$b){
					if(is_array($b)){
						return in_array($a,$b);
					}else{
						return $a == $b;
					}
				}),

				(new Operator())->setName('not in')->setHandler(function($a,$b){
					if(is_array($b)){
						return !in_array($a,$b);
					}else{
						return $a != $b;
					}
				}),

				(new Operator())->setName('like')->setHandler(function($a,$b){
					return fnmatch($b,$a,FNM_CASEFOLD);
				}),

				(new Operator())->setName('start-with')->setHandler(function($a,$b){
					return String::startWith($b,$a,true);
				}),

				(new Operator())->setName('end-with')->setHandler(function($a,$b){
					return String::endWith($b,$a,true);
				})


			];
		}

	}
}

