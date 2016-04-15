<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 14.02.2016
 * Time: 21:50
 */
namespace Jungle\User\Access\ABAC\Policy {

	use Jungle\Basic\Observable;
	use Jungle\CodeForm\LogicConstruction\Condition;
	use Jungle\CodeForm\LogicConstruction\Operator;
	use Jungle\RegExp;
	use Jungle\User\Access\ABAC\Context;
	use Jungle\User\Access\ABAC\Exception;
	use Jungle\User\Access\ABAC\Policy\ConditionResolver\Exception as ResolveException;
	use Jungle\User\Access\ABAC\Policy\ConditionResolver\Exception\BadMethodCall;
	use Jungle\User\Access\ABAC\Policy\ConditionResolver\Exception\InvalidQuery;
	use Jungle\User\Access\ABAC\Policy\ConditionResolver\Exception\PathNotFound;
	use Jungle\User\Access\ABAC\Policy\ConditionResolver\Exception\Query;
	use Jungle\Util\Value\Massive;
	use Jungle\Util\Value\String;

	/**
	 * Class Condition
	 * @package Jungle\User\Access\ABAC
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
	class ConditionResolver extends Observable{

		/** @var  Context */
		protected $current_context;

		/** @var string  */
		protected $path_delimiter   = '.';

		/** @var string  */
		protected $escape_left      = '[';

		/** @var string  */
		protected $escape_right     = ']';

		protected $type_delimiter   = '::';

		protected $operators        = [];

		/** @var  string */
		protected $condition_regex;

		/** @var  string */
		protected $function_regex;

		/**
		 * For debugging or trace calls|getter @see setCollectPaths
		 * @var bool|array
		 */
		protected $trace_on             = false;

		/** @var mixed[query_definition]        */
		protected $success_trace        = [];
		/** @var Query[query_definition]|Query[]*/
		protected $failure_trace        = [];
        /** @var bool[condition_definition]     */
		protected $condition_trace      = [];

		/** @var null  */
		protected $default_allow_value  = null;
		/** @var bool  */
		protected $allow_not_found      = true;
		/** @var bool  */
		protected $allow_bad_method     = true;
		/** @var bool  */
		protected $allow_invalid_query  = true;


		public function __construct(){
			$this->addEvent([
				'path_success',
				'path_success_call',
				'path_success_query',
				'path_error',
				'path_error_call',
				'path_error_not_exists',
				'path_error_invalid_definition',
			]);
		}

		/**
		 * @param bool|true $allowed
		 * @return $this
		 */
		public function allowBadMethodCall($allowed = true){
			$this->allow_bad_method = $allowed;
			return $this;
		}

		/**
		 * @param bool|true $allowed
		 * @return $this
		 */
		public function allowNotFounds($allowed = true){
			$this->allow_not_found = $allowed;
			return $this;
		}

		/**
		 * @param bool|true $allowed
		 * @return $this
		 */
		public function allowInvalidQuery($allowed = true){
			$this->allow_invalid_query = $allowed;
			return $this;
		}

		/**
		 * @param null $defaultValue
		 * @return $this
		 */
		public function setDefaultAllowValue($defaultValue = null){
			$this->default_allow_value = $defaultValue;
			return $this;
		}

		/**
		 * For debugging or trace calls|getter
		 * @param bool|true $traceOn
		 * @param bool $reset
		 * @return $this
		 */
		public function setTrace($traceOn = true, $reset = false){
			$traceOn = boolval($traceOn);
			if($this->trace_on !== $traceOn){
				$this->trace_on = $traceOn;
			}
			if($reset){
				$this->success_trace = [];
				$this->failure_trace = [];
			}
			return $this;
		}


		/**
		 * @param $query
		 * @param $result
		 * @return $this
		 */
		protected function fixSuccessTrace($query, $result = null){
			if($this->trace_on && $query){
				if(!isset($this->success_trace[$query])){
					$this->success_trace[$query] = [];
				}
				$this->success_trace[$query][microtime(true)] = $result;
			}
			return $this;
		}

		/**
		 * @param $query
		 * @param $error
		 * @return $this
		 */
		protected function fixFailureTrace($query,$error){
			if($this->trace_on && $query){
				if(!isset($this->failure_trace[$query])){
					$this->failure_trace[$query] = [];
				}
				$this->failure_trace[$query][microtime(true)] = $error;
			}
			return $this;
		}

		/**
		 * @return array
		 */
		public function getPathTrace(){

			$a = [];

			foreach($this->failure_trace as $definition => $trace){
				foreach($trace as $time => $q){
					/** @var Query $q */
					$a[] = [
						'time'                  => $time,
						'query'                 => $definition,
						'result'                => $this->default_allow_value,
						'failure'               => $q->getType(),
						'failure_path'          => $q->getErrorPath(),
						'failure_call'          => $q->isFunctionErrorPath(),
						'failure_call_getter'   => $q->getCode()===1,
					];
				}
			}
			foreach($this->success_trace as $definition => $trace){
				foreach($trace as $time => $q){
					$a[] = [
						'time'                  => $time,
						'query'                 => $definition,
						'result'                => $q,
						'failure'               => false,
						'failure_path'          => null,
						'failure_call'          => null,
						'failure_call_getter'   => null,
					];
				}
			}


			usort($a,function($a, $b){
				if ($a['time'] == $b['time']) {
					return 0;
				}
				return ($a['time'] < $b['time']) ? -1 : 1;
			});

			return $a;

		}

