<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 15.02.2016
 * Time: 0:03
 */
namespace Jungle\User\AccessControl {

	use Jungle\User\AccessControl\Context;
	use Jungle\User\AccessControl\Policy\Expression;
	use Jungle\User\AccessControl\Policy\MatchResult;
	use Jungle\User\AccessControl\Policy\Target;
	use Jungle\Util\Observable;

	/**
	 * Class Matchable
	 * @package Jungle\User\AccessControl\Policy
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
	abstract class Matchable extends Observable{


		/** Разрешено */
		const PERMIT                = true;

		/** Запрещено */
		const DENY                  = false;

		/** Не определен */
		const INDETERMINATE         = 'indeterminate';

		/** Не применим */
		const NOT_APPLICABLE        = 'not_applicable';


		/** @var  string */
		protected $name;

		/** @var  Target */
		protected $target;

		/** @var  Expression */
		protected $obligation;

		/** @var  Expression */
		protected $advice;

		/** @var bool */
		protected $effect = self::DENY;



		public function __construct(){
			$this->addEvent([
				'beforeInvoked',
				'invoked',
				'invoked_obligation',
				'invoked_advice',
				'match',
				'match_contain_check',
				'match_contain_check_stop',
			]);
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
		 * @param Target $target
		 * @return $this
		 */
		public function setTarget(Target $target){
			$this->target = $target;
			return $this;
		}

		/**
		 * @return Target
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
		 * @return Expression
		 */
		public function getObligation(){
			return $this->obligation;
		}




		/**
		 * @param callable $advice
		 * @return $this
		 */
		public function setAdvice(callable $advice){
			$this->advice = $advice;
			return $this;
		}

		/**
		 * @return Expression
		 */
		public function getAdvice(){
			return $this->advice;
		}

		/**
		 * @param Context $context
		 * @return MatchResult
		 */
		abstract public function match(Context $context);

	}
}

