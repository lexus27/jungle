<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 14.02.2016
 * Time: 21:58
 */
namespace Jungle\User\AccessControl {

	use Jungle\User\AccessControl\Policy\Combiner;
	use Jungle\User\AccessControl\Policy\MatchResult;
	use Jungle\User\AccessControl\Policy\PolicyGroup;
	use Jungle\User\AccessControl\Policy\Target;

	/**
	 *
	 * ABAC Политика
	 *
	 * Class Policy
	 * @package Jungle\User\AccessControl
	 */
	abstract class Policy extends Matchable{

		/** @var  PolicyGroup|null */
		protected $parent;

		/** @var  Combiner */
		protected $combiner;

		/** @var bool */
		protected $effect = null;

		/**
		 * @param $name
		 */
		public function __construct($name){
			parent::__construct();
			$this->name = $name;
		}
		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			$this->name = $name;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getName(){
			return $this->name;
		}

		/**
		 * @param PolicyGroup $group
		 * @return $this
		 */
		public function setParent(PolicyGroup $group = null){
			if($this->parent !== $group){
				$this->parent = $group;
				if($group)$group->addPolicy($this);
			}
			return $this;
		}

		/**
		 * @return PolicyGroup|null
		 */
		public function getParent(){
			return $this->parent;
		}

		/**
		 * @param Target $target
		 * @return $this
		 */
		public function setTarget(Target $target){
			$this->target = $target;
			return $this;
		}

		/**
		 * @return Target
		 */
		public function getTarget(){
			return $this->target;
		}

		/**
		 * @return bool
		 */
		public function getEffect(){
			return $this->effect;
		}

		/**
		 * @param string|Combiner $combiner
		 * @return $this
		 */
		public function setCombiner($combiner){
			$this->combiner = $combiner;
			return $this;
		}

		/**
		 * @return string|Combiner
		 */
		public function getCombiner(){
			return $this->combiner;
		}

		/**
		 * @param Context $context
		 * @return MatchResult
		 */
		public function match(Context $context){
			$manager = $context->getManager();
			$result = new MatchResult($this);
			if($this->target && !$this->target->isApplicable($context)){
				$result->setResult(self::NOT_APPLICABLE);
				$this->invokeEvent('match',$this,$context,$result,true);
				return $result;
			}
			$effect = $this->getEffect();
			if($effect===null){
				$effect = $manager->getBasedEffect();
			}
			$contains = $this->getContains();
			if($contains){
				try{
					$combiner = $manager->requireCombiner($this->getCombiner())->begin($effect);
					foreach($contains as $contain){
						$containResult = $contain->match($context);
						$result->addChild($containResult);
						if($combiner->check($containResult)===false){
							$containResult->setStopped(true);
							$this->invokeEvent('match_contain_check_stop',$this,$containResult,$contain,$context);
							$this->invokeEvent('match_contain_check',$this,$containResult,$contain,$context,true);
							break;
						}else{
							$this->invokeEvent('match_contain_check',$this,$containResult,$contain,$context,false);
						}
					}
					$result->setResult($combiner->getEffect());
				}catch(Exception $e){
					$result->setIndeterminate($e->getMessage());
				}
			}else{
				$result->setResult($effect);
			}

			$this->invokeEvent('match',$this,$result,$context);
			return $result;
		}

		/**
		 * @return Matchable[]
		 */
		abstract public function getContains();


		/**
		 * @return string
		 */
		public function getHierarchyName(){
			return ($this->parent?$this->parent->getHierarchyName() . '::':'') . $this->name;
		}


	}
}

