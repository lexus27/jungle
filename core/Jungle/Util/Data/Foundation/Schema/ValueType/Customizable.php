<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 25.07.2016
 * Time: 4:29
 */
namespace Jungle\Util\Data\Foundation\Schema\ValueType {
	
	use Jungle\Util\Data\Foundation\Schema\ValueType;
	use Jungle\Util\Value;

	/**
	 * Class Dynamic
	 * @package Jungle\Util\Data\Foundation\Schema\ValueType
	 */
	class Customizable extends ValueType{

		/** @var  callable */
		protected $verify_function;

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
		 * @param callable $verifyFunction
		 * @param callable $evaluateFunction
		 * @param callable $originateFunction
		 * @param callable $stabilizeFunction
		 */
		public function __construct($aliases, $vartype,
			callable $verifyFunction,
			callable $evaluateFunction,
			callable $originateFunction,
			callable $stabilizeFunction = null
		){
			$this->setAlias($aliases);
			$this->vartype = $vartype;
			$this->verify_function = $verifyFunction;
			$this->evaluate_function = $evaluateFunction;
			$this->originate_function = $originateFunction;
			$this->stabilize_function = $stabilizeFunction;
		}

		/**
		 * @param $evaluated_value
		 * @param array $options
		 * @return bool
		 */
		public function verify($evaluated_value, array $options = null){
			return !$this->verify_function?
				$evaluated_value
				:call_user_func($this->verify_function, $evaluated_value, array_replace($this->default_options,(array)$options));
		}

		/**
		 * @param $raw_value
		 * @param array $options
		 * @return mixed
		 */
		public function evaluate($raw_value, array $options = null){
			return !$this->evaluate_function?
				$raw_value:
				call_user_func($this->evaluate_function, $raw_value, array_replace($this->default_options,(array)$options));
		}

		/**
		 * @param $evaluated_value
		 * @param array $options
		 * @return mixed
		 */
		public function originate($evaluated_value, array $options = null){
			return !$this->originate_function?
				$evaluated_value:
				call_user_func($this->originate_function, $evaluated_value, array_replace($this->default_options,(array)$options));
		}

		/**
		 * @param $passed_evaluated_value
		 * @param array $options
		 * @return mixed
		 */
		public function stabilize($passed_evaluated_value, array $options = null){
			return !$this->stabilize_function?
				$passed_evaluated_value:
				call_user_func($this->stabilize_function, $passed_evaluated_value, array_replace($this->default_options,(array)$options));
		}
	}
}

