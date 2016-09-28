<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 18.02.2016
 * Time: 4:08
 */
namespace Jungle\User\AccessControl\Matchable {

	use Jungle\User\AccessControl\Matchable;

	/**
	 * Class Result
	 * @package Jungle\User\AccessControl\Matchable
	 */
	class Result{

		/** @var  Matchable */
		protected $matchable;

		/** @var  bool|string */
		protected $effect;

		/** @var bool  */
		protected $missed = false;

		/** @var bool  */
		protected $stopped = false;

		/** @var Result[]  */
		protected $children = [];


		/**
		 * @Constructor
		 * @param $effect
		 * @param Matchable $matchable
		 */
		public function __construct(Matchable $matchable = null, $effect = null){
			if($effect !== null){
				$this->effect = $effect;
			}
			if($matchable!==null){
				$this->matchable = $matchable;
			}
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

