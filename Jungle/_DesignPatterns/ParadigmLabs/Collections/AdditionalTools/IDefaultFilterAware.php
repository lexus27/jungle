<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 16:41
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\Collections\AdditionalTools {

	/**
	 * Interface IDefaultFilterAware
	 * @package Jungle\_DesignPatterns\ParadigmLabs\Collections\AdditionalTools
	 */
	interface IDefaultFilterAware{

		/**
		 * @see array_filter callback function
		 * @param callable|null $filter
		 * @return $this
		 */
		public function setDefaultFilter(callable $filter = null);

		/**
		 * @return callable|null
		 */
		public function getDefaultFilter();

	}
}

