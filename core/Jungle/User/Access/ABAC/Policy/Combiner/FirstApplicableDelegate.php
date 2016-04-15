<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 15.02.2016
 * Time: 20:47
 */
namespace Jungle\User\Access\ABAC\Policy\Combiner {

	use Jungle\User\Access\ABAC\Policy\Combiner;

	/**
	 * Class FirstApplicableDelegate
	 * @package Jungle\User\Access\ABAC\Policy\Combiner
	 */
	class FirstApplicableDelegate extends Combiner{

		/**
		 *
		 */
		protected function onApplicable(){
			$this->earlyMatched(true,$this->result->getResult());
		}

	}
}

