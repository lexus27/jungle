<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 16:36
 */
namespace Jungle\_DesignPatterns\ParadigmLabs\Collections\AdditionalTools {

	/**
	 * Источник элементов коллекции, Source Getter
	 *
	 * Interface ICollectionSourceAware
	 * @package Jungle\_DesignPatterns\ParadigmLabs\Collections\AdditionalTools
	 */
	interface ICollectionSourceHolder{

		/**
		 * @return mixed
		 */
		public function getSource();

	}
}

