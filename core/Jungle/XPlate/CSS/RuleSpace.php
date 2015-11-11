<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 17.05.2015
 * Time: 17:20
 */

namespace Jungle\XPlate\CSS;

/**
 * Class ARuleCollection
 * @package Jungle\XPlate\CSS
 */
abstract class RuleSpace {

	/**
	 * @var Rule[]
	 */
	protected $rules = [];

	/**
	 * @param Rule $rule
	 * @return $this
	 */
	public function addRule(Rule $rule){
		if($this->searchRule($rule) === false){
			$this->rules[] = $rule;
		}
		return $this;
	}

	/**
	 * @param Rule $rule
	 * @return mixed
	 */
	public function searchRule(Rule $rule){
		return array_search($rule,$this->rules,true);
	}

	/**
	 * @param Rule $rule
	 * @return $this
	 */
	public function removeRule(Rule $rule){
		if( ($i = $this->searchRule($rule)) !== false){
			array_splice($this->rules,$i,1);
		}
		return $this;
	}


	/**
	 * @param Definition $definition
	 * @param bool $single
	 * @return Rule[]|Rule|null
	 */
	public function findRulesByDefinition(Definition $definition,$single = false){
		if(!$single) $rules = [];
		foreach($this->rules as $rule){
			if($rule->getDefinition() === $definition){
				if(!$single)$rules[] = $rule;
				else return $rule;
			}
		}
		return isset($rules)?$rules:null;
	}

	/**
	 * @param Selector $selector
	 * @param bool $single
	 * @return Rule[]|Rule|null
	 */
	public function findRulesBySelector(Selector $selector, $single = false){
		if(!$single) $rules = [];
		foreach($this->rules as $rule){
			$group = $rule->getSelectorGroup();
			if($group && $group->searchSelector($selector)!==false){
				if(!$single) $rules[] = $rule;
				else return $rule;
			}
		}
		return isset($rules) ? $rules : null;
	}

	/**
	 * @return \Generator
	 */
	public function yieldRules(){
		foreach($this->rules as $i => $rule){
			yield $i => $rule;
		}
	}

}