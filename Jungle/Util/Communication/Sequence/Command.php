<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 02.10.2016
 * Time: 19:26
 */
namespace Jungle\Util\Communication\Sequence {

	use Jungle\Util\Communication\Sequence;
	use Jungle\Util\Communication\Sequence\Exception\RuleMessage;

	/**
	 * Class Command
	 * @package Jungle\Util\Communication\Sequence
	 */
	class Command implements CommandDefinitionInterface, CommandInterface{

		use CommandTrait;

		/** @var  string */
		protected $definition;

		/** @var  callable|null  */
		protected $aggregator;

		/** @var  array  */
		protected $params = [];

		/** @var  RuleInterface[]  */
		protected $rules = [];


		/**
		 * Command constructor.
		 * @param null $definition
		 */
		public function __construct($definition = null){
			$this->definition = $definition;
		}

		/**
		 * @param $definition
		 * @return mixed
		 */
		public function setDefinition($definition){
			$this->definition = $definition;
		}

		/**
		 * @return mixed
		 */
		public function getDefinition(){
			return $this->definition;
		}

		/**
		 * @return RuleInterface[]
		 */
		public function getRules(){
			return $this->rules;
		}

		/**
		 * @param RuleInterface $rule
		 * @return mixed
		 */
		public function addRule(RuleInterface $rule){
			$this->rules[] = $rule;
		}

		/**
		 * @param callable $aggregator
		 * @return $this
		 */
		public function setAggregator(callable $aggregator){
			$this->aggregator = $aggregator;
			return $this;
		}

		/**
		 * @return callable|null
		 */
		public function getAggregator(){
			return $this->aggregator;
		}

		/**
		 * @param ProcessSequenceInterface $processSequence
		 * @param array $params
		 * @throws Exception
		 * @throws RuleMessage|null
		 */
		protected function _run_one(ProcessSequenceInterface $processSequence, array $params){
			$process = new Process($this);
			$sequence = $processSequence->getSequence();
			try{
				if($this->definition){
					$params = $this->prepareParams($params);
					$process->setParams($params);
					$definition = $this->prepareDefinition($process);
					$process->setCommandText($definition);
					$this->specification->send($sequence, $definition);
				}

				$result = $this->specification->read($sequence);


				$code = $this->specification->recognizeCode($result);
				$process->setResult($result);
				$process->setCode($code);

				$processSequence->addProcess($process);

				if($this->specification->isFatalCode($code)){
					throw new Sequence\Exception\FatalException($process->getResult(), $process->getCode());
				}

				foreach($this->rules as $rule){
					$checking = $rule->check($process, $processSequence);
					if($checking instanceof RuleMessage){
						throw $checking;
					}
				}

				foreach($this->rules as $rule){
					$rule->onNotMessages($process, $processSequence);
				}

			}catch(Exception $e){
				$this->_handleException($e, $process, $processSequence);
			}
		}

		/**
		 * @param ProcessSequenceInterface $processSequence
		 * @param array $params
		 * @return void
		 * @throws Exception
		 */
		public function run(ProcessSequenceInterface $processSequence, array $params){
			if($this->aggregator && $this->definition){
				$aggregation = call_user_func($this->aggregator, $params, $this);
				if(!is_array($aggregation)){
					throw new Exception('Aggregation is not valid returned value, must be each iteration params arrays');
				}
				if(!$aggregation){
					$this->_run_one($processSequence,$params);
				}else{
					foreach($aggregation as $item){
						$this->_run_one($processSequence, $item);
					}
				}
			}else{
				$this->_run_one($processSequence,$params);
			}
		}

		/**
		 * @param array $params
		 * @return string
		 */
		protected function prepareParams(array $params = []){
			return array_replace($this->params,$params);
		}

		/**
		 * @param ProcessInterface $process
		 * @return mixed
		 */
		protected function prepareDefinition(ProcessInterface $process){
			return preg_replace_callback('~\{\{(\w+)\}\}~',function($m) use($process){
				if(!isset($process->{$m[1]})){
					throw new Sequence\Exception\ParamRequired($m[1]);
				}
				$s = $process->{$m[1]};
				if(!is_scalar($s)){
					throw new Sequence\Exception\ParamRequired($m[1],'Is not a scalar');
				}
				return $s;
			},$this->definition);
		}

		/**
		 * @param Exception $e
		 * @param ProcessInterface $process
		 * @param ProcessSequenceInterface $sequence
		 * @return ProcessInterface
		 * @throws Exception
		 */
		protected function _handleException(Exception $e, ProcessInterface $process, ProcessSequenceInterface $sequence){
			throw $e;
		}

		/**
		 * @param array $params
		 * @return $this
		 */
		public function setParams(array $params){
			$this->params = $params;
			return $this;
		}
	}
}

