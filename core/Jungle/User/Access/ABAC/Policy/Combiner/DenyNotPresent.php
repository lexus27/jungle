<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 15.02.2016
 * Time: 21:02
 */
namespace Jungle\User\Access\ABAC\Policy\Combiner {

	use Jungle\User\Access\ABAC\Policy\Combiner;

	/**
	 * Class DenyNotPresent
	 * @package Jungle\User\Access\ABAC\Policy\Combiner\RuleCombineAlgorithm
	 */
	class DenyNotPresent extends Combiner{

		/**
		 *
		 */
		public function onDeny(){
			$this->earlyMatched(false);
		}

		/**
		 * @return bool
		 */
		public function isCompliant(){
			return $this->hasPermit();
		}

	}
}

