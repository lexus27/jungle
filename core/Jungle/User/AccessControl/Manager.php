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
	use Jungle\User\AccessControl\Adapter\PolicyAdapter;
	use Jungle\User\AccessControl\Context\ObjectAccessor;
	use Jungle\User\AccessControl\Policy;
	use Jungle\User\AccessControl\Policy\Combiner;
	use Jungle\User\AccessControl\Policy\ConditionResolver;
	use Jungle\User\AccessControl\Policy\ExpressionResolver;
	use Jungle\User\AccessControl\Policy\MatchResult;
	use Jungle\User\AccessControl\Policy\Rule;
	use Jungle\Util\ObservableTrait;
	use Jungle\Util\Value\Massive;
	use Jungle\Util\Value\String;

	/**
	 * Class Pool
	 * @package Jungle\User\AccessControl
	 */
	class Manager extends Component{

		use ObservableTrait;

		/** @var  ContextAdapter */
		protected $context_adapter;

		/** @var  PolicyAdapter */
		protected $policy_adapter;

		/** @var  ConditionResolver */
		protected $condition_resolver;

		/** @var  ExpressionResolver */
		protected $expression_resolver;





		/** @var   */
		protected $_listeners_propagation;


		/** @var  Combiner */
		protected $main_combiner;

		/** @var  Combiner */
		protected $default_combiner;

		/** @var Combiner[]  */
		protected $combiners = [];

		/**
		 *
		 * Базовый эффект на основе чего будет считаться текущий менеджер Policy Enforcement Point
		 *
		 * @see Policy\Matchable::DENY - Запрещающий PEP
		 * - Запрещающий (Deny based) PEP действует по принципу «Запрещено все, что явно не разрешено».
		 * Это означает, что только в случае положительного решения доступ будет разрешен.
		 * В остальных трех случаях доступ будет запрещен, А с корректировкой
		 * @see $base_effect_strict===true только в 1 инверсном(PERMIT) случае
		 *
		 * @see Policy\Matchable::PERMIT
		 * - Разрешающий (Permit based) PEP действует по принципу «Разрешено все, что явно не запрещено».
		 * Это означает, что только в случае отрицательного решения будет запрещен доступ.
		 * В остальных трех случаях доступ будет разрешен, А с корректировкой
		 * @see $base_effect_strict===true только в 1 инверсном(DENY) случае
		 *
		 * @var bool
		 */
		protected $based_effect = \Jungle\User\AccessControl\Matchable::PERMIT;

		/**
		 * Строгость базового эффекта если равен true
		 * то для достижения $based_effect требуется чтобы ответ от decise был эквивалентом базового решения
		 * @var bool
		 */
		protected $based_effect_strict = false;



		public function __construct(){
			$this->addEvent([
				'beforeInvoked',
				'invoked',
				'invoked_obligation',
				'invoked_advice',
				'match',
				'match_contain_check',
				'match_contain_check_stop',
				'enforced'
			]);

		}

		/**
		 * @param ContextAdapter $adapter
		 * @return $this
		 */
		public function setContextAdapter(ContextAdapter $adapter){
			$this->context_adapter = $adapter;
			return $this;
		}

		/**
		 * @return ContextAdapter
		 * @throws Exception
		 */
		public function getContextAdapter(){
			if(!$this->context_adapter){
				$this->context_adapter = new ContextAdapter\Base();
				//throw new Exception('Context adapter is not set to ABAC::Pool');
			}
			return $this->context_adapter;
		}

		/**
		 * @param PolicyAdapter $adapter
		 * @return $this
		 */
		public function setPolicyAdapter(PolicyAdapter $adapter){
			$this->policy_adapter = $adapter;
			return $this;
		}

		/**
		 * @return PolicyAdapter
		 * @throws Exception
		 */
		public function getPolicyAdapter(){
			if(!$this->policy_adapter){
				throw new Exception('Policy adapter is not set to ABAC::SignManager');
			}
			return $this->policy_adapter;
		}


		/**
		 * @param $combiner_key
		 * @return Combiner
		 * @throws Exception
		 */
		public function requireCombiner($combiner_key = null){
			if($combiner_key === null){
				return $this->requireDefaultCombiner();
			}
			if($combiner_key instanceof Combiner){
				return $combiner_key;
			}
			if(isset($this->combiners[$combiner_key])){
				return $this->combiners[$combiner_key];
			}else{
				if(($combiner = Combiner::get($combiner_key))){
					return $combiner;
				}else{
					throw new Exception('Combiner with key "'.$combiner_key.'" not found');
				}
			}
		}

		/**
		 * @return Combiner
		 * @throws Exception
		 */
		public function requireDefaultCombiner(){
			if(!$this->default_combiner){
				$this->default_combiner = Combiner::get('effect_same_soft');
			}
			return $this->default_combiner;
		}

		/**
		 * @return Combiner
		 * @throws Exception
		 */
		public function requireMainCombiner(){
			if($this->main_combiner){
				return $this->main_combiner;
			}else{
				return $this->requireDefaultCombiner();
			}
		}

		/**
		 * @return ConditionResolver
		 */
		public function requireConditionResolver(){
			if(!$this->condition_resolver){
				$this->condition_resolver = new ConditionResolver();
			}
			return $this->condition_resolver;
		}

		/**
		 * @return ExpressionResolver
		 */
		public function requireExpressionResolver(){
			if(!$this->expression_resolver){
				$this->expression_resolver = new ExpressionResolver();
			}
			return $this->expression_resolver;
		}


		/**
		 * @param Combiner $algorithm
		 * @return $this
		 */
		public function setDefaultCombiner(Combiner $algorithm){
			$this->default_combiner = $algorithm;
			return $this;
		}

		public function setMainCombiner(Combiner $algorithm){
			$this->main_combiner = $algorithm;
			return $this;
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
		 * @param ExpressionResolver $resolver
		 * @return $this
		 */
		public function setExpressionResolver(ExpressionResolver $resolver){
			$this->expression_resolver = $resolver;
			return $this;
		}


		/**
		 * @param $effect
		 * @param null $strict
		 * @return $this
		 */
		public function setBasedEffect($effect, $strict = null){
			if($strict!==null){
				$this->based_effect_strict = boolval($strict);
			}
			$this->based_effect = $effect;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getBasedEffect(){
			return $this->based_effect;
		}


		/**
		 * Метод для вычисления изходя из текущих настроек контекста.
		 * @param $action
		 * @param null|string|array|object $object Объект над которым производится действие, если $useObjectPredicates===true то должна использовать строка имени класса объекта
		 * @param bool $useObjectPredicates TODO implement
		 * @return bool|array(collected predicates) TODO Predicates!!! если Объект передается до получения конкретного, то требуется генерировать Where compatible предикат
		 * TODO Predicates!!! если Объект передается до получения конкретного, то требуется генерировать Where compatible предикат
		 * @throws Exception
		 */
		public function enforce($action, $object, $useObjectPredicates = false){
			$context = $this->contextFrom($action,$object);
			$result = $this->contextCheck($context);
			return $result===Policy::PERMIT?true:false;
		}

		/**
		 * @param $action
		 * @param $object
		 * @param null $otherUser
		 * @param null $otherScope
		 * @return Context
		 */
		public function contextFrom($action, $object, $otherUser = null, $otherScope = null){

			if(is_string($object)){
				if(($object = trim($object)) && String::isCovered($object,'[',']')){
					$object = String::trimSides($object,'[',']');
					$object = explode(',',$object);
					if($object){
						$object = Massive::universalMap(function(& $value, & $key){
							list($key,$value) = explode(':',$value);
							$key = trim($key);
							$value = trim($value);
						},$object,true);
					}
					$object = ObjectAccessor::release($object);
				}else{
					throw new \LogicException('Object definition is invalid! "'.$object.'"');
				}
			}

			$context = $this->getContextAdapter()->getBaseContext([
				'action' => is_string($action)?['name' => $action]:$action,
				'object' => $object
			],$otherUser,$otherScope);
			$context->setManager($this);
			return $context;
		}

		/**
		 * @param Context $context
		 * @return bool
		 */
		public function contextCheck(Context $context){
			$combiner = $this->decise($context);
			$effect = $combiner->getEffect();
			$results = $combiner->getResults();
			if(in_array($effect,[Rule::DENY,Rule::PERMIT],true)){
				try{
					$results = $combiner->getResults();
					foreach($results as $result){
						$this->invoke($effect,$result,$context);
					}
				}catch(\Exception $e){
					$effect = Rule::INDETERMINATE;
				}
			}
			if(in_array($effect,[Rule::NOT_APPLICABLE,Rule::INDETERMINATE],true)){
				$effect = $this->getBasedEffect();
				if($this->based_effect_strict){
					$effect = !$effect;
				}
			}
			$this->invokeEvent('enforced',$this, $effect, $results, $context);
			return $effect;
		}




		/**
		 * @param Context $context
		 * @return Combiner
		 */
		public function decise(Context $context){
			$combiner = $this->requireMainCombiner()->begin($this->getBasedEffect());
			foreach($this->getPolicyAdapter()->getPolicies() as $policy){
				$r = $policy->match($context);
				if($combiner->check($r)===false){
					$r->setStopped(true);
					break;
				}
			}
			return $combiner;
		}

		/**
		 * @param $effect
		 * @param MatchResult $result
		 * @param Context $context
		 * @throws \Exception
		 */
		protected function invoke($effect,MatchResult $result,Context $context){
			$m = $result->getMatchable();
			if($m instanceof Policy){
				foreach($result->getChildren() as $child){
					$this->invoke($effect,$child,$context);
				}
			}
			$e = $m->getEffect();
			if($e === null){
				$e = $this->getBasedEffect();
			}
			if($e === $effect && $result->getResult() === $e){
				$obligation = $m->getObligation();
				if($obligation){
					try{
						if(call_user_func($obligation,$result,$context)===false){
							throw new Exception('internal error.');
						}
					}catch(\Exception $e){
						$result->setIndeterminate('ObligationError: '.$e->getMessage(),true);
						throw $e;
					}
				}
				$advice = $m->getAdvice();
				if($advice){
					try{
						call_user_func($advice,$result,$context);
					}catch(\Exception $e){}
				}
			}
		}


		/**
		 * @param array $listeners
		 * @param \Jungle\User\AccessControl\Matchable $policy
		 * @param bool $internal
		 */
		public function propagateListeners(array $listeners, \Jungle\User\AccessControl\Matchable $policy = null,$internal = false){
			if($listeners){
				if(!$internal && $this->_listeners_propagation){
					$this->stopPropagateListeners();
				}
				if($policy!==null){
					if($policy instanceof Policy){
						foreach($policy->getContains() as $p){
							$this->propagateListeners($listeners,$p);
						}
					}
					foreach($listeners as $eName => $listener){
						$policy->addListener($eName,$listener);
					}
				}else{
					foreach($this->policies as $p){
						$this->propagateListeners($listeners,$p);
					}
					if(!$internal){
						$this->_listeners_propagation = $listeners;
					}
				}
			}
		}




		/**
		 * @param \Jungle\User\AccessControl\Matchable|null $policy
		 */
		protected function delegateEvents(\Jungle\User\AccessControl\Matchable $policy = null){
			if($policy!==null){
				if($policy instanceof Policy){
					foreach($policy->getContains() as $p){
						$this->delegateEvents($p);
					}
				}
				$policy->addListener($this);
			}else{
				foreach($this->getPolicyAdapter()->getPolicies() as $p){
					$this->delegateEvents($p);
				}
			}
		}

		/**
		 * @param array $listeners
		 * @param \Jungle\User\AccessControl\Matchable|null $policy
		 */
		public function stopPropagateListeners(array $listeners = null, \Jungle\User\AccessControl\Matchable $policy = null){
			if($policy!==null){
				if($policy instanceof Policy){
					foreach($policy->getContains() as $p){
						$this->stopPropagateListeners($listeners,$p);
					}
				}
				foreach(($listeners===null?$this->_listeners_propagation:$listeners) as $eName => $listener){
					$policy->removeListener($eName,$listener);
				}
			}else{
				foreach($this->policies as $p){
					$this->stopPropagateListeners($listeners,$p);
				}
				if($listeners===null){
					$this->_listeners_propagation = null;
				}
			}
		}




	}
}

