<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 18.02.2016
 * Time: 23:21
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\Collections\AdditionalTools\Sorter {

	/**
	 * Interface ISorterInner
	 * @package Jungle\_DesignPatterns\ParadigmLabs\Collections\AdditionalTools\Cmp
	 */
	interface ISorterInner{

		/**
		 * @param array|string $column
		 * @return $this
		 */
		function setOrderBy($column);

	}
}

