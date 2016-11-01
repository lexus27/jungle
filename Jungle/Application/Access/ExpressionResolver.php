<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.10.2016
 * Time: 20:56
 */
namespace Jungle\Application\Access {

	use Jungle\Di\Injectable;
	use Jungle\User\AccessControl\Context\ContextInterface;
	use Jungle\User\AccessControl\Matchable\Result;

	/**
	 * Class ExpressionResolver
	 * @package Jungle\Application\Access
	 */
	class ExpressionResolver extends \Jungle\User\AccessControl\Matchable\Resolver\ExpressionResolver{


		/**
		 * ExpressionResolver constructor.
		 * @param Injectable $injectable
		 */
		public function __construct(Injectable $injectable){
			$this->injectable = $injectable;
		}

		/**
		 * @param ContextInterface $context
		 * @param Result $result
		 * @param $expression
		 * @return mixed
		 * @throws \Exception
		 */
		public function resolve(ContextInterface $context, Result $result, $expression){
			if(is_callable($expression)){
				return call_user_func($expression,$this->injectable,$result,$context);
			}else{
				throw new \Exception('Expression is not valid, must be callable');
			}
		}


	}
}

