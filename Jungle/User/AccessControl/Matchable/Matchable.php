<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 15.02.2016
 * Time: 0:03
 */
namespace Jungle\User\AccessControl\Matchable {

	use Jungle\User\AccessControl\Context\ContextInterface;
	use Jungle\User\AccessControl\Matchable\Aggregator;

	/**
	 * Class Matchable
	 * @package Jungle\User\AccessControl\Matchable\Matchable
	 *
	 * beforeInvoked(Matchable $this, $result, Context $context)
	 * invoked(Matchable $this, $result, Context $context)
	 * invoked_obligation(Matchable $this, $result, Context $context)
	 * invoked_advice(Matchable $this, $result, Context $context)
	 *
	 * match(Matchable $this, $result, Context $context,$preSolve = false)
	 * match_contain_check(Matchable $this, $result,Matchable $contain, Context $context,$isStopped)
	 * match_contain_check_stop(Matchable $this, $result,Matchable $contain, Context $context)
	 *
	 *
	 */
	abstract class Matchable implements MatchableInterface{


		/** Разрешено */
		const PERMIT                = 'permit';

		/** Запрещено */
		const DENY                  = 'deny';

		/** Не определен */
		const INDETERMINATE         = 'indeterminate';

		/** Не применим */
		const NOT_APPLICABLE        = 'not_applicable';



		/** @var  string */
		protected $name;

		/** @var  Aggregator */
		protected $parent;

		/** @var  Target|null */
		protected $target;

		/** @var  callable|null */
		protected $obligation;

		/** @var  callable|null */
		protected $advice;

		/** @var  callable|null */
		protected $requirement;

		/** @var bool */
		protected $effect = null;

		/**
		 * @param Aggregator|null $parent
		 * @param bool|false $appliedIn
		 * @param bool|false $appliedOld
		 * @return $this
		 */
		public function setParent(Aggregator $parent = null, $appliedIn = false, $appliedOld = false){
			$old = $this->parent;
			if($old !== $parent){
				$this->parent = $parent;
				if(!$appliedIn && $parent){
					$parent->addChild($this, true);
				}
				if(!$appliedOld && $old){
					$old->removeChild($this);
				}
			}
			return $this;
		}

		/**
		 * @return Aggregator
		 */
		public function getParent(){
			return $this->parent;
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}


		/**
		 * @param string $effect
		 * @return $this
		 */
		public function setEffect($effect){
			$this->effect = $effect;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function getEffect(){
			if($this->parent && $this->effect === null){
				return $this->parent->getEffect();
			}
			return $this->effect;
		}

		/**
		 * @param Target|null $target
		 * @return $this
		 */
		public function setTarget(Target $target = null){
			$this->target = $target;
			return $this;
		}

		/**
		 * @return Target|null
		 */
		public function getTarget(){
			return $this->target;
		}


		/**
		 * @param callable|null $obligation
		 * @return $this
		 */
		public function setObligation(callable $obligation = null){
			$this->obligation = $obligation;
			return $this;
		}

		/**
		 * @return callable|null
		 */
		public function getObligation(){
			return $this->obligation;
		}




		/**
		 * @param callable|null $advice
		 * @return $this
		 */
		public function setAdvice(callable $advice = null){
			$this->advice = $advice;
			return $this;
		}

		/**
		 * @return callable|null
		 */
		public function getAdvice(){
			return $this->advice;
		}

		/**
		 * @param callable|null $requirement
		 * @return $this
		 */
		public function setRequirement(callable $requirement = null){
			$this->requirement = $requirement;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getRequirement(){
			return $this->requirement;
		}

		/**
		 * @param \Jungle\User\AccessControl\Context\ContextInterface $context
		 * @param Aggregator $aggregator
		 * @return Result
		 */
		abstract public function match(ContextInterface $context, Aggregator $aggregator);



		/**
		 * @param $effect
		 * @return string
		 */
		public static function friendlyEffect($effect){
			if($effect === true || $effect === 1){
				return self::PERMIT;
			}elseif($effect === false || $effect === 0){
				return self::DENY;
			}
			return $effect;
		}
	}
}

