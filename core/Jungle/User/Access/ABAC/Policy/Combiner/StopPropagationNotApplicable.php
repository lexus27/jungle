<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 15.02.2016
 * Time: 21:04
 */
namespace Jungle\User\Access\ABAC\Policy\Combiner {


	use Jungle\User\Access\ABAC\Policy\Combiner;

	/**
	 * Class StopPropagationNotApplicable
	 * @package Jungle\User\Access\ABAC\Policy\Combiner
	 */
	abstract class StopPropagationNotApplicable extends Combiner{

		/**
		 * @return $this
		 */
		protected function onIndeterminate(){
			return $this->earlyMatched(false);
		}

		/**
		 * @return $this
		 */
		protected function onNotApplicable(){
			return $this->earlyMatched(false);
		}
	}
}

