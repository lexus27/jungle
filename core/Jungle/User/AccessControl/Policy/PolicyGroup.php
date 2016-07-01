<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 14.02.2016
 * Time: 18:55
 */
namespace Jungle\User\AccessControl\Policy {

	use Jungle\User\AccessControl\Context;
	use Jungle\User\AccessControl\Policy;

	/**
	 * Class PolicyGroup
	 * @package Jungle\User\AccessControl
	 */
	class PolicyGroup extends Policy{

		/** @var  Policy[] */
		protected $policies = [];

		/**
		 * @param Policy $policy
		 * @return $this
		 */
		public function addPolicy(Policy $policy){
			if(!in_array($policy,$this->policies,true)){
				$this->policies[] = $policy;
				$policy->setParent($this);
			}
			return $this;
		}

		/**
		 * @param Policy $policy
		 * @return $this
		 */
		public function removePolicy(Policy $policy){
			if(($i = array_search($policy,$this->policies,true))!==false){
				array_splice($this->policies,$i,1);
				$policy->setParent(null);
			}
			return $this;
		}

		/**
		 * @return Matchable[]|Policy[]
		 */
		public function getContains(){
			return $this->policies;
		}

	}
}

