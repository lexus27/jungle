<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 18:58
 */
namespace Jungle\Data\DataMap\Criteria {

	use Jungle\Data\DataMap\Criteria;
	use Jungle\Data\DataMap\ValueAccess\Getter;
	use Jungle\Data\DataMap\ValueAccess\ValueAccessAwareInterface;

	/**
	 * Class CriteriaBlock
	 */
	class CriteriaBlock implements CriteriaInterface{

		/**
		 * @var CriteriaInterface[]
		 */
		protected $conditions = [];


		/**
		 * @param CriteriaInterface $condition
		 */
		public function addCondition(CriteriaInterface $condition){
			$this->conditions[] = $condition;
		}

		/**
		 * @param $operator
		 */
		public function addOperator($operator){
			$operator = strtoupper($operator);
			if(!in_array($operator,['AND','OR'],true)){
				throw new \LogicException('IS not valid operator '.$operator);
			}
			$this->conditions[] = $operator;
		}

		/**
		 * @param $item
		 * @param ValueAccessAwareInterface|Getter|callable $access
		 * @return bool
		 */
		public function __invoke($item, $access){
			$conditions = $this->conditions;
			while(($condition = array_shift($conditions))){
				if($condition instanceof CriteriaInterface){
					if(isset($value)){
						if(isset($operator) && $operator){
							switch($operator){
								case Criteria::OP_AND:
									$value = $value && call_user_func($condition,$item,$access);
									break;
								case Criteria::OP_OR:
									$value = $value || call_user_func($condition,$item,$access);
									break;
							}
						}else{
							return $value;
						}
					}else{
						$value      = call_user_func($condition,$item,$access);
						$operator   = array_shift($conditions);
					}
				}
			}

			if(isset($value)){
				return $value;
			}else{
				return true;
			}
		}

		/**
		 * @return $this
		 */
		public function complete(){
			$count = count($this->conditions);
			if($count){
				$lastIndex = $count-1;
				$hasClears = false;
				if(is_string($this->conditions[0])){
					unset($this->conditions[0]);
					$hasClears = true;
				}
				if(is_string($this->conditions[$lastIndex])){
					unset($this->conditions[$lastIndex]);
					$hasClears = true;
				}
				if($hasClears){
					ksort($this->conditions);
				}
			}
			return $this;
		}

	}

}

