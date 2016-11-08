<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 14.02.2016
 * Time: 18:55
 */
namespace Jungle\User\AccessControl\Matchable\Aggregator {

	use Jungle\User\AccessControl\Matchable\Aggregator;
	use Jungle\User\AccessControl\Matchable\Matchable;
	use Jungle\User\AccessControl\Matchable\Rule;

	/**
	 * Class Policy
	 * @package Jungle\User\AccessControl
	 */
	class Policy extends Aggregator{

		/**
		 * @param \Jungle\User\AccessControl\Matchable\Matchable $matchable
		 * @param bool $applied
		 * @return $this
		 */
		public function addChild(Matchable $matchable, $applied = false){
			$this->children[] = $matchable;
			if(!$applied && !$matchable instanceof Rule){
				$matchable->setParent($this, true);
			}
			return $this;
		}

		/**
		 * @param \Jungle\User\AccessControl\Matchable\Matchable $matchable
		 * @param bool|false $applied
		 * @return $this
		 */
		public function removeChild(Matchable $matchable, $applied = false){
			$i = array_search($matchable, $this->children, true);
			if($i !== false){
				array_splice($this->children, $i, 1);
				if(!$applied && !$matchable instanceof Rule){
					$matchable->setParent(null,true, true);
				}
			}
			return $this;

		}



	}
}

