<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 08.06.2016
 * Time: 2:28
 */
namespace Jungle\Util\Data\Condition {

	use Jungle\Util\Value\String;

	/**
	 * Class Operator
	 * @package Jungle\ExoCode\LogicConstruction
	 */
	class Operator extends \Jungle\ExoCode\LogicConstruction\Operator{
		/**
		 * @param $name
		 * @return Operator|null
		 */
		public static function getOperator($name){
			if($name instanceof Operator){
				return $name;
			}
			static $base_initialized = false;
			if(!$base_initialized){
				static::_initializeDefaultOperators();
				$base_initialized = true;
			}
			$i = static::searchOperator($name);
			if($i!==false){
				return static::$_operators[$i];
			}
			return null;
		}

		/**
		 * @param $operator
		 * @return bool|int|string
		 */
		public static function searchOperator($operator){
			if($operator instanceof Operator){
				$operator = $operator->getName();
			}
			foreach(static::$_operators as $i => $o){
				if(strcasecmp($o->getName(),$operator)===0){
					return $i;
				}
			}
			return false;
		}

		/**
		 *
		 */
		protected static function _initializeDefaultOperators(){
			self::$_operators = [
				(new Operator())->setName(false)->setHandler(function($a,$b=null){return !$a;}),
				(new Operator())->setName(true)->setHandler(function($a,$b=null){return (bool)$a;}),
				(new Operator())->setName('=')->setHandler(function($a,$b){return $a==$b;}),
				(new Operator())->setName('==')->setHandler(function($a,$b){return $a===$b;}),
				(new Operator())->setName('!=')->setHandler(function($a,$b){return $a!=$b;}),
				(new Operator())->setName('!==')->setHandler(function($a,$b){return $a!==$b;}),
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
					$result = !!preg_match('@^'.strtr($b,[
						'%' => '.*',
						'_' => '.'
					]).'$@smi',$a);
					return $result;
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

