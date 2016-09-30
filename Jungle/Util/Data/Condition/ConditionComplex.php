<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.06.2016
 * Time: 0:04
 */
namespace Jungle\Util\Data\Condition {

	/**
	 * Class ConditionComplex
	 * @package Jungle\Util\Data\Condition
	 */
	class ConditionComplex extends ConditionBlock implements PredicatedConditionInterface{

		/** @var array */
		protected $predicates = [ ];

		/** @var */
		protected static $over_getter;

		/**
		 * @param array $collated_data
		 * @return array
		 * @throws \Exception
		 */
		public function setPredicatedData(array $collated_data){
			if(!$this->checkPredicates($collated_data)){
				throw new \LogicException('Predicates is not collated with current condition');
			}
			$this->predicates = $collated_data;
			return $this;
		}

		/**
		 * @param array $predicates
		 * @return mixed
		 */
		public function checkPredicates(array $predicates){
			if(!self::$over_getter){
				self::$over_getter = function ($data, $key){
					return $data[$key];
				};
			}
			return $this->__invoke($predicates, self::$over_getter);
		}

		/**
		 * @return array
		 */
		public function getPredicatedData(){
			$predicates = [];
			foreach($this->conditions as $condition){
				if($condition instanceof ConditionComplex){
					$predicates = array_replace($predicates,$condition->getPredicatedData());
				}
			}
			return array_replace($predicates,$this->predicates);
		}

	}
}

