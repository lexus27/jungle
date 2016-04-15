<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 09.02.2016
 * Time: 22:21
 */
namespace Jungle\TypeHint\Rule {

	use Jungle\TypeHint;
	use Jungle\TypeHint\Rule;
	use Jungle\TypeHint\TypeChecker;

	/**
	 * Class Complex
	 * @package Jungle\TypeHint\Rule
	 */
	class Complex{

		/** @var Rule[] */
		protected $rules = [];

		/** @var  mixed */
		protected $value;

		/** @var  bool|string */
		protected $error = false;

		/**
		 * @param $value
		 */
		public function setValue(& $value){
			$this->value = & $value;
		}

		/**
		 * @return mixed
		 */
		public function & getValue(){
			return $this->value;
		}

		/**
		 * @param Rule $rule
		 * @return $this
		 */
		public function addRule(Rule $rule){
			if($this->searchRule($rule)===false){
				$this->rules[] = $rule;
			}
			return $this;
		}

		/**
		 * @param Rule $rule
		 * @return bool|int
		 */
		public function searchRule(Rule $rule){
			return array_search($rule,$this->rules,true);
		}

		/**
		 * @param Rule $rule
		 * @return $this
		 */
		public function removeRule(Rule $rule){
			if(($i = $this->searchRule($rule))!==false){
				array_splice($this->rules, $i ,1);
			}
			return $this;
		}


		/**
		 * @param TypeHint $hinter
		 * @param bool $mutableCheck
		 * @param null $requiredParameterNameProvide
		 * @return bool
		 */
		public function check(TypeHint $hinter,$mutableCheck = false,$requiredParameterNameProvide = null){
			$this->error = null;
			$collection = [];
			foreach($this->rules as $rule){
				if($rule->check($this->value,$hinter)){
					return true;
				}
				if(!$mutableCheck && $rule->isBadChecking()){
					$collection[] = $c = [
						$rule->getTypeChecker(),
						$rule->getInternalErrorMessage(),
						$rule
					];
				}

			}
			if($mutableCheck){
				return false;
			}
			$error_message = [];
			/**
			 * @var int $i
			 * @var TypeChecker $checker
			 * @var string $internal_message
			 * @var Rule $rule
			 */
			foreach($collection as $i => list($checker, $internal_message,$rule)){
				$error_message[] = $checker->getErrorMessage($this->value,$rule->getType(),$internal_message,$requiredParameterNameProvide);
			}

			$this->error = "Value type-hint error(s), Solve one problem:\r\n ". implode("\r\n\tOR\r\n",$error_message);
			return false;
		}

		/**
		 * @return bool|string
		 */
		public function getError(){
			return $this->error;
		}



	}
}

