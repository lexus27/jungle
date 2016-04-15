<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 15.02.2016
 * Time: 23:49
 */
namespace Jungle\User\Access\ABAC\Policy\Combiner {

	use Jungle\User\Access\ABAC\Policy\Combiner;

	/**
	 * Class PermittedOnly
	 * @package Jungle\User\Access\ABAC\Policy\Combiner
	 */
	class PermittedSoft extends StopPropagationNotApplicable{

		/**
		 *
		 */
		protected function onDeny(){
			$this->earlyMatched(false);
		}


	}
}

