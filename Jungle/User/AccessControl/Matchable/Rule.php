<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 14.02.2016
 * Time: 18:54
 */
namespace Jungle\User\AccessControl\Matchable {

	use Jungle\User\AccessControl\Context;
	use Jungle\User\AccessControl\Manager;
	use Jungle\User\AccessControl\Matchable;
	use Jungle\User\AccessControl\Matchable\Aggregator;

	/**
	 * Class Rule
	 * @package Jungle\User\AccessControl
	 */
	class Rule extends Matchable{

		/** @var  mixed */
		protected $condition;

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
		 * Произвести проверку данного правила в контексте $context
		 * @param Context $context
		 * @param \Jungle\User\AccessControl\Matchable\Aggregator $aggregator
		 * @return mixed result
		 * @throws \Exception
		 */
		public function match(Context $context, Aggregator $aggregator = null){
			$result = new Result($this,$context);
			try{
				$manager = $context->getManager();
				$effect = $this->getEffect();
				if($aggregator){
					$effect = $effect===null?$aggregator->getEffect():$effect;
				}
				$effect = $effect!==null?$manager->getDefaultEffect():$effect;

				$result->setMatchableEffect($effect);


				$target = $this->target;
				if($target && !$target($context,$result)){
					$result->setMissed(true);
					$result->setEffect(Matchable::NOT_APPLICABLE);
					return $result;
				}

				if($this->condition){
					$resolver = $manager->getConditionResolver();
					if($inspector = $resolver->getInspector()){
						$inspector->setMode('any_of');
					}
					if($resolver->resolve($context, $result, $this->condition)){
						$result->setEffect($effect);
					}else{
						$result->setEffect(Matchable::NOT_APPLICABLE);
					}
				}else{
					$result->setEffect($effect);
				}
			}catch(\Exception $e){
				throw $e;
			}
			return $result;
		}

	}
}

