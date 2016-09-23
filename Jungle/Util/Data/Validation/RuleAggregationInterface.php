<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 16:14
 */
namespace Jungle\Util\Data\Validation {

	use Jungle\Util\Data\Validation\Message\RuleMessageInterface;

	/**
	 * Interface RuleAggregationInterface
	 * @package Jungle\Util\Data\Validation
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
		 * @return RuleMessageInterface[]
		 */
		public function getLastMessages();


	}
}

