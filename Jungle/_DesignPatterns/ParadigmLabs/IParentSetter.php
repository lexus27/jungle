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
	 * Interface IParentSetter
	 * @package Jungle\_DesignPatterns\ParadigmLabs
	 */
	interface IParentSetter{

		/**
		 * @param IContainer $parent
		 * @return $this
		 */
		function setParent(IContainer $parent);

	}
}

