<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 14.02.2016
 * Time: 19:22
 */
namespace Jungle\User\AccessControl\Matchable {

	use Jungle\User\AccessControl\Context;
	use Jungle\User\AccessControl\ContextInterface;

	/**
	 * Class Target
	 * @package Jungle\User\AccessControl
	 *
	 * Цель политики, целью является определенно-подходящая комбинация атрибутов контекста
	 * Если контекст не удовлетворяет цели политики то политика является не применимой для данного контекста
	 *
	 * @Conditions -
	 *          (                                   AnyOf (OR)
	 *              Object.type = Document  ||
	 *              Object.type = Resource  ||
	 *              true                    ||
	 *              true
	 *          ) && (                              AllOf (AND)
	 *              Object.type = Document  &&
	 *              Object.type = Resource  &&
	 *              true                    &&
	 *              true
	 *          )
	 */
	class Target{

		/**
		 * @Conditions
		 * @see $all_of_conditions
		 * @var  array  - Цель применима если любое из условий истино
		 */
		protected $any_of_conditions = [];

		/**
		 * @Conditions
		 * @see $any_of_conditions
		 * @var array - Цель применима если каждое условие в массиве - истино
		 */
		protected $all_of_conditions = [];

		/**
		 * @param $conditions
		 * @param bool $reset
		 * @return $this
		 */
		public function anyOf($conditions, $reset = true){
			if($reset){
				$this->any_of_conditions = [];
			}
			if(!is_array($conditions)){
				$conditions = [$conditions];
			}
			foreach($conditions as $c){
				if($c){
					if(is_string($c)){
						$this->any_of_conditions[] = $c;
					}elseif(is_array($c)){
						$this->anyOf($conditions,false);
					}
				}
			}
			return $this;
		}

		/**
		 * @param $conditions
		 * @param bool $reset
		 * @return $this
		 */
		public function allOf($conditions, $reset = true){
			if($reset){
				$this->all_of_conditions = [];
			}
			if(!is_array($conditions)){
				$conditions = [$conditions];
			}
			foreach($conditions as $c){
				if(is_string($c) && $c){
					$this->all_of_conditions[] = $c;
				}elseif(is_array($c)){
					$this->allOf($conditions,false);
				}
			}
			return $this;
		}


		/**
		 * @param $condition
		 * @return $this
		 */
		public function addAnyOfCondition($condition){
			if(!in_array($condition,$this->any_of_conditions,true)){
				$this->any_of_conditions[] = $condition;
			}
			return $this;
		}

		/**
		 * @param $condition
		 * @return $this
		 */
		public function removeAnyOfCondition($condition){
			if(($i = array_search($condition,$this->any_of_conditions,true))!==false){
				array_splice($this->any_of_conditions,$i,1);
			}
			return $this;
		}



		/**
		 * @param $condition
		 * @return $this
		 */
		public function addAllOfCondition($condition){
			if(!in_array($condition,$this->all_of_conditions,true)){
				$this->all_of_conditions[] = $condition;
			}
			return $this;
		}

		/**
		 * @param $condition
		 * @return $this
		 */
		public function removeAllOfCondition($condition){
			if(($i = array_search($condition,$this->all_of_conditions,true))!==false){
				array_splice($this->all_of_conditions,$i,1);
			}
			return $this;
		}


		/**
		 * @param ContextInterface $context
		 * @param Result $current
		 * @return bool
		 * @throws Resolver\ConditionResolver\Exception
		 */
		public function __invoke(ContextInterface $context, Result $current){
			if(!$this->all_of_conditions && !$this->any_of_conditions){
				return true;
			}
			$resolver = $context->getManager()->getConditionResolver();
			if($inspector = $resolver->getInspector()){
				$inspector->setMode('all_of');
			}
			$result = true;
			foreach($this->all_of_conditions as $condition){
				if(!$resolver->resolve($context, $current, $condition)){
					$result = false;
				}
			}
			if($result === true && $this->any_of_conditions){
				if($inspector){
					$inspector->setMode('any_of');
				}
				foreach($this->any_of_conditions as $condition){
					if($resolver->resolve($context, $current, $condition)){
						return true;
					}
				}
				return false;
			}
			return $result;
		}

	}
}

