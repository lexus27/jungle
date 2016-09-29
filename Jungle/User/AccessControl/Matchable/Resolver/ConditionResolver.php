<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 14.02.2016
 * Time: 21:50
 */
namespace Jungle\User\AccessControl\Matchable\Resolver {

	use Jungle\ExoCode\LogicConstruction\Condition;
	use Jungle\ExoCode\LogicConstruction\Operator;
	use Jungle\RegExp;
	use Jungle\User\AccessControl\Context;
	use Jungle\User\AccessControl\Matchable\Resolver;
	use Jungle\User\AccessControl\Matchable\Resolver\ConditionResolver\Exception as ResolveException;
	use Jungle\User\AccessControl\Matchable\Resolver\ConditionResolver\Exception\BadMethodCall;
	use Jungle\User\AccessControl\Matchable\Resolver\ConditionResolver\Exception\InvalidQuery;
	use Jungle\User\AccessControl\Matchable\Resolver\ConditionResolver\Exception\PathNotFound;
	use Jungle\User\AccessControl\Matchable\Result;
	use Jungle\Util\Value\Massive;
	use Jungle\Util\Value\String;

	/**
	 * Class Condition
	 * @package Jungle\User\AccessControl
	 *
	 *
	 * @Events
	 *
	 * condition_matched($definition, $result, $resolver)
	 * condition_error($definition, $result, $resolver)
	 *
	 * path_success($definition, $result, $resolver)
	 *      path_success_call($definition, $result, $resolver)
	 *      path_success_query($definition, $result, $resolver)
	 * path_error($exception, $resolver)
	 *      path_error_call($exception, $resolver)
	 *      path_error_not_exists($exception, $resolver)
	 *      path_error_invalid_definition($exception, $resolver)
	 *
	 * @TODO implement Observable containers, invokeEvent('path_success._call') >>> equal to
	 *     invokeEvent('path_success');invokeEvent('path_success_call')
	 * @TODO implement Observable effects ON|OFF
	 *
	 */
	class ConditionResolver extends Resolver{

		/** @var  Context */
		protected $current_context;

		/** @var string  */
		protected $path_delimiter   = '.';

		/** @var string  */
		protected $escape_left      = '[';

		/** @var string  */
		protected $escape_right     = ']';

		/** @var string  */
		protected $type_delimiter   = '::';

		/** @var  array  */
		protected $operators        = [];

		/** @var  string */
		protected $condition_regex;

		/** @var  Inspector  */
		protected $inspector;


		/**
		 * @param $inspector
		 * @return $this
		 */
		public function setInspector(Inspector $inspector = null){
			$this->inspector = $inspector;
			return $this;
		}

		/**
		 * @return Inspector
		 */
		public function getInspector(){
			return $this->inspector;
		}


		/**
		 * @param Operator $operator
		 * @return $this
		 */
		public function addOperator(Operator $operator){
			if(!in_array($operator,$this->operators,true)){
				$this->operators[] = $operator;
			}
			return $this;
		}

		/**
		 * @param $operatorKey
		 * @return Operator|null
		 */
		public function getOperator($operatorKey){
			$operator = Massive::getNamed($this->operators,$operatorKey);
			if(!$operator){
				$operator = Operator::getOperator($operatorKey);
			}
			return $operator;
		}

		/**
		 * @return array
		 */
		public function getAllowedOperators(){
			return array_merge($this->operators, Operator::getAllowedOperators());
		}



		/**
		 * @param $result
		 * @return mixed
		 */
		public function checkoutResult($result){
			if($this->inspector){
				return $this->inspector->checkoutResult($result);
			}else{
				return $result;
			}
		}

		/**
		 * @param Context $context
		 * @param Result $result
		 * @param $condition
		 * @return bool
		 * @throws ResolveException
		 */
		public function resolve(Context $context, Result $result, $condition){
			$this->current_context = $context;
			if($this->inspector){
				$this->inspector->beginInspect($context, $result, $condition);
			}

			$condition = $this->_parseCondition($condition);
			if($condition){
				list($left,$operator,$right) = $condition;
				if($this->inspector){
					$this->inspector->expressionParsed(['left'=>$left,'operator'=>$operator,'right'=>$right ]);
					$this->inspector->beforeProcess('left');
				}
				$left       = $this->_get($left);
				if($this->inspector){
					$this->inspector->beforeProcess('right');
				}
				$right      = $this->_get($right);

				if($this->inspector){
					$this->inspector->expressionProcessed(['left'=>$left,'operator'=>$operator,'right'=>$right] );
				}

				$condition  = Condition::getCondition($left,$operator,$right);
				$result     = $condition->execute();

				$result     = $this->checkoutResult($result);
			}else{
				throw new ResolveException();
			}
			return $result;
		}


		/**
		 * @param $chunk
		 * @param $depth
		 * @param $container
		 * @param $path
		 * @param $fullPath
		 * @param $definition
		 * @return null
		 */
		protected function _onNotFound($chunk, $depth, $container, $path, $fullPath, $definition){
			if($this->inspector){
				$this->inspector->onNotFound($chunk, $depth, $container, $path, $fullPath, $definition);
			}
			return null;
		}

