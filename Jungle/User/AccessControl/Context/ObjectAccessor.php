<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.02.2016
 * Time: 16:44
 */
namespace Jungle\User\AccessControl\Context {

	use Jungle\Util\Value\Massive;

	/**
	 * TODO Recording Voice File clipped comment! Запись и привязка голосовых комментариев к файлу, субьекту работы, конкретному элементу)
	 *
	 * Я не помню точно зачем создал этот класс, но помню что предполагается доступ к объектам
	 * с помощью этого класса при помощи WhereClause Predicate,
	 * Класс реализует доступ к сервисам которые получат объект|объекты по определенному предикату
	 * Типо Делегирование куда-то WhereClause выборки
	 * Class ObjectAccessor
	 * @package Jungle\User\AccessControl\Context
	 */
	class ObjectAccessor extends Substitute{



		/** @var  callable|null */
		protected $accessor;

		/** @var  array */
		protected $target_conditions = [];

		/** @var  array */
		protected $predicated_conditions = [];

		/** @var  array */
		protected $phantom;

		/** @var bool|null  */
		protected $collect_predicates_effect = null;


		/**
		 * ObjectAccessor constructor.
		 * @param array $properties
		 */
		public function __construct(array $properties = null){
			if($properties){
				if(isset($properties['class'])){
					$this->class = $properties['class'];
				}
				if(isset($properties['phantom'])){
					$this->phantom = $properties['phantom'];
				}
				if(isset($properties['conditions'])){
					$this->target_conditions = $properties['condition'];
				}
				if(isset($properties['predicate_effect'])){
					$this->collect_predicates_effect = $properties['predicate_effect'];
				}
			}
		}

		/**
		 * @param array $object
		 * @return $this
		 */
		public function setPhantom(array $object){
			$this->phantom = $object;
			return $this;
		}

		/**
		 * @return array
		 */
		public function getPhantom(){
			return $this->phantom;
		}



		/**
		 * @param string $name
		 * @return null
		 */
		function __get($name){
			return isset($this->phantom[$name])?$this->phantom[$name]:null;
		}

		/**
		 * @param string $name
		 * @param mixed $value
		 */
		function __set($name, $value){
			$this->phantom[$name] = $value;
		}

		/**
		 * @param string $name
		 * @return bool
		 */
		function __isset($name){
			return isset($this->phantom[$name]);
		}

		/**
		 * @param string $name
		 */
		function __unset($name){
			unset($this->phantom[$name]);
		}



		/**
		 * @param array $conditions
		 * @return $this
		 */
		public function setTargetConditions(array $conditions){
			$this->target_conditions = $conditions;
			return $this;
		}

		/**
		 * @return array
		 */
		public function getTargetConditions(){
			return $this->target_conditions;
		}



		/**
		 * @param array $conditions
		 */
		public function setPredicatedConditions(array $conditions){
			$this->predicated_conditions = $conditions;
		}

		/**
		 * @return array
		 */
		public function getPredicatedConditions(){
			return $this->predicated_conditions;
		}



		/**
		 * @return array
		 */
		public function getSelectConditions(){
			$a = [];
			if($this->predicated_conditions){
				$a[] = $this->predicated_conditions;
			}
			if($this->target_conditions){
				$a[] = $this->target_conditions;
			}
			if($this->phantom){
				$b = [];
				foreach($this->phantom as $k => $v){
					$b[] = [$k,'=',$v];
				}
				$a[] = $b;
			}
			if(count($a)===1){
				return $a[0];
			}else{
				return Massive::insertSeparates($a, 'AND');
			}
		}

		/**
		 * @param $effect
		 * @return $this
		 */
		public function setPredicateEffect($effect){
			$this->collect_predicates_effect = $effect;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function hasPredicateEffect(){
			return $this->collect_predicates_effect!==null;
		}

		/**
		 * @return bool|null
		 */
		public function getPredicateEffect(){
			return $this->collect_predicates_effect;
		}



	}
}

