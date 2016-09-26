<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.02.2016
 * Time: 2:29
 */
namespace Jungle\User\AccessControl\Adapter\PolicyAdater {

	use Jungle\User\AccessControl\Adapter\PolicyAdapter;
	use Jungle\User\AccessControl\Policy;
	use Jungle\User\AccessControl\Policy\Rule;
	use Jungle\Util\Value\String;

	/**
	 * Class Memory
	 * @package Jungle\User\AccessControl\Adapter\PolicyAdater
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

		/**
		 * @var array|null
		 */
		protected $_building_scope;

		/**
		 * @param array $definition
		 */
		public function fromArray(array $definition){
			$this->_building_scope = [
				'rules' => [],
				'targets' => [],
			];
			if(isset($definition['rules'])){
				foreach($definition['rules'] as $rule){
					$this->addRule($this->_requireRule($rule));
				}
			}
			if(isset($definition['policies'])){
				foreach($definition['policies'] as $policy){
					$this->addPolicy($this->_requirePolicy($policy));
				}
			}
			$this->_building_scope = null;
		}



		/**
		 * @param array $definition
		 * @return Policy\Target
		 */
		protected function _requireTarget(array $definition){
			if(is_array($definition)){
				$name = null;
				$target = new Policy\Target();
				if(isset($definition['name'])){
					$name = $definition['name'];
				}
				if(isset($definition['any_of'])){
					$target->anyOf($definition['any_of']);
				}
				if(isset($definition['all_of'])){
					$target->allOf($definition['all_of']);
				}
				if($name && !isset($this->_building_scope['targets'][$name])){
					$this->_building_scope['targets'][$name] = $target;
				}
				return $target;
			}else{
				return $this->_building_scope['targets'][$definition];
			}
		}

		/**
		 * @param $definition
		 * @return Rule
		 */
		protected function _requireRule($definition){
			if(is_array($definition)){
				$name = null;
				$rule = new Rule();
				if(isset($definition['name'])){
					$name = $definition['name'];
					$rule->setName($definition['name']);
				}
				if(isset($definition['effect'])){
					$rule->setEffect($definition['effect']);
				}
				if(isset($definition['condition'])){
					$rule->setCondition($definition['condition']);
				}
				if($name && !isset($this->_building_scope['rules'][$name])){
					$this->_building_scope['rules'][$name] = $rule;
				}
				return $rule;
			}else{
				return $this->_building_scope['rules'][$definition];
			}
		}

		/**
		 * @param $def
		 * @return Policy\PolicyElement|Policy\PolicyGroup
		 * @throws \Exception
		 */
		protected function _requirePolicy($def){
			if(is_array($def)){
				if(isset($def['rules']) || !isset($def['policies'])){
					return $this->_requirePolicyElement($def);
				}else{
					return $this->_requirePolicyGroup($def);
				}
			}else{
				return $this->_requirePolicyElement($def);
			}
		}


		/**
		 * @param array $definition
		 * @return Policy\PolicyElement
		 * @throws \Exception
		 */
		protected function _requirePolicyElement($definition){

			if(is_array($definition)){
				$definition = array_replace([
					'name'      => null,
					'target'    => null,
					'effect'    => null,
					'rules'     => [],
					'combiner'  => null,

					'obligation'    => null,
					'advice'        => null,

					'requirements'  => null
				],$definition);

				$policy = new Policy\PolicyElement($definition['name']);
				if($definition['target']){
					$policy->setTarget($this->_requireTarget($definition['target']));
				}
				if($definition['rules']){
					foreach($definition['rules'] as $_rule){
						$policy->addRule($this->_requireRule($_rule));
					}
				}
				if(isset($definition['effect'])){
					$policy->setEffect($definition['effect']);
				}
				if(isset($definition['combiner'])){
					$policy->setCombiner($definition['combiner']);
				}

				if(isset($definition['obligation'])){
					$policy->setObligation($definition['obligation']);
				}
				if(isset($definition['advice'])){
					$policy->setAdvice($definition['advice']);
				}
				if(isset($definition['requirements'])){
					$policy->setRequirements($definition['requirements']);
				}
				return $policy;
			}

			throw new \Exception(__METHOD__.' - definition invalid');
		}

		/**
		 * @param array $definition
		 * @return Policy\PolicyGroup
		 * @throws \Exception
		 */
		protected function _requirePolicyGroup($definition){

			if(is_array($definition)){
				$definition = array_replace([
					'name'          => null,
					'target'        => null,

					'combiner'      => null,
					'policies'      => [],

					'effect'        => null,

					'obligation'    => null,
					'advice'        => null,

					'requirements' => null
				],$definition);

				$policy = new Policy\PolicyGroup($definition['name']);
				if($definition['target']){
					$policy->setTarget($this->_requireTarget($definition['target']));
				}
				if(isset($definition['combiner'])){
					$policy->setCombiner($definition['combiner']);
				}
				if($definition['policies']){
					foreach($definition['policies'] as $_policy){
						$policy->addPolicy($this->_requirePolicy($_policy));
					}
				}
				if(isset($definition['effect'])){
					$policy->setEffect($definition['effect']);
				}
				if(isset($definition['obligation'])){
					$policy->setObligation($definition['obligation']);
				}
				if(isset($definition['advice'])){
					$policy->setAdvice($definition['advice']);
				}

				if(isset($definition['requirements'])){
					$policy->setRequirements($definition['requirements']);
				}
				return $policy;
			}
			throw new \Exception(__METHOD__.' - definition invalid');
		}


	}
}

