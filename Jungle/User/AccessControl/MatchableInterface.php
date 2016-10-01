<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 26.09.2016
 * Time: 20:17
 */
namespace Jungle\User\AccessControl {
	
	use Jungle\User\AccessControl\Matchable\Aggregator;
	use Jungle\User\AccessControl\Matchable\Result;
	use Jungle\User\AccessControl\Matchable\Target;
	use Jungle\Util\INamed;

	/**
	 * Interface MatchableInterface
	 * @package Jungle\User\AccessControl
	 */
	interface MatchableInterface extends INamed{

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name);

		/**
		 * @return string
		 */
		public function getName();



		/**
		 * @param bool $effect
		 * @return $this
		 */
		public function setEffect($effect);

		/**
		 * @return bool
		 */
		public function getEffect();

		/**
		 * @param Target $target
		 * @return $this
		 */
		public function setTarget(Target $target = null);

		/**
		 * @return Target
		 */
		public function getTarget();


		/**
		 * @param callable|null $obligation
		 * @return $this
		 */
		public function setObligation(callable $obligation = null);

		/**
		 * @return callable|null
		 */
		public function getObligation();




		/**
		 * @param callable $advice
		 * @return $this
		 */
		public function setAdvice(callable $advice = null);

		/**
		 * @return callable|null
		 */
		public function getAdvice();

		/**
		 * @param callable $requirements
		 * @return $this
		 */
		public function setRequirement(callable $requirements = null);

		/**
		 * @return callable|null
		 */
		public function getRequirement();

		/**
		 * @param ContextInterface $context
		 * @param Aggregator $aggregator
		 * @return Result
		 */
		public function match(ContextInterface $context, Aggregator $aggregator);

	}
}

