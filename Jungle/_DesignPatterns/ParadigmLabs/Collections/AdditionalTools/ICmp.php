<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 16:44
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\Collections\AdditionalTools {

	/**
	 * Interface ICompareAware
	 * @package Jungle\_DesignPatterns\ParadigmLabs\Collections
	 */
	interface ICmp{

		/**
		 * @see cmp functions
		 * @param mixed $value
		 * @param mixed $value_collated
		 * @return int
		 * ---------------------------------------------------------------------
		 * -1,                  0,                          1
		 * Less than collated,  Equivalent with collated,   Large than collated
		 * ---------------------------------------------------------------------
		 */
		public function compare($value, $value_collated);

	}
}

