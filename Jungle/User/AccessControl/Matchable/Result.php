<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 18.02.2016
 * Time: 4:08
 */
namespace Jungle\User\AccessControl\Matchable {

	use Jungle\User\AccessControl\Context\Context;
	use Jungle\User\AccessControl\Context\ContextInterface;

	/**
	 * Class Result
	 * @package Jungle\User\AccessControl\Matchable\Matchable
	 */
	class Result{

		/** @var  Matchable */
		protected $matchable;

		/** @var  \Jungle\User\AccessControl\Context\ContextInterface */
		protected $context;

		/** @var  mixed */
		protected $matchable_effect;

		/** @var  bool|string */
		protected $effect;

		/** @var bool  */
		protected $missed = false;

		/** @var bool  */
		protected $stopped = false;

		/** @var Result[]  */
		protected $children = [];

		/** @var array  */
		protected $data = [];

		/**
		 * @Constructor
		 * @param Matchable $matchable
		 * @param \Jungle\User\AccessControl\Context\ContextInterface $context
		 * @param $effect
		 */
		public function __construct(Matchable $matchable = null,ContextInterface $context = null, $effect = null){
			if($effect !== null){
				$this->effect = $effect;
			}
			if($context!==null){
				$this->context = $context;
			}
			if($matchable!==null){
				$this->matchable = $matchable;
			}
		}

		/**
		 * @return bool
		 */
		public function isAllowed(){
			return $this->effect === Matchable::PERMIT?true:false;
		}

		/**
		 * @param \Jungle\User\AccessControl\Context\ContextInterface $context
		 * @return $this
		 */
		public function setContext(ContextInterface $context){
			$this->context = $context;
			return $this;
		}

		/**
		 * @return Context
		 */
		public function getContext(){
			return $this->context;
		}


		/**
		 * @param Matchable $matchable
		 * @return $this
		 */
		public function setMatchable(Matchable $matchable){
			$this->matchable = $matchable;
			return $this;
		}

		/**
		 * @return Matchable
		 */
		public function getMatchable(){
			return $this->matchable;
		}

		public function setMatchableEffect($effect){
			$this->matchable_effect = $effect;
			return $this;
		}

		public function getMatchableEffect(){
			return $this->matchable_effect;
		}


		/**
		 * @param bool|true $stopped
		 * @return $this
		 */
		public function setStopped($stopped = true){
			$this->stopped = $stopped;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isStopped(){
			return $this->stopped;
		}

		/**
		 * @param $missed
		 * @return $this
		 */
		public function setMissed($missed = true){
			$this->missed = $missed;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isMissed(){
			return $this->missed;
		}

		/**
		 * @param Result $result
		 * @return $this
		 */
		public function addChild(Result $result){
			$this->children[] = $result;
			return $this;
		}

		/**
		 * @return Result[]
		 */
		public function getChildren(){
			return $this->children;
		}

		/**
		 * @param $effect
		 * @return $this
		 */
		public function setEffect($effect){
			$this->effect = $effect;
			return $this;
		}


		/**
		 * @return string
		 */
		public function getEffect(){
			return $this->effect;
		}


		/**
		 * @param $key
		 * @param $value
		 * @return $this
		 */
		public function setData($key, $value){
			$this->data[$key] = $value;
			return $this;
		}

		/**
		 * @param $key
		 * @param $value
		 * @return $this
		 */
		public function addData($key, $value){
			if(!isset($this->data[$key])){
				$this->data[$key] = [];
			}
			$this->data[$key][] = $value;
			return $this;
		}

		/**
		 * @return array
		 */
		public function getData($key){
			return isset($this->data[$key])?$this->data[$key]:null;
		}





		/**
		 * @return bool
		 */
		public function isPermit(){
			return $this->effect === Matchable::PERMIT;
		}

		/**
		 * @return bool
		 */
		public function isDeny(){
			return $this->effect === Matchable::DENY;
		}

		/**
		 * @return bool
		 */
		public function isApplicable(){
			return $this->effect === Matchable::PERMIT || $this->effect === Matchable::DENY;
		}

		/**
		 * @return bool
		 */
		public function isIndeterminate(){
			return $this->effect === Matchable::INDETERMINATE;
		}

		/**
		 * @return bool
		 */
		public function isNotApplicable(){
			return $this->effect === Matchable::NOT_APPLICABLE;
		}




		/**
		 * @return callable
		 */
		public function getObligation(){
			return $this->matchable->getObligation();
		}

		/**
		 * @return callable
		 */
		public function getAdvice(){
			return $this->matchable->getAdvice();
		}

		/**
		 * @return callable
		 */
		public function getRequirement(){
			return $this->matchable->getRequirement();
		}



		public function __toString(){
			return is_bool($this->effect)?($this->effect?'permit':'deny'):($this->effect?:'null');
		}


	}
}

