<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 15.11.2015
 * Time: 2:13
 */
namespace Jungle\_DesignPatterns\ParadigmLabs {

	use Jungle\_DesignPatterns\ParadigmLabs\OneWay\IContainer;

	/**
	 * Interface IParentGetter
	 * @package Jungle\_DesignPatterns\ParadigmLabs
	 */
	interface IParentGetter{

		/**
		 * @return IContainer
		 */
		public function getParent();

	}
}

