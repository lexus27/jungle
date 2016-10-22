<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 20:29
 */
namespace Jungle\Util\Smart\Keyword {

	use Jungle\Util\Value\Massive;

	/**
	 * Class Manager
	 * @package Jungle\Util\Smart\Keyword
	 */
	class Manager{

		use \Jungle\Util\PropContainer\PropContainerOptionTrait;

		/** @var array */
		protected $pools = [];

		/**
		 * @param Pool $manager
		 * @param bool $appliedIn
		 * @return $this
		 */
		public function addPool(Pool $manager,$appliedIn = false){
			if($this->getPool($manager->getAlias())){
				throw new \LogicException('Already exists manager with alias'.$manager->getAlias());
			}
			if($this->searchPool($manager)===false){
				$this->pools[] = $manager;
				if(!$appliedIn){
					$manager->setManager($this,true);
				}
			}
			return $this;
		}

		/**
		 * @param $alias
		 * @return Pool
		 */
		public function getPool($alias){
			return Massive::getNamed($this->pools,$alias,'strcasecmp');
		}

		/**
		 * @param Pool $manager
		 * @return bool|int
		 */
		public function searchPool(Pool $manager){
			return array_search($manager,$this->pools,true);
		}

		/**
		 * @param Pool $manager
		 * @param bool $appliedIn
		 * @return $this
		 */
		public function removePool(Pool $manager,$appliedIn =false){
			if(($i = $this->searchPool($manager))!==false){
				array_splice($this->pools,$i,1);
				if(!$appliedIn){
					$manager->setManager(null,true,true);
				}
			}
			return $this;
		}

	}
}

