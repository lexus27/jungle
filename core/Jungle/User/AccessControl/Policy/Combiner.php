<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 14.02.2016
 * Time: 18:56
 */
namespace Jungle\User\AccessControl\Policy {

	use Jungle\User\AccessControl\Context;
	use Jungle\User\AccessControl\Policy;

	/**
	 * Class Combiner
	 * @package Jungle\User\AccessControl
	 */
	abstract class Combiner{

		protected static $default_compliant = false;

		/** @var  string|bool Result effect if compliant is true */
		protected $effect;

		/** @var  bool */
		protected $compliant = false;

		/** @var  MatchResult */
		protected $result;

		/** @var  MatchResult[]  */
		protected $results = [];

		/** @var  bool  */
		protected $stop = false;


		/**
		 * @param $effect
		 * @return $this
		 */
		public function begin($effect){
			$this->effect           = $effect;
			$this->results          = [];
			$this->result           = null;
			$this->stop             = false;
			$this->compliant        = static::$default_compliant;
			return $this;
		}

		/**
		 * @param MatchResult $result
		 * @return bool false === остановка перебора правил и отдача результата
		 */
		final public function check(MatchResult $result){
			if($this->beforeCheck($result)!==false){
				if($result->isDeny()){
					$this->onApplicable();
					$this->onDeny();
				}elseif($result->isPermit()){
					$this->onApplicable();
					$this->onPermit();
				}elseif($result->isIndeterminate()){
					$this->onIndeterminate();
				}elseif($result->isNotApplicable()){
					$this->onNotApplicable();
				}
				$this->afterCheck();
			}
			if($this->stop){
				return false;
			}
			return true;
		}

		/**
		 * @return bool|string
		 */
		public function isCompliant(){
			return $this->compliant;
		}

		/**
		 * @return null|bool|string
		 */
		public function getEffect(){
			return $this->isCompliant()?$this->effect:Rule::NOT_APPLICABLE;
		}


		/**
		 * @return MatchResult[]
		 */
		public function getResults(){
			return $this->results;
		}


		/**
		 * @param null $compliant
		 * @param null $delegateEffect
		 * @return $this
		 */
		protected function earlyMatched($compliant = null, $delegateEffect = null){
			$this->stop = true;
			if($compliant!==null)       $this->compliant    = boolval($compliant);
			if($delegateEffect!==null)  $this->effect       = $delegateEffect;
			return $this;
		}

		/**
		 * @return bool
		 */
		public function hasIndeterminate(){
			return $this->_inResults(Rule::INDETERMINATE);
		}

		/**
		 * @return bool
		 */
		public function hasPermit(){
			return $this->_inResults(Rule::PERMIT);
		}

		/**
		 * @return bool
		 */
		public function hasDeny(){
			return $this->_inResults(Rule::DENY);
		}

		/**
		 * @return bool
		 */
		public function hasNotApplicable(){
			return $this->_inResults(Rule::NOT_APPLICABLE);
		}

		/**
		 * @param $result
		 * @return bool
		 */
		protected function _inResults($result){
			if(!is_array($result))$result = [$result];
			foreach($this->results as $r){
				if(in_array($r->getResult(),$result,true)){
					return true;
				}
			}
			return false;
		}

		/**
		 * @param MatchResult $result
		 */
		protected function beforeCheck(MatchResult $result){
			$this->results[]    = $result;
			$this->result       = $result;
		}

		/**
		 * @After-check if before check !== false
		 */
		protected function afterCheck(){}

		/**
		 * @Check if before check !== false
		 */
		protected function onIndeterminate(){}

		/**
		 * @Check if before check !== false
		 */
		protected function onNotApplicable(){}

		/**
		 * @Check if before check !== false
		 */
		protected function onApplicable(){}

		/**
		 * @Check if before check !== false
		 */
		protected function onPermit(){}

		/**
		 * @Check if before check !== false
		 */
		protected function onDeny(){}


		/**
		 * @var Combiner[]
		 */
		protected static $base_combiners = [];

		/**
		 * @param $base_key
		 * @return Combiner|string
		 */
		public static function get($base_key){
			if($base_key instanceof Combiner) return $base_key;

			static $initialized = false;
			if(!$initialized){
				self::$base_combiners = [
					'effect_same_only'          => new Policy\Combiner\EffectSameOnly(),
					'effect_same_soft'          => new Policy\Combiner\EffectSameSoft(),
					'first_applicable_delegate' => new Policy\Combiner\FirstApplicableDelegate(),
				];$initialized = true;
			}
			$base_key = strtolower($base_key);
			if(isset(self::$base_combiners[$base_key])){
				return self::$base_combiners[$base_key];
			}else{
				return null;
			}
		}

	}
}

