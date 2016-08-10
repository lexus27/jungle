<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 09.02.2016
 * Time: 22:21
 */
namespace Jungle\TypeHint {

	use Jungle\TypeHint;
	use Jungle\Util\Value\Massive;

	/**
	 * Class Rule
	 * @package Jungle\TypeHint
	 */
	class Rule{

		/**
		 * @var string
		 */
		protected $type;

		/**
		 * @var array
		 */
		protected $parameters = [];

		/**
		 * @var string|null
		 */
		protected $comment;

		/**
		 * @var TypeChecker[]|string[]
		 */
		protected $last_bad_checking_data;

		/**
		 * @param null $type
		 * @param array $parameters
		 * @param null $comment
		 */
		public function __construct($type = null,array $parameters = [], $comment = null){
			$this->setType($type);
			$this->setParameters($parameters);
			$this->setComment($comment);
		}

		/**
		 * @param $type
		 */
		public function setType($type){
			$this->type = $type;
		}

		/**
		 * @return string
		 */
		public function getType(){
			return $this->type;
		}

		/**
		 * @param $key
		 * @param $value
		 * @param null $modeExists
		 * @param string $checkType
		 * @return $this
		 */
		public function setParam($key, $value, $modeExists = null, $checkType = Massive::CHECK_TYPE_ISSET){
			Massive::setItem($this->parameters,$key,$value,$modeExists,$checkType);
			return $this;
		}

		/**
		 * @param $key
		 * @param null $default
		 * @param null $modeExists
		 * @param string $checkType
		 * @return $this
		 */
		public function getParam($key, $default = null, $modeExists = null, $checkType = Massive::CHECK_TYPE_ISSET){
			return Massive::getItem($this->parameters,$key,$default,null,$modeExists,$checkType);
		}

		/**
		 * @param array $parameters
		 * @param null $modeExists
		 * @param string $checkType
		 * @return $this
		 */
		public function setParameters(array $parameters, $modeExists = null, $checkType = Massive::CHECK_TYPE_ISSET){
			Massive::setItems($this->parameters,$parameters,$modeExists,$checkType);
			return $this;
		}

		/**
		 * @return array
		 */
		public function getParameters(){
			return $this->parameters;
		}


		/**
		 * @param $comment
		 */
		public function setComment($comment){
			$this->comment = $comment;
		}

		/**
		 * @return null|string
		 */
		public function getComment(){
			return $this->comment;
		}

		/**
		 * @param $value
		 * @param TypeHint $hinter
		 * @return bool
		 */
		public function check($value, TypeHint $hinter){
			$this->last_bad_checking_data = false;
			foreach($hinter->getCheckers() as $checker){
				$checker->begin($hinter);
				if($checker->verify($this->type)){
					if($checker->check($value,$this->parameters)){
						return true;
					}else{
						$this->last_bad_checking_data = [$checker, $checker->exportInternalErrorMessage()];
						$checker->end();
						return false;
					}

				}
				$checker->end();
			}
			return false;
		}

		/**
		 * @return bool
		 */
		public function isBadChecking(){
			return $this->last_bad_checking_data!==false;
		}

		/**
		 * @return TypeChecker[]|\string[]|Rule[]
		 */
		public function getBadCheckingData(){
			return $this->last_bad_checking_data;
		}

		/**
		 * @return TypeChecker
		 */
		public function getTypeChecker(){
			return $this->isBadChecking()?$this->last_bad_checking_data[0]:null;
		}

		/**
		 * @return string
		 */
		public function getInternalErrorMessage(){
			return $this->isBadChecking()?$this->last_bad_checking_data[1]:null;
		}


	}
}

