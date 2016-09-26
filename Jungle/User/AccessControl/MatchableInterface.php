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
	
	use Jungle\User\AccessControl\Policy\Expression;
	use Jungle\User\AccessControl\Policy\MatchResult;
	use Jungle\User\AccessControl\Policy\Target;
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
		public function setTarget(Target $target);

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
		 * @return Expression
		 */
		public function getObligation();




		/**
		 * @param callable $advice
		 * @return $this
		 */
		public function setAdvice(callable $advice);

		/**
		 * @return Expression
		 */
		public function getAdvice();

		/**
		 * @param callable $requirements
		 * @return $this
		 */
		public function setRequirements(callable $requirements);

		/**
		 * @return mixed
		 */
		public function getRequirements();

		/**
		 * @param Context $context
		 * @return MatchResult
		 */
		public function match(Context $context);

	}
}

