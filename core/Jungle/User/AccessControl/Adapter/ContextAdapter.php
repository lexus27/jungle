<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 19.02.2016
 * Time: 1:47
 */
namespace Jungle\User\AccessControl\Adapter {

	use Jungle\User\AccessControl\Context;

	/**
	 * Class ContextAdapter
	 * @package Jungle\User\AccessControl\Adapter
	 */
	abstract class ContextAdapter{

		/** @var  mixed */
		protected $user;

		/** @var array  */
		protected $addition_variables = [];

		/**
		 * @param array $merge
		 * @param null|mixed $other_user
		 * @param null $other_scope
		 * @return Context
		 */
		public function getBaseContext(array $merge = [], $other_user = null, $other_scope = null){
			$environment = $this->prepareEnvironment($merge);
			if($other_user!==null)$environment['user']      = $other_user;
			if($other_scope!==null)$environment['scope']    = $other_scope;
			return $this->createContext($environment);
		}

		/**
		 * @param array $environment
		 * @return Context
		 */
		protected function createContext(array $environment = []){
			return new Context($environment);
		}

		/**
		 * @param array $merge_variables
		 * @return Context
		 */
		protected function prepareEnvironment(array $merge_variables = []){
			$definition = [
				'scope' => $this->getScope(),
				'user'  => $this->getUser(),
			];
			foreach(array_merge((array)$this->getVariables(),$this->addition_variables) as $key => $variable){
				$key = strtolower($key);
				if(!isset($definition[$key])){
					$definition[$key] = $variable;
				}
			}
			return array_merge_recursive(
				$definition,
				array_change_key_case($merge_variables,CASE_LOWER)
			);
		}

		/**
		 * @return array
		 */
		protected function getScope(){
			return [
				'time'      => [
					'hour'      => date('H'),
					'minutes'   => date('i'),

					'week_day'          => date('l'),
					'week_day_number'   => date('n'),
					'meridiem'          => date('a'),
					'moth'              => date('F'),
					'moth_number'       => date('n'),
					'year'      => intval(date('Y')),
					'time'      => time()
				],
			];
		}

		/**
		 * @return null
		 */
		protected function getUser(){
			return $this->user;
		}

		/**
		 * @param $user
		 * @return $this
		 */
		public function setUser($user){
			$this->user = $user;
			return $this;
		}

		/**
		 * @param array $variables
		 * @return $this
		 */
		public function setAdditionVariables(array $variables){
			$this->addition_variables = $variables;
			return $this;
		}

		/**
		 * @return array
		 */
		protected function getVariables(){
			return [

				'TIME' => [
					'WORK_DAYS'     => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
					'WEEK_END_DAYS' => ['Friday', 'Saturday'],
					'SEASONS'       => [
						'FIRST_HALF'    => in_array(date('n'),[1,2,3,4,5,6]),
						'SECOND_HALF'   => in_array(date('n'),[7,8,9,10,11,12]),

						'WINTER' => ['December','January', 'February'],
						'SPRING' => ['March', 'April' , 'May'],
						'SUMMER' => ['June' , 'July', 'August'],
						'AUTUMN' => ['September', 'October', 'November']
					]
				]


			];
		}


	}
}

