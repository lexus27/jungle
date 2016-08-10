<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 20.01.2016
 * Time: 21:01
 */
namespace Jungle\Util\Communication\Stream\Command {

	use Jungle\Util\Communication\Stream;

	/**
	 * Class Rule
	 * @package Jungle\Util\Communication\Stream\Command
	 */
	class Rule{

		/** @var int|string|int[]|string[]|null */
		protected $code;

		/** @var string */
		protected $error_message;

		/** @var bool */
		protected $negated = false;

		/** @var callable|string */
		protected $behaviour = false;

		/**
		 * @param null|int|string|int[]|string[] $code
		 * @param null $failure_message
		 * @param bool|false $negated
		 */
		public function __construct($code = null, $failure_message = null, $negated = false){
			$this->setErrorMessage($failure_message);
			$this->setCode($code);
			$this->setNegated($negated);
		}

		/**
		 * @param int|string|int[]|string[]|null $code
		 * @return $this
		 */
		public function setCode($code){
			$this->code = $code;
			return $this;
		}

		/**
		 * @param bool|true $negated
		 * @return $this
		 */
		public function setNegated($negated = true){
			$this->negated = $negated;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isNegated(){
			return $this->negated;
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
		public function getErrorMessage(){
			return $this->error_message;
		}

		/**
		 * @param $code
		 * @return bool
		 */
		public function match($code){
			$matched = is_array($this->code)?in_array($code,$this->code,true):$this->code === $code;
			return $this->negated?$matched:(!$matched);
		}

		/**
		 * @param callable|array|bool $behaviour
		 * @return $this
		 */
		public function setBehaviour($behaviour = null){
			$this->behaviour = $behaviour;
			return $this;
		}

		/**
		 * @return callable
		 */
		public function getBehaviour(){
			return $this->behaviour;
		}

	}
}