		/**
		 * @param $value
		 * @param $chunk
		 * @param $depth
		 * @param $container
		 * @param $path
		 * @param $fullPath
		 * @param $definition
		 * @return null
		 */
		protected function _onFound($value, $chunk, $depth, $container, $path, $fullPath, $definition){
			if($this->inspector){
				return $this->inspector->onFound($value, $chunk, $depth, $container, $path, $fullPath, $definition);
			}
			return $value;
		}

		/**
		 * @param $query_definition
		 * @return string
		 * @throws BadMethodCall
		 * @throws InvalidQuery
		 * @throws PathNotFound
		 */
		protected function _get($query_definition){
			$query_definition = trim($query_definition);
			if(substr($query_definition,0,1)==='[' && substr($query_definition,-1)===']'){
				$query_definition = trim($query_definition,'[]');
				$result = $this->_parseSimpleValue($this->_query($query_definition));
				return $result;
			}else{
				return $this->_parseSimpleValue($query_definition);
			}
		}

		/**
		 * @param $path
		 * @param null $container
		 * @param null $depth
		 * @param null $fullPath
		 * @param null $definition
		 * @return null
		 */
		protected function _query($path, $container = null, $depth = null, $fullPath = null, $definition = null){
			if($depth === null){
				$definition     = $path;
			}
			if($container === null){
				$container = $this->current_context;
			}
			if(!is_array($path)){
				$path = explode($this->path_delimiter,$path);
			}
			if($depth === null){
				$fullPath       = $path;
				$depth = 0;
			}
			$chunk = array_shift($path);
			if(($infoQuery = strstr($chunk,$this->type_delimiter))!==false){
				$chunk          = strstr($chunk,$this->type_delimiter,true);
				$infoQuery      = String::trimWordsLeft($infoQuery,$this->type_delimiter);
			}else{
				$infoQuery = null;
			}
			if($container instanceof Context\SubstituteInterface && $container->isDefined()){
				$container = $container->getValue();
			}

			if(is_object($container)){
				if(isset($container->{$chunk})){
					$value = $container->{$chunk};
				}else{
					return $this->_onNotFound($chunk, $depth, $container, $path, $fullPath,$definition);
				}
			}elseif(is_array($container)){
				if(isset($container[$chunk])){
					$value = $container[$chunk];
				}else{
					return $this->_onNotFound($chunk, $depth, $container, $path, $fullPath,$definition);
				}
			}else{
				return null;
			}

			if($infoQuery!==null){
				$value = Context\Substitute::infoQuery($infoQuery, $value);
			}


			if(!$path){
				return $this->_onFound($value, $chunk, $depth, $container, $path, $fullPath, $definition);
			}else{
				return $this->_query($path, $value, $depth + 1, $fullPath, $definition);
			}
		}

		/**
		 * @param $value
		 * @return array|float|int
		 */
		protected function _parseSimpleValue($value){
			if(is_numeric($value)){
				return strpos($value,'.')!==false?floatval($value):intval($value);
			}elseif(is_string($value)){
				if(Massive::searchString(['TRUE','FALSE','NULL'], $value, true)!==false){
					return String::convertToActualType($value);
				}if(substr($value,0,1)==='[' && substr($value,-1)===']'){
					$value = explode(',',trim($value,'[]'));
					foreach($value as & $v){
						$v = $this->_parseSimpleValue($v);
					}
					return $value;
				}
			}
			return $value;
		}


		/**
		 * @return string
		 */
		protected function _getConditionRegex(){
			if(!$this->condition_regex){
				$delimiter      = preg_quote($this->path_delimiter,'@');
				$escape_left    = preg_quote($this->escape_left,'@');
				$escape_right   = preg_quote($this->escape_right,'@');
				$this->condition_regex = '@ (!)?(
					('.$escape_left.'(?:(?>[\[\]]+) | (?R))'.$escape_right.') |
					(\w+['.$delimiter.'\$\&\:\w\d]+\(.*?\)) |
					\s*? ([\+\-\*\/\\\$\#\@\%\^\&\:\=\!]+) \s*? |
					\s+? ([\+\-\*\/\\\$\#\@\%\^\&\:\=\!\w]+) \s+ |
					([\*'.$delimiter.'\w\$\&\:\d]+) |
					TRUE | FALSE | NULL
					)
				@sxmi';
			}
			return $this->condition_regex;
		}

		/**
		 * @param $condition_definition
		 * @return array|bool
		 */
		protected function _parseCondition($condition_definition){
			if(preg_match_all($this->_getConditionRegex(),$condition_definition,$m)){
				$c = count($m[0]);
				if($c===1){
					return [$m[2][0],$m[1][0]?false:true];
				}elseif($c===3){
					return [$m[0][0],trim($m[0][1]),$m[0][2]];
				}else{
					throw new \LogicException($condition_definition);
				}
			}
			return false;
		}


	}
}

