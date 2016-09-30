<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 25.07.2016
 * Time: 4:06
 */
namespace Jungle\Util\Data\Schema\ValueType {
	
	use Jungle\Util\Data\Schema\ValueType;
	use Jungle\Util\Data\Schema\ValueTypeInterface;
	use Jungle\Util\Data\Validation\Rule\RegExp;
	use Jungle\Util\Data\Validation\Rule\Vartype;

	/**
	 * Class Manager
	 * @package Jungle\Util\Data\Schema\ValueType
	 */
	class ValueTypePool{

		/** @var  ValueType[] */
		protected $types = [];


		/**
		 * @param $alias
		 * @param array|ValueTypeInterface $definition
		 * @return Customizable|ValueTypeInterface
		 */
		public function type($alias, $definition){
			if($definition instanceof ValueTypeInterface){
				return $this->types[] = $definition;
			}elseif(is_array($definition)){

				$definition = array_replace([
					'rules'         => null,
					'vartype'       => 'mixed',
					'vartype_check' => false,
					'evaluate'      => null,
					'originate'     => null,
					'stabilize'     => null,

					'options'       => null

				],$definition);
				$definition['rules'] = (array)$definition['rules'];
				if($definition['vartype_check']){
					array_unshift($definition['rules'], new Vartype([
						'vartypes' => $definition['vartype']
					]));
				}

				return $this->types[] = new Customizable($alias, $definition['vartype'],
					$definition['rules'],
					$definition['evaluate'],
					$definition['originate'],
					$definition['stabilize'],
					$definition['options']
				);

			}
			return null;
		}

		/**
		 * @param $alias
		 * @param $vartype
		 * @return Customizable
		 */
		public function vartype($alias, $vartype){
			return $this->types[] = new \Jungle\Util\Data\Schema\ValueType\Vartype($alias, $vartype, [
				new Vartype([ 'vartypes' => $vartype ])
			]);
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
			$this->vartype(['float','double'],'double');

			$this->addType(new Serialized());
			$this->type(['bool','boolean'],[
				'vartype'       => 'boolean',
				'evaluate'      => ($bv = function($v){return boolval($v);}),
				'originate'     => function($v){return intval($v);},
				'stabilize'     => $bv
			]);

			$this->type(['date'],[
				'vartype'       => 'integer',
				'evaluate'      => ($bv = function($v){return strtotime($v);}),
				'originate'     => function($value){
					if(is_int($value)) return date('Y-m-d H:i:s', $value);
					else return $value;
				},
				'stabilize'     => function($value){
					if(is_string($value)) return strtotime($value);
					return $value;
				}
			]);

			$this->type(['email'],[
				'rules' => [
					new RegExp([ 'pattern' => '@^[[:alpha:]][\w\-\_]*@[[:alpha:]]\w*\.[[:alpha:]]\w*$@uS' ])
				]
			]);
			$this->type(['words'],[
				'rules' => [
					new RegExp([ 'pattern' => '@^[[:alpha:]]*[\w\-\_\d\s]*$@uS' ])
				]
			]);
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

