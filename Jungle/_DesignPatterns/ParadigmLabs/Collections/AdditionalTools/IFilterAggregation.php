<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 18.02.2016
 * Time: 22:59
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\Collections\AdditionalTools {

	/**
	 * Interface IFilterAggregation
	 * @package Jungle\_DesignPatterns\ParadigmLabs\Collections\AdditionalTools
	 */
	interface IFilterAggregation{

		/**
		 * @param $alias
		 * @param $filter
		 * @return mixed
		 */
		public function setFilter($alias, $filter);

	}
}

