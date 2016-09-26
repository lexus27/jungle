<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 18.02.2016
 * Time: 4:08
 */
namespace Jungle\User\AccessControl\Policy {

	use Jungle\User\AccessControl\Matchable;

	/**
	 * Class MatchResult
	 * @package Jungle\User\AccessControl\Policy
	 */
	class MatchResult{

		/** @var  Matchable */
		protected $matchable;

		/** @var  bool|string */
		protected $result;

		/** @var bool  */
		protected $target_compliant = false;

		/** @var   */
		protected $indeterminate_message;

		/** @var   */
		protected $indeterminate_obligation;

		/** @var bool  */
		protected $stopped = false;

		/** @var MatchResult[]  */
		protected $children = [];


		/**
		 * @Constructor
		 * @param $result
		 * @param Matchable $matchable
		 */
		public function __construct(Matchable $matchable = null, $result = null){
			if($result!==null)$this->result = $result;
			if($matchable) $this->matchable = $matchable;
		}

		/**
		 * @param $compliant
		 * @return $this
		 */
		public function setTargetCompliant($compliant){
			$this->target_compliant = $compliant;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function isTargetCompliant(){
			return $this->target_compliant;
		}

		/**
		 * @param MatchResult $result
		 * @return $this
		 */
		public function addChild(MatchResult $result){
			$this->children[] = $result;
			return $this;
		}

		/**
		 * @return MatchResult[]
		 */
		public function getChildren(){
			return $this->children;
		}

		/**
		 * @param $result
		 * @return $this
		 */
		public function setResult($result){
			$this->result = $result;
			return $this;
		}

		/**
		 * @param $message
		 * @param bool|false $obligation
		 */
		public function setIndeterminate($message,$obligation = false){
			$this->result = Matchable::INDETERMINATE;
			$this->indeterminate_message = $message;
			$this->indeterminate_obligation = $obligation;
		}

		/**
		 * @return mixed
		 */
		public function getIndeterminateMessage(){
			return $this->indeterminate_message;
		}

		/**
		 * @return mixed
		 */
		public function isObligationError(){
			return $this->indeterminate_obligation;
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
		 * @return bool
		 */
		public function isPermit(){
			return $this->result === Matchable::PERMIT;
		}

		/**
		 * @return bool
		 */
		public function isDeny(){
			return $this->result === Matchable::DENY;
		}

		/**
		 * @return bool
		 */
		public function isApplicable(){
			return $this->result === Matchable::PERMIT || $this->result === Matchable::DENY;
		}

		/**
		 * @return bool
		 */
		public function isIndeterminate(){
			return $this->result === Matchable::INDETERMINATE;
		}

		/**
		 * @return bool
		 */
		public function isNotApplicable(){
			return $this->result === Matchable::NOT_APPLICABLE;
		}

		/**
		 * @return bool|string
		 */
		public function getResult(){
			return $this->result;
		}

		/**
		 * @return mixed
		 */
		public function getObligation(){
			return $this->matchable->getObligation();
		}

		/**
		 * @return mixed
		 */
		public function getAdvice(){
			return $this->matchable->getAdvice();
		}

		/**
		 * @return mixed
		 */
		public function getRequirements(){
			return $this->matchable->getAdvice();
		}

		/**
		 * @return Matchable
		 */
		public function getMatchable(){
			return $this->matchable;
		}

		public function __toString(){
			return is_bool($this->result)?($this->result?'permit':'deny'):($this->result?:'null');
		}


	}
}

