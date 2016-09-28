<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.02.2016
 * Time: 16:44
 */
namespace Jungle\User\AccessControl\Context {

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

		const CONDITION_ALL_OF = 'all_of';

		const CONDITION_ANY_OF = 'any_of';


		/** @var  callable|null */
		protected $accessor;

		/** @var  array */
		protected $predicates = [];


		/**
		 * @param callable|null $accessor
		 * @return $this
		 */
		public function setAccessor(callable $accessor = null){
			$this->accessor = $accessor;
			return $this;
		}

		/**
		 * @return callable|null
		 */
		public function getAccessor(){
			return $this->accessor;
		}

		/**
		 * @return array
		 */
		public function getPredicates(){
			return $this->predicates;
		}

		/**
		 * @return array
		 */
		public function getPredicatedFields(){
			return array_keys($this->predicates);
		}

		/**
		 * @return array
		 */
		public function getAnyOfPredicates(){
			$a = [];
			foreach($this->predicates as $field => $container){
				$a[$field] = $container['any_of'];
			}
			return $a;
		}

		/**
		 * @return array
		 */
		public function getAllOfPredicates(){
			$a= [];
			foreach($this->predicates as $field => $container){
				$a[$field] = $container['all_of'];
			}
			return $a;
		}



		/**
		 * @param $field
		 * @param $operator
		 * @param $value
		 */
		public function addAllOfPredicate($field, $operator, $value){
			if(!isset($this->predicates[$field])){
				$this->predicates[$field] = [
					'all_of' => [],
					'any_of' => []
				];
			}
			$this->predicates[$field]['all_of'][] = [$operator,$value];
		}

		/**
		 * @param $field
		 * @param $operator
		 * @param $value
		 */
		public function addAnyOfPredicate($field, $operator, $value){
			if(!isset($this->predicates[$field])){
				$this->predicates[$field] = [
					'all_of' => [],
					'any_of' => []
				];
			}
			$this->predicates[$field]['any_of'][] = [$operator,$value];
		}



	}
}

