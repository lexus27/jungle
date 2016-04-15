<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 21.11.2015
 * Time: 5:25
 */
namespace Jungle\Basic\Collection {

	/**
	 * Interface INamedSpecification
	 * @package Jungle\Basic\Collection
	 */
	interface INamedSpecification extends IAccessedByKey{

		/**
		 * @param string $class derived from Jungle\Basic\INamedGetter
		 * @return $this
		 */
		public function setKeyAccessorClass($class = 'Jungle\Basic\INamedGetter');

		/**
		 * @return string
		 */
		public function getKeyAccessorClass();

	}
}

