<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.10.2016
 * Time: 20:17
 */
namespace Jungle\User\AccessControl\Matchable\Resolver {

	use Jungle\User\AccessControl\Context\ContextInterface;
	use Jungle\User\AccessControl\Matchable\Resolver;
	use Jungle\User\AccessControl\Matchable\Result;

	/**
	 * Class ExpressionResolver
	 * @package Jungle\User\AccessControl\Matchable\Matchable\Resolver
	 */
	class ExpressionResolver extends Resolver{


		/**
		 * @param \Jungle\User\AccessControl\Context\ContextInterface $context
		 * @param Result $result
		 * @param $expression
		 * @return mixed
		 * @throws \Exception
		 */
		public function resolve(ContextInterface $context, Result $result, $expression){
			if(is_callable($expression)){
				return call_user_func($expression,$result,$context);
			}else{
				throw new \Exception('Expression is not valid, must be callable');
			}

		}
	}
}

