<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.02.2016
 * Time: 1:47
 */
namespace Jungle\User\AccessControl\Adapter {

	use Jungle\User\AccessControl\Policy;
	use Jungle\User\AccessControl\Policy\Rule;

	/**
	 * Class PolicyAdapter
	 * @package Jungle\User\AccessControl\Adapter
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

