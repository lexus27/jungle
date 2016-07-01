<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 25.01.2016
 * Time: 14:59
 */
namespace Jungle\Util\Communication\Stream {

	use Jungle\Util\Communication\Stream;
	use Jungle\Util\Communication\Stream\Command\Rule;
	use Jungle\Util\Communication\Stream\Connection\Socket;

	/**
	 * Class Builder
	 * @package Jungle\Util\Communication\Stream
	 */
	class Builder{

		/** @var Builder */
		protected static $default;

		/**
		 * @return Builder
		 */
		public static function getDefault(){
			if(!self::$default){
				self::$default = new Builder();
			}
			return self::$default;
		}

		/**
		 * @param Builder $builder
		 */
		public static function setDefault(Builder $builder){
			self::$default = $builder;
		}

		/**
		 * @param $d
		 * @return Stream
		 */
		public function buildStream($d){
			if($d instanceof Stream){
				return $d;
			}
			$url        = !is_array($d)?$d:null;
			$connection = Socket::class;
			$timeout    = 20;
			$start      = null;
			if(is_array($d)){
				$url        = isset($d['url'])?$d['url']:$url;
				$connection = isset($d['connection'])?$d['connection']:$connection;
				$timeout    = isset($d['timeout'])?$d['timeout']:$timeout;
				$start      = isset($d['start'])?$d['start']:$start;
			}

			if(!$url){
				throw new \LogicException('Building error because definition is not a valid');
			}

			if($connection){
				$connection = $this->createConnection($connection,$url,$timeout);
			}
			$stream = new Stream($connection);

			if(is_array($start) || $start instanceof Command){
				$stream->setStart($start);
			}
			return $stream;
		}

		/**
		 * @param array $d
		 * @return Rule
		 */
		public function buildRule(array $d){
			$code = isset($d['code'])?$d['code']:null;
			if($code){
				if(is_string($code)){
					if(strpos($code,',')!==false){
						$code = array_filter(array_map('trim',explode(',',$code)));
					}else{
						throw new \InvalidArgumentException('Code definition string is not valid');
					}
				}
				if(is_int($code)){
					$code = [$code];
				}
				if(!is_array($code)){
					throw new \InvalidArgumentException('Code definition string is not valid');
				}
			}else{
				throw new \InvalidArgumentException('Code required');
			}
			$negated        = isset($d['negated'])?$d['negated']:false;
			$behaviour      = isset($d['behaviour'])?$d['behaviour']:null;
			$errorMessage   = isset($d['msg'])?$d['msg']:null;

			$rule = new Rule();
			$rule->setCode($code);
			$rule->setNegated($negated);
			$rule->setBehaviour($behaviour);
			$rule->setErrorMessage($errorMessage);

			return $rule;
		}

		/**
		 * @param $definition
		 * @return \Closure
		 */
		public function buildCodeRecognizer($definition){
			return function($data) use($definition){
				$param  = 'code';
				$type   = 'integer';
				$regex  = null;
				if(is_array($definition)){
					$param  = isset($definition['param'])?$definition['param']:$param;
					$type   = isset($definition['type'])?$definition['type']:$type;
					$regex  = isset($definition['regex'])?$definition['regex']:$regex;
				}else{
					$regex  = $definition;
				}
				if(preg_match($regex,$data,$m)){
					$v = isset($m[$param])?$m[$param]:(isset($m[1])?$m[1]:null);
					settype($v,$type);
					return $v;
				}
				return false;
			};
		}

		/**
		 * @param $definition
		 * @return \Jungle\Util\Communication\Stream\Specification
		 */
		public function buildCodeSpecification($definition){
			if(is_array($definition)){
				return Stream\Specification::fromArray($definition);
			}
			return null;
		}

		/**
		 * @param $className
		 * @param $url
		 * @param int $timeout
		 * @return Connection
		 */
		public function createConnection($className, $url, $timeout = 60){
			if(!class_exists($className)){
				throw new \LogicException("Class '$className' not found!");
			}
			if(!is_a($className,'\Jungle\Communication\Stream\Connection',true)){
				throw new \LogicException("Class '$className' is not a subclass of \\Jungle\\Util\\Communication\\Stream\\Connection");
			}
			/** @var Connection $c */
			$c = new $className();
			$c->setUrl($url);
			$c->setTimeout($timeout);
			return $c;
		}


		/**
		 * @param $definition
		 * @param array|null $defaults
		 * @return Command
		 */
		public function buildCommand($definition,array $defaults = null){
			if($definition instanceof Command){
				return $definition;
			}
			if(!is_array($definition) && !is_string($definition)){
				throw new \InvalidArgumentException('definition must be array or instanceof Command or string command');
			}

			if(!is_array($definition)){
				$definition = [
					'command' => $definition
				];
			}

			if(!$defaults)$defaults = [];
			$ruleDefault = isset($defaults['rule'])?(array)$defaults['rule']:[];
			unset($defaults['rule']);

			if(!isset($definition['command'])){
				$definition['command'] = (isset($definition[0])?$definition[0]:null);
			}
			if(!isset($definition['rules'])){
				$definition['rules'] = (isset($definition[1])?$definition[1]:null);
			}
			if(!isset($definition['modifier'])){
				$definition['modifier'] = (isset($definition[2])?$definition[2]:null);
			}

			foreach($defaults as $k => $d){
				if(!isset($definition[$k]) && !$definition[$k] && $defaults[$k]){
					$definition[$k] = $defaults[$k];
				}
			}

			$c = & $definition['command'];
			$m = & $definition['modifier'];

			$command = new Command();
			$command->setCommand($c);
			$command->setModifier($m);

			$r = isset($definition['rules'])?$definition['rules']:(isset($definition[1])?$definition[1]:null);
			if($r){
				if(is_array($r)){
					if(isset($r[0])){
						foreach($r as & $rule){
							$command->addRule( $this->buildRule(array_merge($ruleDefault,$rule)) ) ;
						}
					}else{
						$command->addRule($this->buildRule(array_merge($ruleDefault,$r)));
					}
				}
			}
			return $command;
		}


		/**
		 * @param array|Command $d
		 * @return Command[]
		 */
		public function buildCommandComposition($d){
			$a = [];
			if(is_array($d) && isset($d['commands'])){
				$defaults = isset($d['defaults'])?$d['defaults']:[];
				$commands = isset($d['commands'])?$d['commands']:[];
				foreach($commands as $c){
					if(isset($c['collection']) && isset($c['handler'])){
						if(is_array($c['collection']) && is_callable($c['handler'])){
							foreach($c['collection'] as $index => $data){
								$command = call_user_func($c['handler'],$index , $data);
								$a[] = $this->buildCommand($command,$defaults);
							}
						}else{
							throw new \InvalidArgumentException(__METHOD__ . ' data collection must be array and callable handler');
						}
					}else{
						$a[] = $this->buildCommand($c,$defaults);
					}
				}
			}elseif(isset($d['collection'])){
				if(is_array($d['collection']) && isset($d['handler']) && is_callable($d['handler'])){
					foreach($d['collection'] as $index => $data){
						$command = call_user_func($d['handler'],$index , $data);
						$a[] = $this->buildCommand($command,null);
					}
				}else{
					throw new \InvalidArgumentException(__METHOD__ . ' data collection must be array and callable handler');
				}
			}else{
				$a[] = $this->buildCommand($d);
			}

			return $a;
		}




	}
}

