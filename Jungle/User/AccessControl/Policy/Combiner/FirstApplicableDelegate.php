<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 15.02.2016
 * Time: 20:47
 */
namespace Jungle\User\AccessControl\Policy\Combiner {

	use Jungle\User\AccessControl\Policy\Combiner;

	/**
	 * Class FirstApplicableDelegate
	 * @package Jungle\User\AccessControl\Policy\Combiner
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

