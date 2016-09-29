<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 15.02.2016
 * Time: 0:16
 */
namespace Jungle\User\AccessControl {

	use Jungle\Application\Component;
	use Jungle\User\AccessControl\Adapter\ContextAdapter;
	use Jungle\User\AccessControl\Context\ObjectAccessor;
	use Jungle\User\AccessControl\Matchable\Aggregator;
	use Jungle\User\AccessControl\Matchable\Combiner;
	use Jungle\User\AccessControl\Matchable\ExpressionResolver;
	use Jungle\User\AccessControl\Matchable\Resolver\ConditionResolver;
	use Jungle\User\AccessControl\Matchable\Resolver\PredicateInspector;
	use Jungle\User\AccessControl\Matchable\Result;
	use Jungle\User\AccessControl\Matchable\Rule;

	/**
	 * Class Pool
	 * @package Jungle\User\AccessControl
	 */
	class Manager extends Component{

		/** @var  Context */
		protected $context;

		/** @var  Aggregator */
		protected $aggregator;

		/** @var  ConditionResolver */
		protected $condition_resolver;

		/** @var  PredicateInspector */
		protected $predicate_inspector;

		/** @var  Combiner */
		protected $default_combiner;

		/** @var  Combiner */
		protected $main_combiner;

		/** @var Combiner[]  */
		protected $combiners = [];



		/** @var  string */
		protected $same_effect = Matchable::DENY;

		/** @var  string */
		protected $default_effect = Matchable::PERMIT;

		/**
		 * @param Context $context
		 * @return $this
		 */
		public function setContext(Context $context){
			$this->context = $context;
			return $this;
		}

		/**
		 * @return Context
		 * @throws Exception
		 */
		public function getContext(){
			if(!$this->context){
				$this->context = new Context();
			}
			return $this->context;
		}

		/**
		 * @param Aggregator $adapter
		 * @return $this
		 */
		public function setAggregator(Aggregator $adapter){
			$this->aggregator = $adapter;
			return $this;
		}

		/**
		 * @return Aggregator
		 * @throws Exception
		 */
		public function getAggregator(){
			if(!$this->aggregator){
				throw new Exception('Aggregator is not set to ABAC::SignManager');
			}
			return $this->aggregator;
		}


		/**
		 * @param $combiner_key
		 * @return Combiner
		 * @throws Exception
		 */
		public function getCombiner($combiner_key = null){
			if($combiner_key === null){
				return $this->getDefaultCombiner();
			}
			if($combiner_key instanceof Combiner){
				return $combiner_key;
			}
			if(isset($this->combiners[$combiner_key])){
				return $this->combiners[$combiner_key];
			}else{
				throw new Exception('Combiner with key "'.$combiner_key.'" not found');
			}
		}

		/**
		 * @param $key
		 * @param Combiner $combiner
		 * @return $this
		 */
		public function setCombiner($key, Combiner $combiner){
			$this->combiners[$key] = $combiner;
			return $this;
		}


		/**
		 * @param Combiner|string $algorithm
		 * @return $this
		 */
		public function setDefaultCombiner($algorithm){
			$this->default_combiner = $algorithm;
			return $this;
		}

		/**
		 * @return Combiner
		 * @throws Exception
		 */
		public function getDefaultCombiner(){
			return $this->getCombiner($this->default_combiner);
		}

		/**
		 * @param Combiner|string $algorithm
		 * @return $this
		 */
		public function setMainCombiner($algorithm){
			$this->main_combiner = $algorithm;
			return $this;
		}

		/**
		 * @return Combiner
		 * @throws Exception
		 */
		public function getMainCombiner(){
			return $this->getCombiner($this->main_combiner);
		}


		/**
		 * @param ConditionResolver $resolver
		 * @return $this
		 */
		public function setConditionResolver(ConditionResolver $resolver){
			$this->condition_resolver = $resolver;
			return $this;
		}

		/**
		 * @return \Jungle\User\AccessControl\Matchable\Resolver\ConditionResolver
		 */
		public function getConditionResolver(){
			if(!$this->condition_resolver){
				$this->condition_resolver = new ConditionResolver();
			}
			return $this->condition_resolver;
		}

		/**
		 * @param PredicateInspector $inspector
		 * @return $this
		 */
		public function setPredicateInspector(PredicateInspector $inspector){
			$this->predicate_inspector = $inspector;
			return $this;
		}

		/**
		 * @return PredicateInspector
		 */
		public function getPredicateInspector(){
			if(!$this->predicate_inspector){
				$this->predicate_inspector = new PredicateInspector();
			}
			return $this->predicate_inspector;

		}


