<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.02.2016
 * Time: 2:29
 */
namespace Jungle\User\Access\ABAC\Adapter\PolicyAdater {

	use Jungle\User\Access\ABAC\Adapter\PolicyAdapter;
	use Jungle\User\Access\ABAC\Policy;
	use Jungle\User\Access\ABAC\Policy\Rule;
	use Jungle\Util\Value\String;

	/**
	 * Class Memory
	 * @package Jungle\User\Access\ABAC\Adapter\PolicyAdater
	 */
	class Memory extends PolicyAdapter{

		/**
		 * @var Policy[]
		 */
		protected $policies = [];

		/**
		 * @var Rule[]
		 */
		protected $rules = [];

		/**
		 * @param Rule $rule
		 * @return $this
		 */
		public function addRule(Rule $rule){
			if(!in_array($rule,$this->rules,true)){
				$this->rules[] = $rule;
			}
		}

		/**
		 * @param $name
		 * @return Rule
		 */
		public function getRule($name){
			$name = trim(String::camelCase($name,false,'_'));
			foreach($this->rules as $rule){
				if(trim(String::camelCase($rule->getName(),false,'_')) === $name){
					return $rule;
				}
			}
			return null;
		}

		/**
		 * @return Rule[]
		 */
		public function getRules(){
			return $this->rules;
		}

		/**
		 * @param Policy $policy
		 * @return $this
		 */
		public function addPolicy(Policy $policy){
			if(!in_array($policy,$this->policies,true)){
				$this->policies[] = $policy;
			}
			return $this;
		}

		/**
		 * @param $name
		 * @return Policy
		 */
		public function getPolicy($name){
			$name = trim(String::camelCase($name,false,'_'));
			foreach($this->policies as $policy){
				if(trim(String::camelCase($policy->getName(),false,'_')) === $name){
					return $policy;
				}
			}
			return null;
		}

		/**
		 * @return Policy[]
		 */
		public function getPolicies(){
			return $this->policies;
		}
	}
}

