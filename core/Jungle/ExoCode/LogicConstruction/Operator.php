<?php
/**
 * Created by PhpStorm.
 * Project: jungle
 * Date: 09.04.2015
 * Time: 16:41
 */

namespace Jungle\ExoCode\LogicConstruction;
use Jungle\Util\INamed;
use Jungle\Util\Value\Massive;
use Jungle\Util\Value\String;

/**
 * Class Operator
 * @package Jungle\ExoCode\LogicConstruction
 */
class Operator implements INamed{

	/**
	 * @var Operator[]
	 */
	protected static $_operators = [];

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var callable
	 */
	protected $handler;


	/**
	 * Получить имя объекта
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * Выставить имя объекту
	 * @param $name
	 * @return $this
	 */
	public function setName($name){
		$this->name = $name;
		return $this;
	}

	/**
	 * @param callable $handler
	 * @return $this
	 */
	public function setHandler(callable $handler){
		$this->handler = $handler;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getHandler(){
		return $this->handler;
	}

	/**
	 * @param $primary
	 * @param $secondary
	 * @return mixed
	 */
	public function check($primary, $secondary){
		return call_user_func($this->getHandler(), $primary, $secondary);
	}


	/**
	 * @param $name
	 * @return Operator|null
	 */
	public static function getOperator($name){
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

	public static function getAllowedOperators(){
		static $base_initialized = false;
		if(!$base_initialized){
			static::_initializeDefaultOperators();
			$base_initialized = true;
		}
		return static::$_operators;
	}

	protected static function _initializeDefaultOperators(){
		static::$_operators = [
			(new Operator())->setName(false)->setHandler(function($a,$b=null){return !$a;}),
			(new Operator())->setName(true)->setHandler(function($a,$b=null){return (bool)$a;}),
			(new Operator())->setName('=')->setHandler(function($a,$b){return $a==$b;}),
			(new Operator())->setName('===')->setHandler(function($a,$b){return $a===$b;}),
			(new Operator())->setName('==')->setHandler(function($a,$b){return $a==$b;}),
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

			(new Operator())->setName('have')->setHandler(function($a,$b){
				if(is_array($a)) return Massive::stringExists($a,$b,true);
				elseif(is_string($a))return mb_stripos($a,$b)!==false;
				else return false;
			}),

			(new Operator())->setName('in')->setHandler(function($a,$b){
				if(is_array($b)) return Massive::stringExists($b,$a,true);
				elseif(is_string($b))return mb_stripos($b,$a)!==false;
				else return false;
			}),

			(new Operator())->setName('match')->setHandler(function($a,$b){
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

	/**
	 * @param Operator $operator
	 */
	public static function addOperator(Operator $operator){
		if(self::searchOperator($operator)===false){
			static::$_operators[] = $operator;
		}
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
	 * @param Operator $operator
	 */
	public static function removeOperator(Operator $operator){
		if(($i = static::searchOperator($operator))!==false){
			array_splice(static::$_operators,$i,1);
		}
	}


}