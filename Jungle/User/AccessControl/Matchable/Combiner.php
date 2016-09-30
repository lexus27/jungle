<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 14.02.2016
 * Time: 18:56
 */
namespace Jungle\User\AccessControl\Matchable {

	use Jungle\User\AccessControl\Context;
	use Jungle\User\AccessControl\ContextInterface;
	use Jungle\User\AccessControl\Matchable;
	use Jungle\User\AccessControl\Matchable\Aggregator;

	/**
	 * Class Combiner
	 * @package Jungle\User\AccessControl
	 */
	class Combiner{

		/** @var  string  */
		protected $default_effect = Matchable::NOT_APPLICABLE;


		/** @var  Result */
		protected $result;

		/** @var  array  */
		protected $history = [];

		/** @var  Result[]  */
		protected $history_results = [];


		/** @var  string */
		protected $same_effect;

		/** @var  string */
		protected $fixed_effect;

		/** @var  string */
		protected $current_effect;

		/** @var  Result */
		protected $current_result;

		/** @var int  */
		protected $current_iteration;


		/** @var bool  */
		protected $early = false;

		/** @var array  */
		protected $config = [];


		/**
		 * Combiner constructor.
		 * @param array|null $config
		 */
		public function __construct(array $config = null){
			if($config!==null){
				$this->setConfig($config);
			}
		}

		/**
		 * @param $same
		 * @return $this
		 */
		public function setSame($same){
			$this->same_effect = $same;
			return $this;
		}

		/**
		 * Clone for extending.
		 */
		public function __clone(){}

		/**
		 * Reset.
		 */
		public function reset(){
			$this->result = null;
			$this->history = [];
			$this->history_results = [];
			$this->current_iteration = 0;
			$this->current_effect = null;
			$this->current_result = null;
			$this->fixed_effect = null;
			$this->early = false;
		}


		/**
		 * @param array $config
		 * @param bool|false $overlap
		 * @return $this
		 */
		public function setConfig(array $config, $overlap = false){
			if(!$this->config || !$overlap){
				$this->config = array_replace_recursive([
					'default'           => null,
					'empty'             => null,
					'result'            => null,
					'history'           => false,
					'not_applicable'    => [
						'check'     => null,
						'early'     => null,
						'effect'    => null,
						'history'   => null,
					],
					'applicable'        => [
						'check'     => null,
						'early'     => null,
						'effect'    => null,
						'history'   => null,
					],
					'deny'              => [
						'check'     => null,
						'early'     => null,
						'effect'    => null,
						'history'   => null,
					],
					'permit'            => [
						'check'     => null,
						'early'     => null,
						'effect'    => null,
						'history'   => null,
					],

				],$config);
			}elseif($this->config && $overlap){
				$this->config = array_replace_recursive($this->config, $config);
			}
			return $this;
		}

		/**
		 * @return array
		 */
		public function getHistory(){
			return $this->history;
		}

		/**
		 * @return Result[]
		 */
		public function getHistoryResults(){
			return $this->history_results;
		}

		/**
		 * @return string
		 */
		public function getSameEffect(){
			return $this->same_effect;
		}

		/**
		 * @return string
		 */
		public function getFixedEffect(){
			return $this->fixed_effect;
		}

		/**
		 * @return string
		 */
		public function getCurrentEffect(){
			return $this->current_effect;
		}

		/**
		 * @return Result
		 */
		public function getCurrentResult(){
			return $this->current_result;
		}

		/**
		 * @return int
		 */
		public function getCurrentIteration(){
			return $this->current_iteration;
		}




		/**
		 * @return bool
		 */
		public function onApplicable(){
			return $this->_eachEffect('applicable');
		}

		/**
		 * @return bool
		 */
		public function onNotApplicable(){
			return $this->_eachEffect('not_applicable');
		}

		/**
		 * @return bool
		 */
		public function onDeny(){
			return $this->_eachEffect('deny');
		}

		/**
		 * @return bool
		 */
		public function onPermit(){
			return $this->_eachEffect('permit');
		}

		/**
		 * onEmpty
		 */
		public function onEmpty(){
			if(($effect = $this->config['empty'])!==null){
				$this->fixed_effect = $this->_checkoutEffect($effect);
			}
		}

		/**
		 * @return mixed
		 */
		public function getEffect(){
			if($this->fixed_effect === null && $this->config['default']!==null){
				$default = $this->config['default'];
				$this->fixed_effect = $this->_checkoutEffect($default);
			}
			$result = $this->config['result'];
			if($result!==null){
				if(is_callable($result)){
					$this->fixed_effect = call_user_func($result, $this->history, $this->fixed_effect, $this->same_effect, $this->current_effect);
				}
			}
			return $this->fixed_effect===null?$this->default_effect:$this->fixed_effect;
		}


