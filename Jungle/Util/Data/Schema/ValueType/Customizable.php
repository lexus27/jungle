<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 25.07.2016
 * Time: 4:29
 */
namespace Jungle\Util\Data\Schema\ValueType {
	
	use Jungle\Util\Data\Schema\ValueType;
	use Jungle\Util\Value;

	/**
	 * Class Dynamic
	 * @package Jungle\Util\Data\Schema\ValueType
	 */
	class Customizable extends ValueType{

		/** @var  callable */
		protected $evaluate_function;

		/** @var  callable */
		protected $originate_function;

		/** @var  callable */
		protected $stabilize_function;

		/**
		 * Customizable constructor.
		 * @param $aliases
		 * @param $vartype
		 * @param array $rules
		 * @param callable $evaluateFunction
		 * @param callable $originateFunction
		 * @param callable $stabilizeFunction
		 */
		public function __construct($aliases, $vartype,
			array $rules,
			callable $evaluateFunction,
			callable $originateFunction,
			callable $stabilizeFunction = null
		){
			$this->setAlias($aliases);
			$this->vartype = $vartype;
			$this->rules                = $rules;
			$this->evaluate_function    = $evaluateFunction;
			$this->originate_function   = $originateFunction;
			$this->stabilize_function   = $stabilizeFunction;
		}

		/**
		 * @param $raw_value
		 * @param array $options
		 * @return mixed
		 */
		public function evaluate($raw_value, array $options = null){
			return !$this->evaluate_function?
				$raw_value:
				call_user_func($this->evaluate_function, $raw_value, array_replace($this->converter_options,(array)$options));
		}

		/**
		 * @param $evaluated_value
		 * @param array $options
		 * @return mixed
		 */
		public function originate($evaluated_value, array $options = null){
			return !$this->originate_function?
				$evaluated_value:
				call_user_func($this->originate_function, $evaluated_value, array_replace($this->converter_options,(array)$options));
		}

		/**
		 * @param $passed_evaluated_value
		 * @param array $options
		 * @return mixed
		 */
		public function stabilize($passed_evaluated_value, array $options = null){
			return !$this->stabilize_function?
				$passed_evaluated_value:
				call_user_func($this->stabilize_function, $passed_evaluated_value, array_replace($this->converter_options,(array)$options));
		}
	}
}

