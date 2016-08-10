<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 14.02.2016
 * Time: 18:55
 */
namespace Jungle\User\AccessControl\Policy {

	use Jungle\User\AccessControl\Context;
	use Jungle\User\AccessControl\Matchable;
	use Jungle\User\AccessControl\Policy;

	/**
	 * Class PolicyElement
	 * @package Jungle\User\AccessControl
	 */
	class PolicyElement extends Policy{

		/** @var  Rule[] */
		protected $rules = [];

		/**
		 * @param Rule $policy
		 * @return $this
		 */
		public function addRule(Rule $policy){
			if(!in_array($policy,$this->rules,true)){
				$this->rules[] = $policy;
			}
			return $this;
		}

		/**
		 * @param Rule $policy
		 * @return $this
		 */
		public function removeRule(Rule $policy){
			if(($i = array_search($policy,$this->rules,true))!==false){
				array_splice($this->rules,$i,1);
			}
			return $this;
		}

		/**
		 * @return Matchable[]|Rule[]
		 */
		public function getContains(){
			return $this->rules;
		}



	}
}