		/**
		 * @param Result $aggregatorResult
		 * @param \Jungle\User\AccessControl\Matchable\Aggregator $aggregator
		 * @param ContextInterface $context
		 * @return Result
		 */
		public function match(Result $aggregatorResult, Aggregator $aggregator, ContextInterface $context){
			$this->reset();
			$this->result = $aggregatorResult;
			$aggregation = $aggregator->getChildren();
			if($aggregation){
				foreach($aggregation as $i => $matchable){
					$result = $matchable->match($context, $aggregator);
					$aggregatorResult->addChild($result);
					$this->current_iteration    = $i;
					$this->current_effect       = $result->getEffect();
					$this->current_result       = $result;
					switch($this->current_effect){
						case Matchable::PERMIT:
							if(!$this->onApplicable()){
								$this->onPermit();
							}
							break;
						case Matchable::DENY:
							if(!$this->onApplicable()){
								$this->onDeny();
							}
							break;
						case Matchable::NOT_APPLICABLE:
							$this->onNotApplicable();
							break;
						case Matchable::INDETERMINATE:

							break;
					}
					if($this->early){
						$aggregatorResult->setStopped(true);
						break;
					}
				}
			}else{
				$this->onEmpty();
			}
			return $aggregatorResult->setEffect($this->getEffect());
		}

		/**
		 * @return bool
		 */
		public function hasInterrupts(){
			if($this->config['applicable']['early']){
				return true;
			}elseif(is_array($this->config['applicable']['check'])){
				foreach($this->config['applicable']['check'] as $check){
					if(isset($check['early']) && $check['early']){
						return true;
					}
				}
			}
			if($this->config['not_applicable']['early']){
				return true;
			}elseif(is_array($this->config['not_applicable']['check'])){
				foreach($this->config['not_applicable']['check'] as $check){
					if(isset($check['early']) && $check['early']){
						return true;
					}
				}
			}
			if($this->config['permit']['early']){
				return true;
			}elseif(is_array($this->config['permit']['check'])){
				foreach($this->config['permit']['check'] as $check){
					if(isset($check['early']) && $check['early']){
						return true;
					}
				}
			}
			if($this->config['deny']['early']){
				return true;
			}elseif(is_array($this->config['deny']['check'])){
				foreach($this->config['deny']['check'] as $check){
					if(isset($check['early']) && $check['early']){
						return true;
					}
				}
			}
			return false;
		}

		/**
		 * @return Result
		 */
		public function getResult(){
			return $this->result;
		}



		/**
		 * @param $name
		 * @return bool
		 */
		protected function _eachEffect($name){
			$config = $this->config[$name];
			/**
			 * @var mixed $effect
			 * @var mixed $check
			 * @var bool $early
			 * @var bool $history
			 */
			extract($config);

			$historyEnabled = $history!==null?$history:$this->config['history'];
			if($historyEnabled){
				$this->history[$this->current_iteration] = $this->current_effect;
				$this->history_results[$this->current_iteration] = $this->current_result;
			}
			if($check){
				if(is_array($check)){
					return $this->_handleChecks($check);
				}else{
					if($check === '{same}'){
						if($this->current_effect === $this->same_effect){
							if($early){
								$this->early = true;
							}
							if($effect !== null){
								$this->fixed_effect = $this->_checkoutEffect($effect);
							}
						}
					}elseif($check === '{!same}'){
						if($this->current_effect !== $this->same_effect){
							if($early){
								$this->early = true;
							}
							if($effect !== null){
								$this->fixed_effect = $this->_checkoutEffect($effect);
							}
						}
					}else{
						return false;
					}
					return true;
				}
			}elseif($effect !== null){
				if($early){
					$this->early = true;
				}
				$this->fixed_effect = $this->_checkoutEffect($effect);
			}
			return false;
		}

		protected function _handleOneConditionCheck($check){
			$check = array_replace([
				'check' => null,
				'early' => null,
				'effect' => null
			],$check);
			/**
			 * @var mixed $effect
			 * @var mixed $check
			 * @var bool $early
			 * @var bool $history
			 */
			extract($check,EXTR_OVERWRITE);
			if($check === '{same}'){
				if($this->current_effect === $this->same_effect){
					if($early){
						$this->early = true;
					}
					if($effect !== null){
						$this->fixed_effect = $this->_checkoutEffect($effect);
					}
					return true;
				}
			}elseif($check === '{!same}'){
				if($this->current_effect !== $this->same_effect){
					if($early){
						$this->early = true;
					}
					if($effect !== null){
						$this->fixed_effect = $this->_checkoutEffect($effect);
					}
					return true;
				}
			}
			return false;
		}

		/**
		 * @param $checks
		 * @return bool
		 */
		protected function _handleChecks($checks){
			if(is_array($checks)){
				foreach($checks as $condition){
					if($this->_handleOneConditionCheck($condition) === true){
						return true;
					}
				}
			}
			return false;
		}

		/**
		 * @param $effect
		 * @return mixed
		 */
		protected function _checkoutEffect($effect){
			if($effect === '{same}'){
				return $this->same_effect;
			}elseif($effect === '{!same}'){
				if($this->same_effect === Matchable::PERMIT){
					return Matchable::DENY;
				}elseif($this->same_effect === Matchable::DENY){
					return Matchable::PERMIT;
				}else{
					return null;
				}
			}elseif($effect === '{current}'){
				return $this->current_effect;
			}elseif(is_callable($effect)){
				return call_user_func($effect, $this, $this->history, $this->fixed_effect, $this->same_effect, $this->current_effect);
			}else{
				return $effect;
			}
		}


	}
}

