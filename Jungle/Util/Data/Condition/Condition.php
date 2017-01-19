<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.05.2016
 * Time: 20:27
 */
namespace Jungle\Util\Data\Condition {

	use Jungle\Util\Data\Record\PropertyRegistryInterface;
	use Jungle\Util\Data\Schema\OuterInteraction\ValueAccessAwareInterface;
	use Jungle\Util\Data\Schema\OuterInteraction\ValueAccessor;

	/**
	 * Class Condition
	 * @package Jungle\Util\Data\Condition
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
		 * @return bool
		 */
		public function __invoke($data, $access = null){
			return \Jungle\ExoCode\LogicConstruction\Condition::collateRaw(
				ValueAccessor::handleAccessGet($access, $data, $this->field),
				Operator::getOperator($this->operator),
				$this->wanted
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
					if(is_array($c)){
						$count = count($c);
					}else{
						$count = 0;
					}

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


						//$left = isset($c[0])?$c[0]:$c['left'];
						//$operator = isset($c[1])?$c[1]:$c['operator'];
						//$right = isset($c[2])?$c[2]:$c['right'];
						// сдесь была раньше проблема если значения является NULL , при проверке на isset()

						list($left, $operator, $right) = self::toList($c,[0,'left'],[1,'operator'],[2,'right']);

						$target = new Condition();
						$target->setField($left);
						$target->setOperator($operator);
						$target->setWanted($right);

						$complex->addCondition($target, $delimiterOperator);
						$delimiterOperator = null;
					}elseif($count === 1){
						$delimiterOperator = $c[0];
					}else{
						$delimiterOperator = $c;
					}
				}
				return $complex;
			}
			return null;
		}

		/**
		 * @param array $a
		 * @param \array[] ...$keys
		 * @return array
		 *
		 * Выборка альтернативных ключей последовательно
		 *
		 */
		public static function toList(array $a, array ... $keys){
			$b = [];
			foreach($keys as $i => $k){
				foreach($k as $key){
					if(array_key_exists($key,$a)){
						$b[$i] = $a[$key];
						break;
					}
				}
			}
			return $b;
		}

	}
}

