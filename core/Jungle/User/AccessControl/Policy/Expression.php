<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 14.02.2016
 * Time: 19:04
 */
namespace Jungle\User\AccessControl\Policy {

	use Jungle\User\AccessControl\Context;

	/**
	 * Class Expression
	 * @package Jungle\User\AccessControl
	 */
	class Expression{


		protected $fn;


		/**
		 * @param callable $function
		 */
		public function __construct(callable $function){
			$this->fn = $function;
		}

		/**
		 * @param $result
		 * @param Context $context
		 * @param Matchable|Rule|PolicyElement|PolicyGroup $policy
		 * @return mixed|void
		 */
		public function call($result, Context $context, Matchable $policy){
			if($this->fn){
				return call_user_func($this->fn,$result,$context,$policy);
			}
			return null;
		}

		/**
		 * @param $result
		 * @param Context $context
		 * @param Matchable $policy
		 * @return mixed
		 */
		public function __invoke($result, Context $context, Matchable $policy){
			return $this->call($result,$context,$policy);
		}

	}
}

