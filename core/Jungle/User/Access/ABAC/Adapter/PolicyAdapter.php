<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.02.2016
 * Time: 1:47
 */
namespace Jungle\User\Access\ABAC\Adapter {

	use Jungle\User\Access\ABAC\Policy;
	use Jungle\User\Access\ABAC\Policy\Rule;

	/**
	 * Class PolicyAdapter
	 * @package Jungle\User\Access\ABAC\Adapter
	 */
	abstract class PolicyAdapter{

		/**
		 * @param Rule $policy
		 * @return $this
		 */
		abstract public function addRule(Rule $policy);
		/**
		 * @param $name
		 * @return Rule
		 */
		abstract public function getRule($name);
		/**
		 * @return Rule[]
		 */
		abstract public function getRules();

		/**
		 * @param Policy $policy
		 * @return $this
		 */
		abstract public function addPolicy(Policy $policy);

		/**
		 * @param $name
		 * @return Policy
		 */
		abstract public function getPolicy($name);
		/**
		 * @return Policy[]
		 */
		abstract public function getPolicies();

	}
}