		/**
		 * @param $effect
		 * @return $this
		 */
		public function setDefaultEffect($effect){
			$this->default_effect = $effect;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getDefaultEffect(){
			return $this->default_effect;
		}

		/**
		 * @param $same
		 * @return $this
		 */
		public function setSameEffect($same){
			$this->same_effect = $same;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getSameEffect(){
			return $this->same_effect;
		}


		/**
		 * @param $action
		 * @param $object
		 * @param bool $returnResult
		 * @return Result|bool
		 */
		public function enforce($action, $object, $returnResult = false){
			$resolver = $this->getConditionResolver();
			$context = $this->contextFrom($action,$object);
			$collectByEffect = null;

			$object = $context->getObject();
			if($object instanceof ObjectAccessor && $object->hasPredicateEffect()){
				$collectByEffect = Matchable::friendlyEffect($object->getPredicateEffect());
				$inspector = $this->getPredicateInspector();
				$inspector->setTargetEffect($collectByEffect);
				$resolver->setInspector($inspector);

			}else{
				$resolver->setInspector(null);
			}
			$result = $this->decise($context);
			if($returnResult || $collectByEffect!==null){
				return $result;
			}
			return $result->isAllowed();
		}

		/**
		 * @param Context $context
		 * @return Result
		 */
		public function decise(Context $context){
			$sameEffect = $this->getSameEffect();

			$aggregator = $this->getAggregator();
			$combiner = $this->getMainCombiner();
			$combiner->setSame($sameEffect);

			$result = new Result($aggregator,$context);
			$result->setMatchableEffect($sameEffect);

			$result = $combiner->match($result, $aggregator, $context);
			$this->_handleResult($context, $result);


			$effect = $result->getEffect();
			if(in_array($effect,[Rule::DENY,Rule::PERMIT],true)){
				try{
					$results = $result->getChildren();
					foreach($results as $r){
						$this->invoke($effect,$r,$context);
					}
				}catch(\Exception $e){
					$result->setEffect(Rule::INDETERMINATE);
				}
			}elseif(in_array($effect,[Rule::NOT_APPLICABLE,Rule::INDETERMINATE],true)){
				$results = $result->getChildren();
				foreach($results as $r){
					$this->invoke($effect,$r,$context);
				}
				$result->setEffect($aggregator->getEffect());
			}else{
				throw new \LogicException('Bad result effect '. var_export($effect, true));
			}
			return $result;
		}

		/**
		 * @param Context $context
		 * @param Result $result
		 */
		protected function _handleResult(Context $context,Result $result){
			$inspector  = $this->condition_resolver->getInspector();
			if($inspector instanceof PredicateInspector){
				$conditions = $inspector->collectCondition($context, $result);
				if($conditions){
					$object = $context->getObject();
					if($object instanceof ObjectAccessor){
						$object->setPredicatedConditions($conditions);
					}
				}
			}
		}


		/**
		 * @param $action
		 * @param $object
		 * @return Context
		 */
		public function contextFrom($action, $object){
			if(is_string($object)){
				if(($object = trim($object)) && substr($object,0,1)==='[' && substr($object,-1)===']'){
					$object = rtrim($object,']');
					$object = ltrim($object,'[');

					$object = explode(',',$object);
					if($object){
						$_o = [];
						foreach($object as $pair){
							$pair = explode(':',$pair);
							$_o[trim($pair[0])] = trim($pair[1]);
						}
						$object = ObjectAccessor::release($_o);
					}
				}else{
					throw new \LogicException('Object definition is invalid! "'.$object.'"');
				}
			}
			$context = $this->getContext();
			$context = clone $context;
			$context->setManager($this);
			$context->setProperties([
				'action' => is_string($action)?['name' => $action]:$action,
				'object' => $object
			], true);
			return $context;
		}

		/**
		 * @param $finalEffect
		 * @param Result $result
		 * @param Context $context
		 * @throws \Exception
		 */
		protected function invoke($finalEffect,Result $result,Context $context){
			$matchable = $result->getMatchable();
			if($matchable instanceof Aggregator){
				foreach($result->getChildren() as $child){
					$this->invoke($finalEffect,$child,$context);
				}
			}
			$effect = $result->getEffect();
			$sameEffect = $matchable->getEffect()?:$this->getDefaultEffect();
			if($sameEffect === $finalEffect && $effect === $sameEffect){
				$obligation = $matchable->getObligation();
				if($obligation){
					try{
						if(call_user_func($obligation,$result,$context)===false){
							throw new Exception('Obligation is not executed');
						}
					}catch(\Exception $e){
						$this->_handleImportantException($e, $result, $obligation);
					}
				}
				$advice = $matchable->getAdvice();
				if($advice){
					try{
						call_user_func($advice,$result,$context);
					}catch(\Exception $e){
						$this->_handleException($e, $result);
					}
				}
			}elseif(!$result->isMissed()){
				$requirements = $matchable->getRequirement();
				if($requirements){
					try{
						call_user_func($requirements,$result,$context);
					}catch(\Exception $e){
						$this->_handleException($e, $result);
					}
				}
			}
		}

		/**
		 * @param \Exception $exception
		 * @throws \Exception
		 */
		protected function _handleException(\Exception $exception, Result $result){

		}

		/**
		 * @param \Exception $exception
		 * @param Result $result
		 * @param callable $obligation
		 * @throws \Exception
		 */
		protected function _handleImportantException(\Exception $exception, Result $result, callable $obligation = null){
			$result->setEffect('indeterminate');
			throw $exception;
		}


	}
}

