<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 14.02.2016
 * Time: 18:54
 */
namespace Jungle\User\AccessControl\Policy {

	use Jungle\User\AccessControl\Context;
	use Jungle\User\AccessControl\Manager;
	use Jungle\User\AccessControl\Matchable;
	use Jungle\User\AccessControl\Policy;

	/**
	 * Class Rule
	 * @package Jungle\User\AccessControl
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
		 * @param Policy $parent
		 * @return mixed result
		 */
		public function match(Context $context, Policy $parent = null){
			$result = new MatchResult($this);
			try{
				if($this->target && !$this->target->isApplicable($context)){
					$result->setResult(self::NOT_APPLICABLE);
					$this->invokeEvent('match',$this,$result,true);
					return $result;
				}
				$effect = $this->getEffect();
				if($parent){
					$effect = $effect===null?$parent->getEffect():$effect;
				}
				if($effect===null){
					$effect = $context->getManager()->getBasedEffect();
				}
				if($this->condition){
					if($context->getManager()->requireConditionResolver()->check($this->condition,$context)){
						$result->setResult($effect);
					}else{
						$result->setResult(self::NOT_APPLICABLE);
					}
				}else{
					$result->setResult($effect);
				}
			}catch(\Exception $e){
				$result->setIndeterminate($e->getMessage());
			}
			$this->invokeEvent('match',$this,$result);
			return $result;
		}

	}
}

