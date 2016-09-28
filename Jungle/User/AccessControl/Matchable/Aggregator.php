<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 14.02.2016
 * Time: 21:58
 */
namespace Jungle\User\AccessControl\Matchable {

	use Jungle\User\AccessControl\Context;
	use Jungle\User\AccessControl\Exception;
	use Jungle\User\AccessControl\Matchable;

	/**
	 *
	 * ABAC Политика
	 *
	 * Class Policy
	 * @package Jungle\User\AccessControl
	 */
	abstract class Aggregator extends Matchable{

		/** @var  Aggregator|null */
		protected $parent;

		/** @var  Combiner */
		protected $combiner;

		/** @var bool */
		protected $effect = null;

		/** @var  Matchable[]  */
		protected $children = [];

		/**
		 * @param $name
		 */
		public function __construct($name){
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
		 * @return bool
		 */
		public function getEffect(){
			if($this->parent && $this->effect === null){
				return $this->parent->getEffect();
			}
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
		 * @param Aggregator $aggregator
		 * @return \Jungle\User\AccessControl\Matchable\Result
		 * @throws Exception
		 */
		public function match(Context $context, Aggregator $aggregator = null){
			$manager = $context->getManager();
			$result = new Result($this);
			if($this->target && !call_user_func($this->target,$context)){
				$result->setEffect(self::NOT_APPLICABLE);
				$result->setMissed(true);
				return $result;
			}
			$effect = $this->getEffect();
			if($effect===null){
				$effect = $manager->getDefaultEffect();
			}
			$combiner = $manager->getCombiner($this->getCombiner());
			$combiner = clone $combiner;
			$combiner->setSame($effect);
			$combiner->match($result, $this, $context);
			return $result;
		}

		/**
		 * @return Matchable[]
		 */
		public function getChildren(){
			return $this->children;
		}

		/**
		 * @param Matchable $matchable
		 * @param bool $applied
		 * @return $this
		 */
		public function addChild(Matchable $matchable, $applied = false){
			$this->children[] = $matchable;
			if(!$applied){
				$matchable->setParent($this, true);
			}
			return $this;
		}

		/**
		 * @param Matchable $matchable
		 * @param bool|false $applied
		 * @return $this
		 */
		public function removeChild(Matchable $matchable, $applied = false){
			$i = array_search($matchable, $this->children, true);
			if($i !== false){
				array_splice($this->children, $i, 1);
				if(!$applied){
					$matchable->setParent(null,true, true);
				}
			}
			return $this;

		}

	}
}

