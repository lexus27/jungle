<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 17:23
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\Collections\AdditionalTools {


	/**
	 * Interface ISorterAggregation
	 * @package Jungle\_DesignPatterns\ParadigmLabs\Collections\AdditionalTools
	 *
	 * Внутреннее расположения коллекции сортировщиков по псевдонимам
	 *
	 */
	interface ISorterAggregation{

		/**
		 * @param $alias
		 * @param $sortManager
		 * @return $this
		 */
		public function setSorter($alias, $sortManager);

	}
}

