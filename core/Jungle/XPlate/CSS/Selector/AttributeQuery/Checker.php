<?php
/**
 * Created by PhpStorm.
 * Project: localhost
 * Date: 11.05.2015
 * Time: 0:16
 */

namespace Jungle\XPlate\CSS\Selector\AttributeQuery {

	use Jungle\CodeForm\LogicConstruction\Condition;
	use Jungle\CodeForm\LogicConstruction\Operator;

	/**
	 * Class Checker
	 * @package Jungle\XPlate\CSS\Selector\AttributeQuery
	 *
	 * Класс чекер который должен проверять значения атрибутов по заданому сравниваемому значению
	 *
	 */
	class Checker implements IChecker{

		/**
		 * @var string
		 */
		protected $symbol;

		/**
		 * @var Condition
		 */
		protected $condition;

		/**
		 * @var callable
		 */
		protected $handler;


		/**
		 * @param callable $handler
		 * @return $this
		 */
		public function setHandler(callable $handler){
			if($this->handler !== $handler){
				if($this->condition){
					$this->condition->getOperator()->setHandler($handler);
				}
				$this->handler = $handler;
			}
			return $this;
		}

		/**
		 * @return callable
		 */
		protected function getHandler(){
			return $this->handler;
		}

		/**
		 * @return Condition
		 */
		protected function getCondition(){
			if(!$this->condition){
				$this->condition    = new Condition();
				$operator           = new Operator();
				$handler            = $this->getHandler();
				if($handler){
					$operator->setHandler($handler);
				}
				$this->condition->setOperator($operator);
			}
			return $this->condition;
		}


		/**
		 * @param mixed $value Поданное значение, существуемое
		 * @param mixed $collated Требуемое значение для сопоставления
		 * @return bool
		 */
		public function check($value, $collated){
			$condition = $this->getCondition();
			$condition->setValue($value);
			$condition->setSecondary($collated);
			return boolval($condition->execute());
		}

		/**
		 * @param $value
		 * @param $collated
		 * @return bool
		 *
		 * Check alias, callable object
		 */
		public function __invoke($value, $collated){
			return $this->check($value, $collated);
		}

		/**
		 * @return string
		 */
		public function getName(){
			return $this->symbol;
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			$this->symbol = $name;
			return $this;
		}

		/**
		 * @return string
		 */
		public function __toString(){
			return $this->getName();
		}


	}
}