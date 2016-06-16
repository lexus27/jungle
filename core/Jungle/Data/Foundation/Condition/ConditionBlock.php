<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.05.2016
 * Time: 20:27
 */
namespace Jungle\Data\Foundation\Condition {

	use Jungle\Data\Foundation\Record\Properties\PropertyRegistryInterface;
	use Jungle\Data\Foundation\Schema\OuterInteraction\ValueAccessAwareInterface;

	/**
	 * Class ConditionBlock
	 * @package Jungle\Data\Foundation\Condition
	 */
	class ConditionBlock implements ConditionBlockInterface, ConditionInterface{

		/** @var  ConditionInterface[] */
		protected $conditions = [ ];

		/** @var  array */
		protected $operators = [ ];

		/**
		 * @param PropertyRegistryInterface|mixed $data
		 * @param null|ValueAccessAwareInterface|callable $access - if data map is outer original data
		 * @return mixed
		 */
		public function __invoke($data, $access = null){
			$operator = null;
			foreach($this->conditions as $i => $condition){
				if($i > 0){
					$operator = isset($this->operators[$i])?$this->operators[$i]:'and';
				}
				if($operator === 'and' && isset($value)){
					$value = $value && call_user_func($condition, $data, $access);
				}elseif($operator === 'or' && isset($value)){
					$value = $value || call_user_func($condition, $data, $access);
				}else{
					$value = call_user_func($condition, $data, $access);
				}
			}
			if(isset($value)){
				return $value;
			}else{
				return true;
			}
		}

		/**
		 * @param ConditionInterface $condition
		 * @param null $operator
		 * @return $this
		 */
		public function addCondition(ConditionInterface $condition, $operator = null){
			$count = count($this->conditions);
			$this->conditions[$count] = $condition;
			if(!$count){
				$this->operators[$count] = null;
			}elseif($operator!==false){
				$this->operators[$count] = ($operator?strtoupper($operator):'AND');
			}
			return $this;
		}

		/**
		 * @return array
		 */
		public function toStorageCondition(){
			$c = [];
			foreach($this->conditions as $i => $condition){
				if($i > 0){
					if(isset($this->operators[$i])){
						$c[] = [$this->operators[$i]];
					}else{
						$c[] = ['AND'];
					}
				}
				$c[] = $condition->toStorageCondition();
			}
			return $c;
		}

		/**
		 * @param string $delimiter
		 * @return $this
		 */
		public function addOperator($delimiter = 'AND'){
			// TODO: Implement addOperator() method.
		}
	}
}

