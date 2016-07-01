<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.05.2016
 * Time: 20:27
 */
namespace Jungle\Util\Data\Foundation\Condition {

	use Jungle\Util\Data\Foundation\Record\Properties\PropertyRegistryInterface;
	use Jungle\Util\Data\Foundation\Schema\OuterInteraction\ValueAccessAwareInterface;
	use Jungle\Util\Data\Foundation\Schema\OuterInteraction\ValueAccessor;

	/**
	 * Class Condition
	 * @package Jungle\Util\Data\Foundation\Condition
	 */
	class Condition implements ConditionTargetInterface{

		/** @var  string */
		protected $field;

		/** @var  string */
		protected $operator;

		/** @var  mixed */
		protected $wanted;

		/**
		 * @param PropertyRegistryInterface|mixed $data
		 * @param null|ValueAccessAwareInterface|callable $access - if data map is outer original data
		 * @return mixed
		 */
		public function __invoke($data, $access = null){
			return \Jungle\CodeForm\LogicConstruction\Condition::collateRaw(
				ValueAccessor::handleAccessGet($access, $data, $this->field),
				$this->operator,
				$this->wanted, Operator::class
			);
		}

		/**
		 * @param string $name
		 * @return $this
		 */
		public function setField($name){
			$this->field = $name;
			return $this;
		}

		/**
		 * @param string $operator_definition
		 * @return $this
		 */
		public function setOperator($operator_definition){
			$this->operator = $operator_definition;
			return $this;
		}

		/**
		 * @param mixed $wanted
		 * @return $this
		 */
		public function setWanted($wanted){
			$this->wanted = $wanted;
			return $this;
		}

		/**
		 * @return array
		 */
		public function toStorageCondition(){
			return [$this->field,$this->operator,$this->wanted];
		}

		/**
		 * @param $condition
		 * @return ConditionComplex|null
		 */
		public static function build($condition){
			if($condition instanceof ConditionInterface){
				return $condition;
			}
			if(is_array($condition) && $condition){
				$complex = new ConditionComplex();
				$delimiterOperator = null;
				foreach($condition as $key => $c){
					$s = is_string($key);
					$count = count($c);
					$block = false;
					if(!$s){
						$block = true;
						if(!is_array($c)){
							$block = false;
						}else{
							foreach($c as $i){
								if(!is_array($i)){
									$block = false;
								}
							}
						}
					}
					if($block){
						$a = self::build($c);
						if($a){
							$complex->addCondition($a, $delimiterOperator);
							$delimiterOperator = null;
						}
					}elseif($s){
						$operator = null;
						if(strpos($key,':')!==false){
							list($key,$operator) = array_replace([null,$operator],explode(':',$key,2));
						}
						if(!$operator){
							$operator = '=';
						}
						$left = $key;
						$right = $c;

						$target = new Condition();
						$target->setField($left);
						$target->setOperator($operator);
						$target->setWanted($right);

						$complex->addCondition($target, $delimiterOperator);
						$delimiterOperator = null;
					}elseif($count === 3 || $count === 2){
						$left = isset($c[0])?$c[0]:$c['left'];
						$operator = isset($c[1])?$c[1]:$c['operator'];
						$right = isset($c[2])?$c[2]:$c['right'];

						$target = new Condition();
						$target->setField($left);
						$target->setOperator($operator);
						$target->setWanted($right);

						$complex->addCondition($target, $delimiterOperator);
						$delimiterOperator = null;
					}elseif($count === 1){
						$delimiterOperator = $c[0];
					}
				}
				return $complex;
			}
			return null;
		}

	}
}