		/**
		 * @return Query
		 */
		public function getPathFailureTrace(){
			return $this->failure_trace;
		}

		/**
		 * @return mixed
		 */
		public function getPathSuccessTrace(){
			return $this->success_trace;
		}

		/**
		 * @return bool
		 */
		public function getConditionTrace(){
			return $this->condition_trace;
		}

		/**
		 * @param $condition
		 * @param null $result
		 * @param null $error
		 * @return $this
		 */
		protected function fixConditionTrace($condition, $result = null, $error = null){
			if($this->trace_on && $condition && !isset($this->condition_trace[$condition])){
				$this->condition_trace[$condition] = [$result,$error];
			}
			return $this;
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
		 *
		 */
		public function getAllowedOperators(){
			return array_merge($this->operators, Operator::getAllowedOperators());
		}


		/**
		 * @return string
		 */
		protected function getConditionRegex(){
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

		protected function getFunctionRegex(){
			if(!$this->function_regex){
				$delimiter = preg_quote($this->path_delimiter,'@');
				$this->function_regex = '@(\w+['.$delimiter.'\w\d]+)\(([^)]+)\)$@';
			}
			return $this->function_regex;
		}

		/**
		 * @param $condition
		 * @param Context $context
		 * @return bool
		 * @throws Exception
		 */
		public function check($condition, Context $context){
			$this->current_context = $context;
			$definition = trim($condition);
			$condition = $this->_parseCondition($condition);
			if($condition){
				list($left,$operator,$right) = $condition;
				$left       = $this->_get($left);
				$right      = $this->_get($right);
				$condition  = Condition::getCondition($left,$operator,$right);
				$result     = $condition->execute();
				$this->invokeEvent('condition_matched',$definition,$result,$this);
				$this->fixConditionTrace($definition,!!$result,false);
			}else{
				$this->invokeEvent('condition_error',$definition);
				$this->fixConditionTrace($definition,null,'Bad condition');
				$this->current_context = null;
				throw new ResolveException();
			}
			$this->current_context = null;
			return $result;
		}

		/**
		 * @param $condition_definition
		 * @return array|bool
		 */
		protected function _parseCondition($condition_definition){
			if(preg_match_all($this->getConditionRegex(),$condition_definition,$m)){
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
				try{
					$query_definition = trim($query_definition,'[]');
					if(preg_match($this->getFunctionRegex(),$query_definition,$matches)){
						$arguments = explode(',',$matches[2]);
						foreach($arguments as & $arg){
							$arg = trim($arg);
							if(substr($arg,0,1)==='[' && substr($arg,-1)===']'){
								$arg = $this->_get($arg);
							}
						}
						$result = $this->_parseSimpleValue($this->_execute($matches[1],$arguments));
						$this->invokeEvent('path_success_call',$query_definition,$result,$this);
					}else{
						$result = $this->_parseSimpleValue($this->_query($query_definition));
						$this->invokeEvent('path_success_query',$query_definition,$result,$this);
					}
					$this->invokeEvent('path_success',$query_definition,$result,$this);
					$this->fixSuccessTrace($query_definition,$result);
					return $result;
				}catch(BadMethodCall $e){
					$this->invokeEvent('path_error',$e,$this);
					$this->invokeEvent('path_error_call',$e,$this);
					$this->fixFailureTrace($query_definition,$e);
					if(!$this->allow_bad_method     && $e instanceof BadMethodCall){
						throw $e;
					}
					return $this->default_allow_value;
				}catch(PathNotFound $e){
					$this->invokeEvent('path_error',$e,$this);
					$this->invokeEvent('path_error_not_exists',$e,$this);
					$this->fixFailureTrace($query_definition,$e);
					if(!$this->allow_not_found && $e instanceof PathNotFound){
						throw $e;
					}
					return $this->default_allow_value;
				}catch(InvalidQuery $e){
					$this->invokeEvent('path_error',$e,$this);
					$this->invokeEvent('path_error_invalid',$e,$this);
					$this->fixFailureTrace($query_definition,$e);
					if(!$this->allow_invalid_query  && $e instanceof InvalidQuery){
						throw $e;
					}
					return $this->default_allow_value;
				}
			}else{
				return $this->_parseSimpleValue($query_definition);
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
				if(in_array($value,['TRUE','FALSE','NULL'])){
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
		 * @param $path
		 * @return mixed
		 * @throws BadMethodCall
		 * @throws PathNotFound
		 */
		protected function _query($path){
			$oPath = $path;
			$path = explode($this->path_delimiter,$path);
			$current = $this->current_context;
			$currentPath = '';
			while(($chunk = array_shift($path))!==null){

				if(($iq = strstr($chunk,$this->type_delimiter))!==false){
					$chunk  = strstr($chunk,$this->type_delimiter,true);
					$iq     = String::trimWordsLeft($iq,$this->type_delimiter);
				}else{
					$iq = null;
				}

				$currentPath.= $chunk . ($path?$this->path_delimiter:'');
				if($current instanceof Context\ISubstitute){
					$current = $current->getValue();
				}
				if(is_array($current)){
					if(!isset($current[$chunk])){
						throw new PathNotFound($oPath,$currentPath);
					}
					$current = $current[$chunk];
				}elseif(is_object($current)){
					if(isset($current->{$chunk})){
						$current = $current->{$chunk};
					}elseif(method_exists($current,'get'.$chunk)){
						$current = @call_user_func([$current,'get'.$chunk]);
						$e = error_get_last();
						if($e && !$current){
							throw new BadMethodCall($oPath,$currentPath,[],$e['message'],1);
						}
					}elseif(preg_match('@([\w]+[\w\d]+)\((.*)\)@m',$chunk,$m)){
						$methodName = $m[1];
						$args = preg_split('@,\s*@',$m[2]);
						if(method_exists($chunk,$methodName)){
							$current = @call_user_func_array([$current,$methodName],$args);
							$e = error_get_last();
							if($e && !$current){
								throw new BadMethodCall($oPath,$currentPath,$args,$e['message']);
							}
						}else{
							throw new PathNotFound($oPath,$currentPath,true);
						}
					}else{
						throw new PathNotFound($oPath,$currentPath);
					}
				}else{
					throw new PathNotFound($oPath,$currentPath);
				}
				if($iq){
					$current = $this->getInfoFor($current,$iq);
					break;
				}
			}
			return $current;
		}

		/**
		 * @param $methodPath
		 * @param $arguments
		 * @return mixed
		 * @throws BadMethodCall
		 * @throws PathNotFound
		 */
		protected function _execute($methodPath,$arguments){
			$oPath = $methodPath;
			$path = explode($this->path_delimiter,$methodPath);
			$currentPath = '';
			$current = $this->current_context;
			while(($chunk = array_shift($path))!==null){

				if(($iq = strstr($chunk,$this->type_delimiter))!==false){
					$chunk  = strstr($chunk,$this->type_delimiter,true);
					$iq     = String::trimWordsLeft($iq,$this->type_delimiter);
				}else{
					$iq = null;
				}

				$currentPath.= $chunk . ($path?$this->path_delimiter:'');
				if($current instanceof Context\ISubstitute){
					$current = $current->getValue();
				}
				if(is_array($current)){
					if(!isset($current[$chunk])){
						throw new PathNotFound($oPath, $currentPath);
					}
					$current = $current[$chunk];
				}elseif(is_object($current)){
					if(count($path) === 1){
						$methodName = $chunk;
						break;
					}else{
						if(isset($current->{$chunk})){
							$current = $current->{$chunk};
						}elseif(method_exists($current, 'get' . $chunk)){
							$current = @call_user_func([$current, 'get' . $chunk]);
							$e = error_get_last();
							if($e && !$current){
								throw new BadMethodCall($oPath, $currentPath, [], $e['message'], 1);
							}
						}elseif(preg_match('@([\w]+[\w\d]+)\((.*)\)@m', $chunk, $m)){
							$methodName = $m[1];
							$args = preg_split('@,\s*@', $m[2]);
							if(method_exists($chunk, $methodName)){
								$current = @call_user_func_array([$current, $methodName], $args);
								$e = error_get_last();
								if($e && !$current){
									throw new BadMethodCall($oPath, $currentPath, $args, $e['message']);
								}
							}else{
								throw new PathNotFound($oPath, $currentPath, true);
							}
						}else{
							throw new PathNotFound($oPath, $currentPath);
						}
					}
				}else{
					throw new PathNotFound($oPath, $currentPath);
				}

				if($iq){
					$current = $this->getInfoFor($current,$iq);
					break;
				}

			}
			if(isset($methodName)){
				if(is_object($current) && method_exists($current,$methodName)){
					$current = @call_user_func_array([$current,$methodName],$arguments);
					$e = error_get_last();
					if($e && !$current){
						throw new BadMethodCall($oPath,$currentPath,$arguments,$e['message']);
					}
					return $current;
				}else{
					throw new PathNotFound($oPath,$currentPath,true);
				}
			}else{
				return $current;
			}
		}

		/**
		 * @param $current
		 * @param $chunk
		 * @return int|null|string
		 */

		protected function getInfoFor($current, $chunk){
			return Context\Substitute::getInfoFor($chunk,$current);
		}

	}
}

