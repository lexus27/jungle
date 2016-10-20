<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.10.2016
 * Time: 20:03
 */
namespace Jungle\Util\Communication\ApiInteractingStream {

	use Jungle\Util\Communication\ApiInteracting\ActionInterface;
	use Jungle\Util\Communication\ApiInteracting\Combination as MainCombination;

	/**
	 * Class Action
	 * @package Jungle\Util\Communication\ApiInteractingStream
	 */
	class Action implements ActionInterface{

		/** @var  string|callable */
		protected $definition;

		/** @var  callable */
		protected $validator;

		/** @var Api */
		public $api;

		/** @var  Process */
		public $process;

		/** @var  Combination */
		public $combination;

		/**
		 * Action constructor.
		 * @param Api $api
		 * @param $definition
		 * @param $validator
		 */
		public function __construct(Api $api, $definition, callable $validator = null){
			$this->definition = $definition;
			$this->validator = $validator;
			$this->api = $api;
		}

		/**
		 * @param array $params
		 * @param MainCombination $combination
		 * @return void
		 * @throws \Exception
		 */
		public function execute(array $params, MainCombination $combination){
			$this->combination = $combination;
			try{
				if(!$combination instanceof Combination){
					throw new \InvalidArgumentException('Argument $combination must be instance of stream specify Combination');
				}
				$process = $this->process = $this->createProcess();
				$stream = $combination->getStream();
				if($this->definition){
					$command = $this->resolveCommand();
					$this->api->send($stream,$command);
					$process->setCommand($command);
				}
				$response = $this->api->read($stream);
				$process->setCode($this->api->code($response));
				$process->setResult($response);
				$combination->getCollector()->push($process);
				$this->check();
			}finally{
				$this->process = null;
				$this->combination = null;
			}
		}
		/**
		 * @return Process
		 */
		protected function createProcess(){
			return new Process($this);
		}
		/**
		 * @return string
		 */
		protected function resolveCommand(){
			$definition = $this->definition;
			if(is_callable($definition)){
				return call_user_func($definition, $this);
			}else{
				$placer = $this->api->getReplacer();
				return $placer->replace($definition,function($placeholder){
					if(!isset($this->process->{$placeholder})){
						throw new \InvalidArgumentException("Action {$this->combination->key()} require param {$placeholder}");
					}
					return $this->process->{$placeholder};
				});
			}
		}

		protected function check(){
			$this->api->validateProcess($this->process);
			if($this->validator){
				call_user_func($this->validator, $this);
			}
		}

	}
}

