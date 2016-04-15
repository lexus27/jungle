<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 14.02.2016
 * Time: 18:54
 */
namespace Jungle\User\Access\ABAC\Policy {

	use Jungle\User\Access\ABAC\Context;

	/**
	 * Class Rule
	 * @package Jungle\User\Access\ABAC
	 */
	class Rule extends Matchable{

		/** @var  string */
		protected $condition;


		/**
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @param string $name
		 * @return $this
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}

		/**
		 * @param bool $effect
		 * @return $this
		 */
		public function setEffect($effect = self::DENY){
			$this->effect = $effect;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function getEffect(){
			return $this->effect;
		}


		/**
		 * @param string $condition
		 * @return $this
		 */
		public function setCondition($condition){
			$this->condition = $condition;
			return $this;
		}
		/**
		 * @return string
		 */
		public function getCondition(){
			return $this->condition;
		}


		/**
		 * Применим ли контекст $context к данному правилу
		 * @param Context $context
		 * @return bool
		 */
		public function isApplicable(Context $context){
			if($this->target){
				return $this->target->isApplicable($context);
			}
			return true;
		}

		/**
		 * Произвести проверку данного правила в контексте $context
		 * @param Context $context
		 * @return mixed result
		 */
		public function match(Context $context){
			$result = new MatchResult($this);
			try{
				if($this->target && !$this->target->isApplicable($context)){
					$result->setResult(self::NOT_APPLICABLE);
					$this->invokeEvent('match',$this,$result,true);
					return $result;
				}
				if($this->condition){
					if($context->getManager()->requireConditionResolver()->check($this->condition,$context)){
						$result->setResult($this->getEffect());
					}else{
						$result->setResult(self::NOT_APPLICABLE);
					}
				}else{
					$result->setResult($this->getEffect());
				}
			}catch(\Exception $e){
				$result->setIndeterminate($e->getMessage());
			}
			$this->invokeEvent('match',$this,$result);
			return $result;
		}

	}
}

