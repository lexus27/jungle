<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 26.09.2016
 * Time: 20:14
 */
namespace Jungle\User\AccessControl {

	use Jungle\User\AccessControl\Adapter\ContextAdapter;
	use Jungle\User\AccessControl\Adapter\PolicyAdapter;
	use Jungle\User\AccessControl\Matchable\Combiner;
	use Jungle\User\AccessControl\Matchable\ExpressionResolver;
	use Jungle\User\AccessControl\Matchable\Resolver\ConditionResolver;

	/**
	 * Interface ManagerInterface
	 * @package Jungle\User\AccessControl
	 */
	interface ManagerInterface{

		/**
		 * @param ContextAdapter $adapter
		 * @return $this
		 */
		public function setContextAdapter(ContextAdapter $adapter);

		/**
		 * @return ContextAdapter
		 * @throws Exception
		 */
		public function getContextAdapter();

		/**
		 * @param PolicyAdapter $adapter
		 * @return $this
		 */
		public function setPolicyAdapter(PolicyAdapter $adapter);

		/**
		 * @return PolicyAdapter
		 * @throws Exception
		 */
		public function getPolicyAdapter();


		/**
		 * @param $combiner_key
		 * @return Combiner
		 * @throws Exception
		 */
		public function requireCombiner($combiner_key = null);

		/**
		 * @return Combiner
		 * @throws Exception
		 */
		public function requireDefaultCombiner();

		/**
		 * @return Combiner
		 * @throws Exception
		 */
		public function requireMainCombiner();

		/**
		 * @return ConditionResolver
		 */
		public function requireConditionResolver();

		/**
		 * @return ExpressionResolver
		 */
		public function requireExpressionResolver();


		/**
		 * @param Combiner $algorithm
		 * @return $this
		 */
		public function setDefaultCombiner(Combiner $algorithm);

		/**
		 * @param Combiner $algorithm
		 * @return mixed
		 */
		public function setMainCombiner(Combiner $algorithm);

		/**
		 * @param \Jungle\User\AccessControl\Matchable\Resolver\ConditionResolver $resolver
		 * @return $this
		 */
		public function setConditionResolver(ConditionResolver $resolver);

		/**
		 * @param ExpressionResolver $resolver
		 * @return $this
		 */
		public function setExpressionResolver(ExpressionResolver $resolver);


		/**
		 * @param $effect
		 * @param null $strict
		 * @return $this
		 */
		public function setBasedEffect($effect, $strict = null);

		/**
		 * @return mixed
		 */
		public function getBasedEffect();


		/**
		 * Метод для вычисления иcходя из текущих настроек контекста.
		 * @param $action
		 * @param null|string|array|object $object Объект над которым производится действие, если $useObjectPredicates===true то должна использовать строка имени класса объекта
		 * @param bool $useObjectPredicates
		 * @return bool|array(collected predicates)
		 * @throws Exception
		 */
		public function enforce($action, $object, $useObjectPredicates = false);

		/**
		 * @param $action
		 * @param $object
		 * @param null $otherUser
		 * @param null $otherScope
		 * @return Context
		 */
		public function contextFrom($action, $object, $otherUser = null, $otherScope = null);

		/**
		 * @param Context $context
		 * @return bool
		 */
		public function contextCheck(Context $context);

		/**
		 * @param Context $context
		 * @return Combiner
		 */
		public function decise(Context $context);

	}
}

