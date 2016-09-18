<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 16:11
 */
namespace Jungle\Util\Data\Foundation\Validation {

	use Jungle\Util\Data\Foundation\Validation\Message\RuleMessage;
	use Jungle\Util\Data\Foundation\Validation\Message\RuleMessageInterface;

	/**
	 * Trait RuleAggregationTrait
	 * @package Jungle\Util\Data\Foundation\Validation
	 */
	trait RuleAggregationTrait{

		/** @var  Rule[] */
		protected $rules = [];

		/** @var  mixed  */
		protected $last_value;

		/** @var  RuleMessageInterface[]  */
		protected $last_messages = [];

		/**
		 * @param Rule $rule
		 * @return $this
		 */
		public function addRule(Rule $rule){
			$this->rules[] = $rule;
			return $this;
		}

		/**
		 * @return Rule[]
		 */
		public function getRules(){
			return $this->rules;
		}

		/**
		 * @param $value
		 * @return boolean
		 */
		public function check($value){
			$messages = [];
			foreach($this->rules as $rule){
				if(!$rule->check($value)){
					$messages[] = new RuleMessage($rule->getType(), $rule->getParams());
				}
			}
			$this->last_value = $value;
			$this->last_messages = $messages;
			return empty($messages);
		}

		/**
		 * @return mixed
		 */
		public function getLastValue(){
			return $this->last_value;
		}

		/**
		 * @return RuleMessageInterface[]
		 */
		public function getLastMessages(){
			return $this->last_messages;
		}

	}
}

