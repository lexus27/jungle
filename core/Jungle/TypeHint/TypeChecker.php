<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 28.10.2015
 * Time: 0:11
 */
namespace Jungle\TypeHint {

	use Jungle\Basic\INamed;
	use Jungle\TypeHint;
	use Jungle\Util\Value\String;

	/**
	 * Class TypeChecker
	 * @package Jungle\TypeHint
	 */
	abstract class TypeChecker implements INamed{

		/** @var string */
		protected $name;

		/** @var int */
		protected $priority = 0;

		/** @var array */
		protected $aliases = [];

		/** @var null|string|array */
		protected $verified = null;

		/** @var string|null , set string if value check maybe checked once standard function */
		protected $checker_function = null;

		/** @var bool */
		protected $checker_function_code = false;

		/** @var string */
		protected $error_message = 'Passed "{{valueGetType}}":"{{value}}" value {{param}} is not a {{hinter}}!, and internal message {{internal_message}}';

		/** @var  null|string */
		protected $last_internal_error_message;

		/** @var TypeHint */
		protected $hinter;



		/**
		 * @param $value
		 * @param $typeString
		 * @param $internal_message
		 * @param $paramName
		 * @return string
		 */
		public function getErrorMessage($value, $typeString, $internal_message, $paramName = null){
			$tplValues = [
				'value'             => substr(String::representFrom($value), 0, 20),
				'type'              => $typeString,
				'param'             => $paramName,
				'hinter'            => $this->name,
				'internal_message'  => $internal_message,
				'valueGetType'      => gettype($value),
			];

			if(is_callable($this->error_message)){
				return call_user_func($this->error_message,$tplValues,$this);
			}

			$keys = array_map(
				function ($k){
					return "{{{$k}}}";
				},
				array_keys($tplValues)
			);
			return str_replace($keys, array_values($tplValues), $this->error_message);
		}

		/**
		 * @param $value
		 * @param array $parameters
		 * @return bool
		 */
		protected function validateByParameters($value, array $parameters = []){
			$p = $this->normalizeParameters($parameters,[
				'not'       => null,
				'range'     => null
			]);

			if($p['not']!==null && (
					(is_array($p['not']) && in_array($value,$p['not'],true)) ||
					($value === $p['not'])
				)
			){
				$this->last_internal_error_message = 'String is not compatible with matcher "'.$p['match'].'"';
				return false;
			}

			if($p['required']!==null && (
					(is_array($p['required']) && !in_array($value,$p['required'],true)) ||
					($value !== $p['required'])
				)
			){
				$this->last_internal_error_message = 'String is not compatible with matcher "'.$p['match'].'"';
				return false;
			}

			return true;
		}

		/**
		 * @param array $parameters
		 * @param array $defaultParameters
		 * @return array
		 */
		public function normalizeParameters(array $parameters = [],array $defaultParameters = []){
			$a = [];
			foreach($defaultParameters as $k => & $v){
				if(isset($parameters[$k])){
					$a[$k] = $parameters[$k];
				}else{
					$a[$k] = $v;
				}
			}
			if(!isset($a['verified']) || !$a['verified']){
				$a['verified'] = $this->getVerified();
			}
			return $a;
		}

		/**
		 * @return bool
		 */
		public function hasLastErrorMessage(){
			return (bool)$this->last_internal_error_message;
		}

		/**
		 * @return null|string
		 */
		public function exportInternalErrorMessage(){
			$m = $this->last_internal_error_message;
			$this->last_internal_error_message = null;
			return $m;
		}

		/**
		 * @return int
		 */
		public function getPriority(){
			return $this->priority;
		}

		/**
		 * @param $priority
		 *
		 * @return $this
		 */
		public function setPriority($priority){
			$this->priority = $priority;
			return $this;
		}

		/**
		 * @param $message
		 * @return $this
		 */
		public function setErrorMessage($message){
			$this->error_message = $message;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @param $name
		 */
		public function setName($name){
			$this->name = $name;
		}

		/**
		 * @param null $function
		 * @param bool $asCode
		 */
		public function setCheckerFunction($function = null, $asCode = false){
			$this->checker_function = $function;
			if($this->checker_function && $asCode){
				if($asCode){
					$this->checker_function = create_function('$value,$checker', $function);
					$this->checker_function_code = $asCode;
				}else{
					if(!is_callable($function)){
						throw new \LogicException('setCheckerFunction passed not callable');
					}
					$this->checker_function = $function;
					$this->checker_function_code = false;
				}

			}

			if(!$this->checker_function){
				$this->checker_function = null;
				$this->checker_function_code = false;
			}

		}

		/**
		 * @param TypeHint $hinter
		 * @return $this
		 */
		public function begin(TypeHint $hinter){
			$this->hinter = $hinter;
			return $this;
		}

		/**
		 *
		 */
		public function end(){
			$this->hinter = null;
			$this->last_internal_error_message = null;
		}

		/**
		 * @return TypeHint
		 */
		public function getHinter(){
			return $this->hinter;
		}

		/**
		 * @return array|null|string
		 */
		public function getVerified(){
			return $this->verified?$this->verified:$this->name;
		}

		/**
		 * Проверяет значение на тип.
		 * @param mixed $value
		 * @param array $parameters
		 * @return bool
		 */
		public function check($value, array $parameters = []){
			if($this->validateByParameters($value, $parameters)===false){
				return false;
			}
			if(!empty($this->checker_function)){
				$f = $this->checker_function;
			}else{
				$f = 'is_' . $this->name;
				if(!function_exists($f)){
					throw new \LogicException('checker_function "' . $f . '" not exists.');
				}
			}
			return $this->checker_function_code ? call_user_func($f, $value, $this) : call_user_func($f, $value);
		}

		/**
		 * Определяет Соответствует ли переданая строка с именем TypeChecker ,
		 * текущему TypeChecker для дальнейшей проверки через @see check()
		 * @param string $type
		 *
		 * @return bool
		 */
		public function verify($type){
			$stack = $this->aliases;
			array_unshift($stack, $this->name);
			$stack = array_unique($stack);
			foreach($stack as $alias){
				if(strcasecmp($alias, $type) === 0){
					$this->verified = $type;
					return true;
				}
			}
			return false;
		}

	}
}

