<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 27.10.2015
 * Time: 23:46
 */
namespace Jungle {

	use Jungle\TypeHint\Rule\Builder\MultiBuilder;
	use Jungle\TypeHint\TypeChecker;
	use Jungle\Util\Value\String;

	/**
	 * Class TypeHint
	 * @package Jungle
     *  TODO \Jungle\TypeHint::check{fast_type_name}($value, $ОшибкаВозниклаПри)
	 *  TODO \Jungle\TypeHint::check($type:array:string,$value, $ОшибкаВозниклаПри)
	 *  TODO @throws \Jungle\TypeHintException[$ОшибкаВозниклаПри, ]
	 *  TODO Реализовать концепцию строгой/мягенькой типизации
	 */
	class TypeHint{

		/** @var string */
		protected static $checker_prefix = 'check';
		/** @var string  */
		protected static $is_prefix = 'is';

		/** @var TypeHint */
		protected static $instance = null;

		/** @var TypeChecker[]  */
		protected $_type_checkers = [];

		/** @var MultiBuilder */
		protected $builder;

		/**
		 * @param TypeChecker $checker
		 * @return $this
		 */
		public function addType(TypeChecker $checker){
			if($this->searchType($checker)===false){
				if($this->getType($checker)){
					throw new \LogicException('TypeHint Configuration Error: type-checker "'.$checker->getName().'" already exists in TypeHint');
				}
				$this->_type_checkers[] = $checker;
				$this->_sortTypes();
			}
			return $this;
		}

		/**
		 * @param TypeChecker $checker
		 * @return mixed
		 */
		public function searchType(TypeChecker $checker){
			return array_search($checker,$this->_type_checkers,true);
		}

		/**
		 * @param TypeChecker $checker
		 * @return $this
		 */
		public function removeType(TypeChecker $checker){
			if( ($i = $this->searchType($checker))!==false){
				array_splice($this->_type_checkers,$i,1);
				$this->_sortTypes();
			}
			return $this;
		}

		/**
		 * @param $name
		 * @return TypeChecker|null
		 * @paradigm {getObjectByName} in object equal collection = equal_instance + propertyEqual
		 */
		public function getType($name){
			if($name instanceof TypeChecker){
				$name = $name->getName();
			}
			if(!$name) return null;
			foreach($this->_type_checkers as $checker){
				if(strcasecmp($checker->getName(),$name)===0){
					return $checker;
				}
			}
			return null;
		}

		/**
		 * @return TypeHint
		 */
		public static function getInstance(){
			if(!self::$instance instanceof self){
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * @param $methodDefinition
		 * @param $arguments
		 *
		 * @return mixed
		 */
		public function __call($methodDefinition,$arguments){
			if(strcasecmp($methodDefinition,'check')===0){
				return call_user_func_array([$this,'check'],$arguments);
			}
			if(String::startWith(self::$checker_prefix,$methodDefinition,true)){
				$type = strtolower(String::trimWordsLeft($methodDefinition,self::$checker_prefix,true));
				array_unshift($arguments,$type);
				return call_user_func_array([$this,'check'],$arguments);
			}elseif(String::startWith(self::$is_prefix,$methodDefinition,true)){
				$type = strtolower(String::trimWordsLeft($methodDefinition,self::$is_prefix,true));
				array_unshift($arguments,$type);
				return call_user_func_array([$this,'is'],$arguments);
			}else{
				throw new \LogicException('__call magic: ');
			}
		}


		/**
		 * @param $method
		 * @param $arguments
		 *
		 * @return bool|mixed
		 * @throws \LogicException
		 * @_call check{Type}
		 */
		public static function __callStatic($method,$arguments){
			return self::getInstance()->__call($method,$arguments);
		}

		/**
		 * @param MultiBuilder $builder
		 */
		public function setBuilder(MultiBuilder $builder){
			$this->builder = $builder;
		}

		/**
		 * @param null|string $type
		 * @return MultiBuilder
		 */
		public function getBuilder($type = null){
			if(!$this->builder){

				$this->builder = (new MultiBuilder())
					->addBuilder((new TypeHint\Rule\Builder\ArrayBuilder()))
					->addBuilder((new TypeHint\Rule\Builder\InlineBuilder()));

			}
			return !is_string($type)||!$type? $this->builder:$this->builder->getBuilder($type);
		}


		/**
		 * @param string $type
		 * @param mixed $value
		 * @param null $required_parameter
		 * @param bool $mutable
		 * @param string $errorClass
		 * @return bool
		 */
		public function check($type,$value,$required_parameter = null,$mutable = false,$errorClass='\Jungle\TypeHintException'){
			$builder = $this->getBuilder();
			$complex = $builder->buildComplex($type,$this);
			if($complex){
				$complex->setValue($value);
				if($complex->check($this,$mutable,$required_parameter)===false){
					if($mutable){
						return false;
					}else{
						throw new $errorClass($complex->getError());
					}
				}
			}else{

			}
			return true;
		}

		/**
		 * @param string $type
		 * @param array $values
		 * @param null $required_parameter
		 * @param bool $mutable
		 * @param string $errorClass
		 * @return bool
		 */
		public function checkValues($type,array $values,$required_parameter = null,$mutable = false,$errorClass='\Jungle\TypeHintException'){
			if(!$type){
				return true;
			}
			$builder = $this->getBuilder();
			$complex = $builder->buildComplex($type,$this);
			if($complex){
				foreach($values as $key => $value){
					$complex->setValue($value);
					if($complex->check($this,$mutable,is_string($key) && $key?$key:$required_parameter)===false){
						if($mutable){
							return false;
						}else{
							throw new $errorClass($complex->getError());
						}
					}
				}
			}else{

			}
			return true;
		}

		/**
		 * @param $type
		 * @param $value
		 * @return bool
		 */
		public function is($type, $value){
			return $this->check($type, $value,null,true);
		}

		/**
		 * Сортирует типы по их приоритету ->getPriority()
		 */
		protected function _sortTypes(){
			static $sorter = null;
			if(!$sorter){
				$sorter = function(TypeChecker $ch1,TypeChecker $ch2){
					$ch1 = $ch1->getPriority();
					$ch2 = $ch2->getPriority();
					if($ch1 == $ch2 ) return 0;
					return ($ch1 < $ch2) ? -1 : 1;
				};
			}
			usort($this->_type_checkers,$sorter);
		}

		/**
		 * @return TypeHint\TypeChecker[]
		 */
		public function getCheckers(){
			if(!$this->_type_checkers){
				$this->_type_checkers = [
					(new TypeChecker\Arrays()),
					(new TypeChecker\String()),
					(new TypeChecker\Boolean()),
					(new TypeChecker\Nullable()),
					(new TypeChecker\Object()),
					(new TypeChecker\Object\Derivative()),
					(new TypeChecker\Numeric()),
					(new TypeChecker\Numeric\Integer()),
					(new TypeChecker\Numeric\Float()),
					(new TypeChecker\Any()),
					(new TypeChecker\Arrays()),
					(new TypeChecker\Arrays\InnerType()),
				];
			}
			return $this->_type_checkers;
		}

		/**
		 * @param $value
		 * @param null $sanitizer
		 * @return mixed
		 */
		public function sanitize($value, $sanitizer = null){

		}



	}
}

