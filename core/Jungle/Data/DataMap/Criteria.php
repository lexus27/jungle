<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 10.03.2016
 * Time: 18:58
 */
namespace Jungle\Data\DataMap {

	use Jungle\Data\DataMap\Criteria\CriteriaBlock;
	use Jungle\Data\DataMap\Criteria\CriteriaInterface;
	use Jungle\Data\DataMap\ValueAccess\Getter;
	use Jungle\Data\DataMap\ValueAccess\ValueAccessAwareInterface;

	/**
	 * Class Criteria
	 */
	class Criteria implements CriteriaInterface{

		const OP_OR = 'OR';

		const OP_AND = 'AND';


		/** @var  string */
		protected $field;

		/** @var  string|callable */
		protected $operator;

		/** @var   */
		protected $wanted_value;

		/**
		 * @param $item
		 * @param ValueAccessAwareInterface|callable|Getter $access
		 * @return mixed
		 */
		public function __invoke($item, $access){
			if($access instanceof ValueAccessAwareInterface){
				$value = $access->valueAccessGet($item,$this->field);
			}elseif(($getter = ValueAccess::checkoutGetter($access))){
				$value = call_user_func($getter,$item,$this->field);
			}else{
				throw new \InvalidArgumentException('Passed Access not qualified!');
			}
			$condition = \Jungle\CodeForm\LogicConstruction\Condition::getCondition($value,$this->operator,$this->wanted_value);
			$execute =  $condition->execute();
			return $execute;
		}

		public function __construct($field,$operator,$wanted){
			$this->field        = $field;
			$this->operator     = $operator;
			$this->wanted_value = is_numeric($wanted)?floatval($wanted):$wanted;
		}

		/**
		 * @param $definition
		 * @return CriteriaBlock
		 */
		public static function build($definition){

			if($definition instanceof CriteriaInterface){
				return $definition;
			}

			if(is_array($definition)){
				$criteria = new CriteriaBlock();
				foreach($definition as $key => $value){

					if(is_string($key) && is_string($value)){
						$key = trim($key,"\r\n\t\0\x0B:");

						list($key,$operator) = explode(':',$key);
						if(!$operator)$operator = '=';

						$wanted = trim($value,"\r\n\t\0\x0B:");

						list($wanted, $conditionDelimiter) = explode(':',$wanted);

						if(!$conditionDelimiter){
							$conditionDelimiter = 'AND';
						}

						$criteria->addCondition(new Criteria($key,$operator,$wanted));
						$criteria->addOperator($conditionDelimiter);
					}elseif(is_string($key) && is_array($value)){
						$key = trim($key,"\r\n\t\0\x0B:");
						list($key,$operator,$wanted,$conditionDelimiter) = explode(':',$key);
						if(!$operator)$operator = '=';
						if(!$conditionDelimiter){
							$conditionDelimiter = 'AND';
						}
						$criteria->addCondition(new Criteria($key,$operator,$wanted));
						$criteria->addOperator($conditionDelimiter);
						$criteria->addCondition(self::build($value));
					}elseif(is_int($key) && is_string($value)){
						$wanted = trim($value,"\r\n\t\0\x0B ");
						list($key, $operator, $wanted, $conditionDelimiter) = preg_split('@\s+@',$wanted);
						if(!$operator) $operator = '=';
						if(!$conditionDelimiter) $conditionDelimiter = 'AND';
						$criteria->addCondition(new Criteria($key,$operator,$wanted));
						$criteria->addOperator($conditionDelimiter);
					}else{
						throw new \LogicException('Criteria definition is invalid');
					}
				}
				return $criteria->complete();
			}elseif(is_string($definition)){
				$criteria = new CriteriaBlock();
				$definition = trim($definition,"()\r\n\t\0\x0B");
				$regex = '@ (!)?(
					(\((?:(?>[\(\)]+) | (?R))\)) |
					(\w+[\.\$\&\:\-\w\d]+\(.*?\)) |
					\s*? ([\+\-<>\*\/\\\$\#\@\%\^\&\:\=\!]+) \s*? |
					\s+? ([\+\-<>\*\/\\\$\#\@\%\^\&\:\=\!\w]+) \s+ |
					([\*\.<>\w\$\-\&\:\d]+) |
					TRUE | FALSE | NULL | AND | OR
					)
				@sxmiu';

				if(preg_match_all($regex,$definition,$m)){
					$lastCriteria = false;
					while( ($c = array_shift($m[0])) ){
						$c = trim($c);
						if(!$lastCriteria){
							if(substr($c,0,1)==='('){
								try{
									$criteria = self::build($c);
								}catch (\Exception $e){
									throw new \LogicException('Error on block parsing from definition: '.$c. ' message: '.$e->getMessage());
								}
							}else{
								if(!($operator = array_shift($m[0]))){
									throw new \LogicException('Operator not found!');
								}
								$operator = trim($operator);
								if(!($wanted = array_shift($m[0]))){
									throw new \LogicException('Wanted value not found!');
								}
								$wanted = trim($wanted);
								$criteria->addCondition(new Criteria($c,$operator,$wanted));
							}
							$lastCriteria = true;
						}else{
							$lastCriteria = false;
							$criteria->addOperator($c);
						}
					}
				}
				return $criteria->complete();
			}elseif(!$definition){
				return null;
			}else{

				throw new \LogicException('Invalid definition');

			}

		}

	}
}

