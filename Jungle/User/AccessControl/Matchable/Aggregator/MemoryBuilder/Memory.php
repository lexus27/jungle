<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.02.2016
 * Time: 2:29
 */
namespace Jungle\User\AccessControl\Matchable\Aggregator\MemoryBuilder {

	use Jungle\User\AccessControl\Matchable\Aggregator;
	use Jungle\User\AccessControl\Matchable\Aggregator\Policy;
	use Jungle\User\AccessControl\Matchable\Aggregator\PolicyGroup;
	use Jungle\User\AccessControl\Matchable\Matchable;
	use Jungle\User\AccessControl\Matchable\Rule;
	use Jungle\User\AccessControl\Matchable\Target;

	/**
	 * Class Memory
	 * @package Jungle\User\AccessControl\Adapter\PolicyAdater
	 */
	class Memory extends Aggregator\PolicyGroup{

		/**
		 * @var array|null
		 */
		protected $_building_scope;

		/**
		 * @param array $definition
		 */
		public function build(array $definition){
			$this->_building_scope = [
				'rules'     => [],
				'targets'   => [],
			];
			if(isset($definition['rules'])){
				foreach($definition['rules'] as $rule){
					$this->_requireRule($rule);
				}
			}
			if(isset($definition['policies'])){
				foreach($definition['policies'] as $policy){
					$this->addChild($this->_requirePolicy($policy));
				}
			}
			$this->_building_scope = null;
		}



		/**
		 * @param array $definition
		 * @return Target
		 */
		protected function _requireTarget(array $definition){
			if(is_array($definition)){
				$name = null;
				$target = new Target();
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
				if(isset($definition['target'])){
					$rule->setTarget($this->_requireTarget($definition['target']));
				}
				if(isset($definition['effect'])){
					$rule->setEffect(Matchable::friendlyEffect($definition['effect']));
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
		 * @return Policy|PolicyGroup
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
		 * @return Policy
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

				$policy = new Policy($definition['name']);
				if($definition['target']){
					$policy->setTarget($this->_requireTarget($definition['target']));
				}
				if($definition['rules']){
					foreach($definition['rules'] as $_rule){
						$policy->addChild($this->_requireRule($_rule));
					}
				}
				if(isset($definition['effect'])){
					$policy->setEffect(Matchable::friendlyEffect($definition['effect']));
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
					$policy->setRequirement($definition['requirements']);
				}
				return $policy;
			}

			throw new \Exception(__METHOD__.' - definition invalid');
		}

		/**
		 * @param array $definition
		 * @return PolicyGroup
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

				$policy = new PolicyGroup($definition['name']);
				if($definition['target']){
					$policy->setTarget($this->_requireTarget($definition['target']));
				}
				if(isset($definition['combiner'])){
					$policy->setCombiner($definition['combiner']);
				}
				if($definition['policies']){
					foreach($definition['policies'] as $_policy){
						$policy->addChild($this->_requirePolicy($_policy));
					}
				}
				if(isset($definition['effect'])){
					$policy->setEffect(Matchable::friendlyEffect($definition['effect']));
				}
				if(isset($definition['obligation'])){
					$policy->setObligation($definition['obligation']);
				}
				if(isset($definition['advice'])){
					$policy->setAdvice($definition['advice']);
				}

				if(isset($definition['requirements'])){
					$policy->setRequirement($definition['requirements']);
				}
				return $policy;
			}
			throw new \Exception(__METHOD__.' - definition invalid');
		}

	}
}

