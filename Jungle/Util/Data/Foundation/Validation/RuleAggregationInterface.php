<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 16:14
 */
namespace Jungle\Util\Data\Foundation\Validation {

	/**
	 * Interface RuleAggregationInterface
	 * @package Jungle\Util\Data\Foundation\Validation
	 */
	interface RuleAggregationInterface extends ValueCheckerInterface{

		/**
		 * @return Rule[]
		 */
		public function getRules();

		/**
		 * @param Rule $rule
		 * @return mixed
		 */
		public function addRule(Rule $rule);

		/**
		 * @param $value
		 * @return bool
		 */
		public function check($value);



		/**
		 * @return mixed
		 */
		public function getLastValue();

		/**
		 * @return Rule[]
		 */
		public function getLastRules();


	}
}

