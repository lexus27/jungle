<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 15.02.2016
 * Time: 21:04
 */
namespace Jungle\User\AccessControl\Policy\Combiner {


	use Jungle\User\AccessControl\Policy\Combiner;

	/**
	 * Class StopPropagationNotApplicable
	 * @package Jungle\User\AccessControl\Policy\Combiner
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

