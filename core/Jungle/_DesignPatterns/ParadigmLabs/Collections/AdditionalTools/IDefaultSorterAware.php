<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 16:39
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\Collections\AdditionalTools {

	/**
	 * Interface IDefaultSorterAware
	 * @package Jungle\_DesignPatterns\ParadigmLabs\Collections\AdditionalTools
	 */
	interface IDefaultSorterAware{

		/**
		 * @see usort cmp function
		 * @param callable|null $sorter
		 * @return $this
		 */
		public function setDefaultSorter(callable $sorter = null);

		/**
		 * @return callable|null
		 */
		public function getDefaultSorter();
	}
}

