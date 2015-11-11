<?php
/**
 * Created by PhpStorm.
 * Project: jungle
 * Date: 09.04.2015
 * Time: 16:42
 */

namespace Jungle\Delegator\Code {

	/**
	 * Class ClosureX
	 * @package Jungle\Delegator\Code
	 */
	class ClosureX implements \Serializable{

		/**
		 * @var array|null
		 */
		protected $arguments;

		/**
		 * @var string|null
		 */
		protected $code;

		/**
		 * @var callable
		 */
		protected $callable;


		/**
		 * @param callable $callable
		 */
		public function setCallable(callable $callable){
			if(!$this->callable && !$this->isString()){
				$this->callable = $callable;
			}else{
				throw new \LogicException('ClosureX is locked');
			}
		}

		/**
		 * @param $args
		 * @param $code
		 */
		public function setString($args, $code){
			if(!$this->callable && !$this->isString()){
				$arguments = [];
				$args = explode(',', $args);
				foreach($args as $arg){
					$hint = null;
					$var = null;
					$default = null;
					if(preg_match('@\s*?(([\w\\\\_]+)\s*?)?(\$[\w\_]+)\s*?(=\s*?([.\r\n\t]+))@', $arg, $m)){
						if($m[1]) $hint = $m[2] ? $m[2] : null;
						$var = $m[3];
						if($m[4]) $default = $m[5];
					}
					$arguments[ltrim($var, '$')] = [$hint, $default];
				}
				$this->arguments = $arguments;
				$this->code = $code;
			}else{
				throw new \LogicException('ClosureX is locked');
			}
		}


		/**
		 * @return bool
		 */
		public function isString(){
			return (bool)$this->code;
		}

		/**
		 * @return mixed
		 */
		public function __invoke(){
			if(!$this->callable && $this->isString()){
				$this->callable = create_function($this->arguments, $this->code);
			}
			if(!$this->callable){
				throw new \LogicException();
			}
			return call_user_func_array($this->callable, func_get_args());
		}

		/**
		 * @return string
		 */
		public function __toString(){
			return $this->isString() ? 'function(' . $this->getArgumentsString() . '){' . $this->code . '}' : '';
		}

		/**
		 * @return string
		 */
		public function getArgumentsString(){
			$args = [];
			foreach($this->arguments as $paramName => list($hint, $default)){
				$args[] = ($hint ? $hint . ' ' : '') . ('$' . $paramName) . ($default ? ' = ' . $default : '');
			}
			return implode(', ', $args);
		}

		/**
		 * @return int
		 */
		public function argumentsCount(){
			return count($this->arguments);
		}

		/**
		 * @return string
		 */
		public function serialize(){
			return serialize([$this->getArgumentsString(), $this->code]);
		}

		/**
		 * @param string $serialized
		 */
		public function unserialize($serialized){
			list($arguments, $code) = unserialize($serialized);
			$this->setString($arguments, $code);
		}
	}
}