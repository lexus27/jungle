<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 25.07.2016
 * Time: 4:06
 */
namespace Jungle\Util\Data\Foundation\Schema\ValueType {
	
	use Jungle\Util\Data\Foundation\Schema\ValueType;
	use Jungle\Util\Data\Foundation\Schema\ValueTypeInterface;

	/**
	 * Class Manager
	 * @package Jungle\Util\Data\Foundation\Schema\ValueType
	 */
	class ValueTypePool{

		/** @var  ValueType[] */
		protected $types = [];

		/**
		 * @param $alias
		 * @param $vartype
		 * @param callable $verifyFunction
		 * @param callable $evaluateFunction
		 * @param callable $originateFunction
		 * @param callable $stabilizeFunction
		 * @param array $customizableOptions
		 */
		public function add($alias, $vartype,
			callable $verifyFunction,
			callable $evaluateFunction,
			callable $originateFunction,
			callable $stabilizeFunction = null,
			array $customizableOptions = []
		){
			$this->types[] = new Customizable($alias, $vartype,
				$verifyFunction,
				$evaluateFunction,
				$originateFunction,
				$stabilizeFunction,
				$customizableOptions
			);
		}

		/**
		 * @param $alias
		 * @param $vartype
		 * @return $this
		 */
		public function vartype($alias, $vartype){
			$this->types[] = new VartypeChecker($alias, $vartype);
			return $this;
		}

		/**
		 * @param $alias
		 * @param $pattern
		 * @param null $vartype
		 * @return $this
		 */
		public function pattern($alias, $pattern, $vartype = null){
			$this->types[] = new Pattern($alias, $pattern, $vartype);
			return $this;
		}

		/**
		 * @param ValueType $type
		 * @return $this
		 */
		public function addType(ValueType $type){
			$this->types[] = $type;
			return $this;
		}

		/**
		 * @param $alias
		 * @return ValueType|null
		 */
		public function getType($alias){
			if($alias instanceof ValueTypeInterface){
				return $alias;
			}
			foreach($this->types as $type){
				if($type->hasAlias($alias)){
					return $type;
				}
			}
			return null;
		}


		/**
		 * ValueTypePool constructor.
		 */
		public function __construct(){
			$this->vartype(['string'],'string');
			$this->vartype(['int','integer','long'],'integer');
			$this->vartype(['int','integer','long'],'integer');
			$this->addType(new Serialized());
			$this->add(['bool','boolean'],'integer',
				function($v){return is_bool($v);},
				function($v){boolval($v);},
				function($v){return intval($v);},
				function($v){boolval($v);}
			);
			$this->add(['date'],'integer',
				function($v){return is_int($v);},
				function($v){return strtotime($v);},
				function($value){
					if(is_int($value)) return date('Y-m-d H:i:s', $value);
					else return $value;

				},function($value){
					if(is_string($value)) return strtotime($value);
					return $value;
				});
			$this->vartype(['float','double'],'double');
			$this->pattern(['email'],'@[[:alpha:]][\w\-\_]*@[[:alpha:]]\w*\.[[:alpha:]]\w*@S');
		}

		/** @var  ValueTypePool */
		protected static $default;

		/**
		 * @param ValueTypePool $manager
		 */
		public static function setDefault(ValueTypePool $manager){
			static::$default = $manager;
		}

		/**
		 * @return ValueTypePool
		 */
		public static function getDefault(){
			if(!static::$default){
				static::$default = new static();
			}
			return static::$default;
		}

	}
}

